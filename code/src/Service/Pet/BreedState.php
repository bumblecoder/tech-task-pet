<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

final class BreedState
{
    public ?string $breedId = null;
    public string $breedSearch = '';
    /** @var array<int, array{name:string,id:string}> */
    public array $filteredBreeds = [];
    public ?string $breedChoice = null;
    public ?string $breedMixText = null;
}
