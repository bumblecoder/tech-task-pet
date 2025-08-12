<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

final class BreedStateFactory
{
    public function reset(): BreedState
    {
        return new BreedState();
    }
}
