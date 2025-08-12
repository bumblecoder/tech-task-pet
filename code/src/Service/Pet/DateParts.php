<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

final readonly class DateParts
{
    public function __construct(
        public ?int $year,
        public ?int $month,
        public ?int $day,
    ) {
    }

    public function isComplete(): bool
    {
        return null !== $this->year && null !== $this->month && null !== $this->day;
    }

    public function toDateOrNull(): ?\DateTimeImmutable
    {
        if (!$this->isComplete()) {
            return null;
        }
        if (!\checkdate((int) $this->month, (int) $this->day, (int) $this->year)) {
            return null;
        }
        $str = \sprintf('%04d-%02d-%02d', $this->year, $this->month, $this->day);
        $dt = \DateTimeImmutable::createFromFormat('!Y-m-d', $str);

        return $dt ?: null;
    }
}
