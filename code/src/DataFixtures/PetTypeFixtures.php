<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\DataFixtures;

use App\Entity\PetType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class PetTypeFixtures extends Fixture
{
    public const string REF_DOG = 'pet_type_dog';
    public const string REF_CAT = 'pet_type_cat';

    public function load(ObjectManager $manager): void
    {
        $dog = (new PetType())->setName('Dog');
        $cat = (new PetType())->setName('Cat');

        $manager->persist($dog);
        $manager->persist($cat);
        $manager->flush();

        $this->addReference(self::REF_DOG, $dog);
        $this->addReference(self::REF_CAT, $cat);
    }
}
