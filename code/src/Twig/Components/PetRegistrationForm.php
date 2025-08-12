<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Twig\Components;

use App\Entity\Pet;
use App\Entity\PetBreed;
use App\Entity\PetType;
use App\Form\PetRegistrationType;
use App\Service\Pet\BreedSearchService;
use App\Service\Pet\BreedStateFactory;
use App\Service\Pet\PetBreedResolverInterface;
use App\Service\Pet\PetTypeResolver;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentWithFormTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(name: 'PetRegistrationForm')]
class PetRegistrationForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Pet $data = null;

    #[LiveProp]
    public ?PetType $type = null;

    #[LiveProp(writable: true)]
    public ?string $breedSearch = null;

    #[LiveProp(writable: true)]
    public ?string $breedId = null;

    #[LiveProp]
    public array $filteredBreeds = [];

    #[LiveProp(writable: true)]
    public bool $dobKnown = false;

    #[LiveProp(writable: true)]
    public ?int $approximateAge = null;

    #[LiveProp(writable: true)]
    public ?int $dobDay = null;

    #[LiveProp(writable: true)]
    public ?int $dobMonth = null;

    #[LiveProp(writable: true)]
    public ?int $dobYear = null;

    #[LiveProp(writable: true)]
    public ?string $breedChoice = null;

    #[LiveProp(writable: true)]
    public ?string $breedMixText = null;

    #[LiveProp(writable: true)]
    public ?string $selectedBreedName = null;

    private bool $disableSubmitOnRender = false;

    #[LiveProp(writable: true)]
    public bool $userSubmit = false;

    #[LiveProp]
    public ?array $summary = null;

    #[LiveProp(writable: true)]
    public bool $showSummary = false;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PetTypeResolver $petTypeResolver,
        private readonly BreedStateFactory $breedStateFactory,
        private readonly BreedSearchService $breedSearchService,
        private readonly PetBreedResolverInterface $petBreedResolver,
    ) {
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PetRegistrationType::class, new Pet());
    }

    #[LiveAction]
    public function pickType(#[LiveArg] string $id): void
    {
        if ($this->type && method_exists($this->type, 'getId') && (string) $this->type->getId() === $id) {
            return;
        }

        $type = $this->petTypeResolver->byId($id);

        if (null === $type) {
            return;
        }

        $this->type = $type;
        $this->formValues['type'] = $type->getId();

        $state = $this->breedStateFactory->reset();

        $this->breedId = $state->breedId;
        $this->breedSearch = $state->breedSearch;
        $this->filteredBreeds = $state->filteredBreeds;
        $this->breedChoice = $state->breedChoice;
        $this->breedMixText = $state->breedMixText;
    }

    #[LiveAction]
    public function searchBreeds(): void
    {
        if ($this->breedId && $this->breedSearch !== $this->selectedBreedName) {
            $this->breedId = null;
            $this->selectedBreedName = null;
            $this->breedChoice = null;
            $this->breedMixText = null;
        }

        $type = $this->type;

        if (!$type) {
            $typeId = $this->formValues['type'] ?? null;

            if ($typeId) {
                $type = $this->petTypeResolver->byId((string) $typeId);
            }
        }

        if (!$this->breedSearch || !$type instanceof PetType) {
            $this->filteredBreeds = [];
            $this->breedChoice = null;
            $this->breedMixText = null;

            return;
        }

        $this->filteredBreeds = $this->breedSearchService->search($type, $this->breedSearch);

        if (!empty($this->filteredBreeds)) {
            $this->breedChoice = null;
            $this->breedMixText = null;
        }
    }

    #[LiveAction]
    public function setBreed(#[LiveArg('id')] string $id): void
    {
        $this->breedId = $id;
        $this->formValues['breed'] = $id;

        if ($breed = $this->petBreedResolver->byId($id)) {
            $name = $breed->getName();
            $this->breedSearch = $name;
            $this->selectedBreedName = $name;
        }

        $this->filteredBreeds = [];
        $this->breedChoice = null;
        $this->breedMixText = null;
    }

    #[LiveAction]
    public function save(): void
    {
        $this->userSubmit = true;
        $this->submitForm();

        $form = $this->getForm();

        /** @var Pet|null $pet */
        $pet = $form->getData();
        if (!$pet instanceof Pet) {
            $form->addError(new FormError('Form data is not bound to Pet.'));

            return;
        }

        if ($this->breedId) {
            $breed = $this->em->getRepository(PetBreed::class)->find($this->breedId);
            if (!$breed) {
                $form->addError(new FormError('Selected breed is invalid.'));

                return;
            }
            $pet->setBreed($breed);
            $pet->setBreedOther(null);
        } else {
            $pet->setBreed(null);

            $choice = $form->has('breedChoice') ? $form->get('breedChoice')->getData() : null; // 'unknown'|'mix'|null
            $mixText = $form->has('breedOther') ? trim((string) $form->get('breedOther')->getData()) : '';

            if (null === $choice || '' === $choice) {
                $form->get('breedChoice')->addError(new FormError('Please choose one option.'));

                return;
            }

            if ('mix' === $choice) {
                if ('' === $mixText) {
                    $form->get('breedOther')->addError(new FormError('Please specify the mix breed.'));

                    return;
                }
                $pet->setBreedOther($mixText);
            } else {
                $pet->setBreedOther(null);
            }
        }

        if ($this->dobKnown) {
            $date = null;
            if ($this->dobYear && $this->dobMonth && $this->dobDay) {
                try {
                    $date = new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $this->dobYear, $this->dobMonth, $this->dobDay));
                } catch (\Throwable) {
                }
            }
            $pet->setDateOfBirth($date);
            $pet->setApproximateAge(null);
        } else {
            $pet->setDateOfBirth(null);
        }

        if (!$form->isValid()) {
            return;
        }

        $this->em->persist($pet);
        $this->em->flush();

        $this->summary = [
            'name' => $pet->getName() ?: '—',
            'type' => $pet->getType()?->getName() ?: '—',
            'breed' => $pet->getBreed()?->getName()
                ?? ($pet->getBreedOther() ? 'Mix' : 'Unknown'),
            'mix' => $pet->getBreedOther() ? 'Yes' : 'No',
            'mixText' => $pet->getBreedOther() ?: null,
            'gender' => $pet->getSex()?->name ?? '—',
            'ageOrDob' => $pet->getDateOfBirth()
                ? $pet->getDateOfBirth()->format('Y-m-d')
                : ($pet->getApproximateAge() ? ('~' . $pet->getApproximateAge() . ' years') : '—'),
            'dangerous' => $pet->isDangerous(),
        ];

        $this->showSummary = true;
        $this->disableSubmitOnRender = true;

        $this->resetForm();

        $this->userSubmit = false;
        $this->breedId = null;
        $this->breedSearch = '';
        $this->dobYear = $this->dobMonth = $this->dobDay = null;
    }

    #[LiveAction]
    public function resetDobControls(): void
    {
        if ($this->dobKnown) {
            $this->approximateAge = null;
            if ($this->getForm()->has('approximateAge')) {
                $this->getForm()->get('approximateAge')->setData(null);
            }
        } else {
            $this->dobDay = null;
            $this->dobMonth = null;
            $this->dobYear = null;
            if ($this->getForm()->has('dateOfBirth')) {
                $this->getForm()->get('dateOfBirth')->setData(null);
            }
        }
    }

    #[LiveAction]
    public function addAnother(): void
    {
        $this->showSummary = false;
        $this->summary = null;
        $this->disableSubmitOnRender = false;

        $this->resetForm();
        if (\method_exists($this, 'resetValidation')) {
            $this->resetValidation();
        }

        $this->breedId = null;
        $this->breedSearch = '';
        $this->breedChoice = null;
        $this->breedMixText = null;
        $this->dobYear = $this->dobMonth = $this->dobDay = null;
        $this->userSubmit = false;
    }
}
