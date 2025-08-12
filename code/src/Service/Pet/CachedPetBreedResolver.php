<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

use App\Entity\PetBreed;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class CachedPetBreedResolver implements PetBreedResolverInterface
{
    public function __construct(
        private PetBreedResolverInterface $inner,
        private CacheInterface $cache,
        private EntityManagerInterface $em,
    ) {}

    /**
     * @throws InvalidArgumentException
     * @throws ORMException
     */
    public function byId(string $id): ?PetBreed
    {
        $exists = $this->cache->get('breed_exists_'.$id, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);
            return $this->inner->byId($id) !== null;
        });

        if (!$exists) {
            return null;
        }

        $uuid = Uuid::fromString($id);
        /** @var PetBreed $ref */
        $ref =  $this->em->getReference(PetBreed::class, $uuid);

        return $ref;
    }
}
