<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

use App\Entity\Pet;

class UnknownDobHandler implements PetDateHandlerInterface
{
    public function apply(Pet $pet, DateParts $parts): void
    {
        $pet->setDateOfBirth(null);
    }

    public function supports(bool $dobKnown): bool
    {
        return false === $dobKnown;
    }
}
