<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Unit\Service\Pet;

use App\Service\Pet\BreedStateFactory;
use PHPUnit\Framework\TestCase;

final class BreedStateFactoryTest extends TestCase
{
    public function testResetReturnsFreshStateWithDefaults(): void
    {
        $factory = new BreedStateFactory();
        $state = $factory->reset();

        self::assertNull($state->breedId);
        self::assertSame('', $state->breedSearch);
        self::assertSame([], $state->filteredBreeds);
        self::assertNull($state->breedChoice);
        self::assertNull($state->breedMixText);
    }

    public function testResetIsIdempotentAndReturnsNewInstanceEachTime(): void
    {
        $factory = new BreedStateFactory();

        $first = $factory->reset();
        $first->breedId = 'x';
        $first->breedSearch = 'husky';
        $first->filteredBreeds = [['id' => '1', 'name' => 'Siberian Husky']];
        $first->breedChoice = '1';
        $first->breedMixText = 'mix';

        $second = $factory->reset();

        self::assertNotSame($first, $second);
        self::assertNull($second->breedId);
        self::assertSame('', $second->breedSearch);
        self::assertSame([], $second->filteredBreeds);
        self::assertNull($second->breedChoice);
        self::assertNull($second->breedMixText);
    }
}
