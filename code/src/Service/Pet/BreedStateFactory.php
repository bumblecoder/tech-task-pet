<?php

namespace App\Service\Pet;

final class BreedStateFactory
{
    public function reset(): BreedState
    {
        return new BreedState();
    }
}
