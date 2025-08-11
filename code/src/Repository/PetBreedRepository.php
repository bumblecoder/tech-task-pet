<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Repository;

use App\Entity\PetBreed;
use App\Entity\PetType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PetBreed>
 */
class PetBreedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PetBreed::class);
    }

    public function findBySearch(PetType $type, string $search): array
    {
        $qb = $this->createQueryBuilder('b')
            ->where('b.type = :type')
            ->andWhere('LOWER(b.name) LIKE :search')
            ->setParameter('type', $type->getId(), 'uuid')
            ->setParameter('search', '%' . strtolower($search) . '%')
            ->setMaxResults(10);

        return $qb->getQuery()->getResult();
    }
}
