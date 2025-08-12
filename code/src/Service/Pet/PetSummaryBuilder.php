<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

use App\Entity\Pet;

final readonly class PetSummaryBuilder
{
    public function build(Pet $pet): array
    {
        return [
            'name' => $pet->getName() ?: '—',
            'type' => $pet->getType()?->getName() ?: '—',
            'breed' => $pet->getBreed()?->getName()
                ?? ($pet->getBreedOther() ? 'Mix' : 'Unknown'),
            'mix' => $pet->getBreedOther() ? 'Yes' : 'No',
            'mixText' => $pet->getBreedOther() ?: null,
            'gender' => $pet->getSex()?->name ?? '—',
            'ageOrDob' => $pet->getDateOfBirth()
                ? $pet->getDateOfBirth()->format('Y-m-d')
                : ($pet->getApproximateAge() ? ('~' . $pet->getApproximateAge() . ' years') : '—'),
            'dangerous' => $pet->isDangerous(),
        ];
    }
}
