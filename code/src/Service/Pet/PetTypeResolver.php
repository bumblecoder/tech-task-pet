<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

use App\Entity\PetType;
use App\Repository\PetTypeRepository;

final readonly class PetTypeResolver
{
    public function __construct(private PetTypeRepository $repo)
    {
    }

    public function byId(string $id): ?PetType
    {
        return $this->repo->find($id);
    }
}
