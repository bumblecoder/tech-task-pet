<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Service\Pet;

use App\Entity\Pet;
use Symfony\Component\DependencyInjection\Attribute\TaggedIterator;

final readonly class PetDateApplier
{
    /** @param iterable<PetDateHandlerInterface> $handlers */
    public function __construct(
        #[TaggedIterator('app.pet_date_handler')]
        private iterable $handlers)
    {
    }

    public function apply(Pet $pet, bool $dobKnown, DateParts $parts): void
    {
        foreach ($this->handlers as $h) {
            if ($h->supports($dobKnown)) {
                $h->apply($pet, $parts);

                return;
            }
        }

        (new UnknownDobHandler())->apply($pet, $parts);
    }
}
