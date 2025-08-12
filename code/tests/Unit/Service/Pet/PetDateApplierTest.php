<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Unit\Service\Pet;

use App\Entity\Pet;
use App\Service\Pet\DateParts;
use App\Service\Pet\PetDateApplier;
use App\Service\Pet\PetDateHandlerInterface;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class PetDateApplierTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testAppliesFirstSupportingHandler(): void
    {
        $pet = $this->createMock(Pet::class);
        $parts = new DateParts(2020, 1, 2);

        $h1 = $this->createMock(PetDateHandlerInterface::class);
        $h1->expects(self::once())->method('supports')->with(true)->willReturn(false);
        $h1->expects(self::never())->method('apply');

        $h2 = $this->createMock(PetDateHandlerInterface::class);
        $h2->expects(self::once())->method('supports')->with(true)->willReturn(true);
        $h2->expects(self::once())
            ->method('apply')
            ->with($pet, self::identicalTo($parts));

        $applier = new PetDateApplier([$h1, $h2]);

        $applier->apply($pet, true, $parts);
    }

    /**
     * @throws Exception
     */
    public function testStopsAtFirstSupportingHandler(): void
    {
        $pet = $this->createMock(Pet::class);
        $parts = new DateParts(1999, 12, 31);

        $first = $this->createMock(PetDateHandlerInterface::class);
        $first->expects(self::once())->method('supports')->with(true)->willReturn(true);
        $first->expects(self::once())->method('apply')->with($pet, self::identicalTo($parts));

        $second = $this->createMock(PetDateHandlerInterface::class);
        $second->expects(self::never())->method('supports');
        $second->expects(self::never())->method('apply');

        $applier = new PetDateApplier([$first, $second]);

        $applier->apply($pet, true, $parts);
    }

    /**
     * @throws Exception
     */
    public function testFallbackToUnknownWhenNoHandlerSupports(): void
    {
        $pet = $this->createMock(Pet::class);
        $parts = new DateParts(null, null, null);

        $h1 = $this->createMock(PetDateHandlerInterface::class);
        $h1->expects(self::once())->method('supports')->with(false)->willReturn(false);
        $h1->expects(self::never())->method('apply');

        $h2 = $this->createMock(PetDateHandlerInterface::class);
        $h2->expects(self::once())->method('supports')->with(false)->willReturn(false);
        $h2->expects(self::never())->method('apply');

        $pet->expects(self::once())->method('setDateOfBirth')->with(null);
        $pet->expects(self::never())->method('setApproximateAge');

        $applier = new PetDateApplier([$h1, $h2]);

        $applier->apply($pet, false, $parts);
    }
}
