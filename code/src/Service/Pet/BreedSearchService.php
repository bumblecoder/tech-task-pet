<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

use App\Entity\PetBreed;
use App\Entity\PetType;
use App\Repository\PetBreedRepository;

final readonly class BreedSearchService
{
    public function __construct(private PetBreedRepository $repo)
    {
    }

    public function search(PetType $type, string $term): array
    {
        $breeds = $this->repo->findBySearch($type, $term) ?? [];

        $result = [];

        foreach ($breeds as $b) {
            if ($b instanceof PetBreed) {
                $result[] = ['id' => (string) $b->getId(), 'name' => $b->getName()];
            }
        }

        return $result;
    }
}
