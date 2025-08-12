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
use App\Service\Pet\KnownDobHandler;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class KnownDobHandlerTest extends TestCase
{
    public function testSupports(): void
    {
        $h = new KnownDobHandler();

        self::assertTrue($h->supports(true));
        self::assertFalse($h->supports(false));
    }

    /**
     * @throws Exception
     */
    public function testApplyWithValidDateSetsDateAndClearsApproximateAge(): void
    {
        $pet = $this->createMock(Pet::class);

        $pet->expects(self::once())
            ->method('setDateOfBirth')
            ->with(self::callback(function ($dt) {
                return $dt instanceof \DateTimeImmutable
                    && '2020-01-02' === $dt->format('Y-m-d');
            }));

        $pet->expects(self::once())
            ->method('setApproximateAge')
            ->with(null);

        $handler = new KnownDobHandler();
        $handler->apply($pet, new DateParts(2020, 1, 2));
    }

    /**
     * @throws Exception
     */
    public function testApplyWithInvalidOrIncompleteDateSetsNullAndClearsApproximateAge(): void
    {
        $pet = $this->createMock(Pet::class);

        $pet->expects(self::once())
            ->method('setDateOfBirth')
            ->with(null);

        $pet->expects(self::once())
            ->method('setApproximateAge')
            ->with(null);

        $handler = new KnownDobHandler();

        $handler->apply($pet, new DateParts(2020, 2, 31));
    }
}
