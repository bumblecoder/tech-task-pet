<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

use App\Entity\PetBreed;
use Psr\Cache\InvalidArgumentException;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final readonly class CachedPetBreedResolver implements PetBreedResolverInterface
{
    public function __construct(
        private PetBreedResolverInterface $inner,
        private CacheInterface $cache,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function byId(string $id): ?PetBreed
    {
        return $this->cache->get('breed_' . $id, function (ItemInterface $item) use ($id) {
            $item->expiresAfter(3600);

            return $this->inner->byId($id);
        });
    }
}
