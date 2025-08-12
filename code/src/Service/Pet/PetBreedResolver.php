<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

use App\Entity\PetBreed;
use App\Repository\PetBreedRepository;

final readonly class PetBreedResolver implements PetBreedResolverInterface
{
    public function __construct(private PetBreedRepository $repo)
    {
    }

    public function byId(string $id): ?PetBreed
    {
        return $this->repo->find($id);
    }
}
