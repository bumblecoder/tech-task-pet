<?php

namespace App\DataFixtures;

use App\Entity\PetBreed;
use App\Entity\PetType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class PetBreedFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var PetType $dog */
        $dog = $this->getReference(PetTypeFixtures::REF_DOG, PetType::class);
        /** @var PetType $cat */
        $cat = $this->getReference(PetTypeFixtures::REF_CAT, PetType::class);

        $dogBreeds = [
            'Labrador Retriever',
            'German Shepherd',
            'Golden Retriever',
            'French Bulldog',
            'Bulldog',
            'Poodle',
            'Beagle',
            'Rottweiler',
            'Dachshund',
            'Siberian Husky',
        ];

        $catBreeds = [
            'Persian',
            'Maine Coon',
            'Siamese',
            'Ragdoll',
            'Bengal',
            'British Shorthair',
            'Sphynx',
            'Abyssinian',
            'Scottish Fold',
            'Russian Blue',
        ];

        foreach ($dogBreeds as $name) {
            $b = (new PetBreed())
                ->setName($name)
                ->setType($dog);
            $manager->persist($b);
        }

        foreach ($catBreeds as $name) {
            $b = (new PetBreed())
                ->setName($name)
                ->setType($cat);
            $manager->persist($b);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [PetTypeFixtures::class];
    }
}
