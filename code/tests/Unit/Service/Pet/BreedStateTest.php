<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Service\Pet;

use App\Service\Pet\BreedState;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class BreedStateTest extends TestCase
{
    public function testDefaults(): void
    {
        $s = new BreedState();

        self::assertNull($s->breedId);
        self::assertSame('', $s->breedSearch);
        self::assertIsArray($s->filteredBreeds);
        self::assertCount(0, $s->filteredBreeds);
        self::assertNull($s->breedChoice);
        self::assertNull($s->breedMixText);
    }

    #[DataProvider('provideMutableValues')]
    public function testMutablePropertiesCanBeSetAndRead(
        ?string $breedId,
        string $breedSearch,
        array $filteredBreeds,
        ?string $breedChoice,
        ?string $breedMixText,
    ): void {
        $s = new BreedState();

        $s->breedId = $breedId;
        $s->breedSearch = $breedSearch;
        $s->filteredBreeds = $filteredBreeds;
        $s->breedChoice = $breedChoice;
        $s->breedMixText = $breedMixText;

        self::assertSame($breedId, $s->breedId);
        self::assertSame($breedSearch, $s->breedSearch);
        self::assertSame($filteredBreeds, $s->filteredBreeds);
        self::assertSame($breedChoice, $s->breedChoice);
        self::assertSame($breedMixText, $s->breedMixText);
    }

    public static function provideMutableValues(): iterable
    {
        yield 'empty list, only search' => [null, 'hus', [], null, null];

        yield 'with results' => [
            'abc-123',
            'husky',
            [
                ['id' => '1', 'name' => 'Siberian Husky'],
                ['id' => '2', 'name' => 'Alaskan Husky'],
            ],
            '1',
            null,
        ];

        yield 'mix description set' => [null, '', [], null, 'Husky x Shepherd'];
    }

    public function testFilteredBreedsShapeConvention(): void
    {
        $s = new BreedState();
        $s->filteredBreeds = [
            ['id' => '1', 'name' => 'Siberian Husky'],
            ['id' => '2', 'name' => 'Alaskan Husky'],
        ];

        foreach ($s->filteredBreeds as $row) {
            self::assertIsArray($row);
            self::assertArrayHasKey('id', $row);
            self::assertArrayHasKey('name', $row);
            self::assertIsString($row['id']);
            self::assertIsString($row['name']);
        }
    }
}
