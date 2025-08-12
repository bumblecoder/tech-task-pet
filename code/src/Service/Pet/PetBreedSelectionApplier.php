<?php

namespace App\Service\Pet;

use App\Entity\Pet;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

final readonly class PetBreedSelectionApplier
{
    public function __construct(private PetBreedResolverInterface $breedResolver) {}

    public function apply(Pet $pet, ?string $breedId, FormInterface $form): bool
    {
        if ($breedId !== null && $breedId !== '') {
            $breed = $this->breedResolver->byId($breedId);
            if (!$breed) {
                $form->addError(new FormError('Selected breed is invalid.'));
                return false;
            }

            $pet->setBreed($breed);
            $pet->setBreedOther(null);

            return true;
        }

        $pet->setBreed(null);

        $choice = $form->has('breedChoice') ? $form->get('breedChoice')->getData() : null;

        if ($choice === null || $choice === '') {
            $form->get('breedChoice')->addError(new FormError('Please choose one option.'));
            return false;
        }

        if ($choice === 'mix') {
            $mixText = $form->has('breedOther') ? trim((string) $form->get('breedOther')->getData()) : '';
            if ($mixText === '') {
                $form->get('breedOther')->addError(new FormError('Please specify the mix breed.'));
                return false;
            }

            $pet->setBreedOther($mixText);

            return true;
        }

        $pet->setBreedOther(null);

        return true;
    }
}
