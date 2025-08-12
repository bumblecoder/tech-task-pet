<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Unit\Service\Pet;

use App\Service\Pet\DateParts;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DatePartsTest extends TestCase
{
    public function testIsCompleteReturnsTrueWhenAllPartsPresent(): void
    {
        $p = new DateParts(2020, 1, 2);
        self::assertTrue($p->isComplete());
    }

    #[DataProvider('incompletePartsProvider')]
    public function testIsCompleteReturnsFalseWhenAnyPartMissing(?int $y, ?int $m, ?int $d): void
    {
        $p = new DateParts($y, $m, $d);
        self::assertFalse($p->isComplete());
    }

    public static function incompletePartsProvider(): iterable
    {
        yield 'no year' => [null, 1, 2];
        yield 'no month' => [2020, null, 2];
        yield 'no day' => [2020, 1, null];
        yield 'none' => [null, null, null];
        yield 'year only' => [2020, null, null];
        yield 'year+month only' => [2020, 1, null];
    }

    public function testToDateOrNullReturnsImmutableMidnightForValidDate(): void
    {
        $p = new DateParts(2020, 1, 2);
        $dt = $p->toDateOrNull();

        self::assertInstanceOf(\DateTimeImmutable::class, $dt);
        self::assertSame('2020-01-02', $dt->format('Y-m-d'));
        self::assertSame('00:00:00', $dt->format('H:i:s'));
    }

    #[DataProvider('invalidDatesProvider')]
    public function testToDateOrNullReturnsNullForInvalidOrIncompleteDates(?int $y, ?int $m, ?int $d): void
    {
        $p = new DateParts($y, $m, $d);
        self::assertNull($p->toDateOrNull());
    }

    public static function invalidDatesProvider(): iterable
    {
        yield 'incomplete: missing day' => [2020, 1, null];
        yield 'incomplete: missing month' => [2020, null, 10];
        yield 'incomplete: missing year' => [null, 2, 10];
        yield 'invalid: 31 Feb' => [2021, 2, 31];
        yield 'invalid: 29 Feb non-leap' => [2021, 2, 29];
        yield 'invalid: month 0' => [2020, 0, 10];
        yield 'invalid: month 13' => [2020, 13, 10];
        yield 'invalid: day 0' => [2020, 1, 0];
        yield 'invalid: day 32' => [2020, 1, 32];
        yield 'invalid: 31 Apr' => [2020, 4, 31];
    }
}
