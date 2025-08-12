<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Service\Pet;

use App\Entity\Pet;
use App\Service\Pet\DateParts;
use App\Service\Pet\UnknownDobHandler;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

class UnknownDobHandlerTest extends TestCase
{
    public function testSupports(): void
    {
        $h = new UnknownDobHandler();

        self::assertTrue($h->supports(false));
        self::assertFalse($h->supports(true));
    }

    /**
     * @throws Exception
     */
    public function testApplySetsDateOfBirthNullAndLeavesApproximateAgeUntouched(): void
    {
        $pet = $this->createMock(Pet::class);

        $pet->expects(self::once())
            ->method('setDateOfBirth')
            ->with(null);

        $pet->expects(self::never())
            ->method('setApproximateAge');

        $h = new UnknownDobHandler();

        $parts = new DateParts(2020, 1, 1);

        $h->apply($pet, $parts);
    }
}
