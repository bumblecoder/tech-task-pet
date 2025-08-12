<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Twig\Components;

use App\Entity\Pet;
use App\Entity\PetType;
use App\Form\PetRegistrationType;
use App\Service\Pet\BreedSearchService;
use App\Service\Pet\BreedStateFactory;
use App\Service\Pet\DateParts;
use App\Service\Pet\PetBreedResolverInterface;
use App\Service\Pet\PetBreedSelectionApplier;
use App\Service\Pet\PetDateApplier;
use App\Service\Pet\PetSummaryBuilder;
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
        private readonly PetBreedSelectionApplier $breedSelectionApplier,
        private readonly PetDateApplier $petDateApplier,
        private readonly PetSummaryBuilder $petSummaryBuilder,
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

        if (!$this->breedSelectionApplier->apply($pet, $this->breedId, $form)) {
            return;
        }

        $this->petDateApplier->apply(
            $pet,
            $this->dobKnown,
            new DateParts($this->dobYear, $this->dobMonth, $this->dobDay)
        );

        if (!$form->isValid()) {
            return;
        }

        $this->em->persist($pet);
        $this->em->flush();

        $this->summary = $this->petSummaryBuilder->build($pet);

        $this->showSummary = true;
        $this->disableSubmitOnRender = true;

        $this->resetForm();
        $this->resetAfterSuccessfulSave();
    }

    #[LiveAction]
    public function addAnother(): void
    {
        $this->hideSummary();
        $this->resetForm();
        $this->resetEntryState();
    }

    private function resetAfterSuccessfulSave(): void
    {
        $s = $this->breedStateFactory->reset();
        $this->breedId = $s->breedId;
        $this->breedSearch = $s->breedSearch;
        $this->filteredBreeds = $s->filteredBreeds;
        $this->breedChoice = $s->breedChoice;
        $this->breedMixText = $s->breedMixText;

        $this->userSubmit = false;
        $this->dobYear = $this->dobMonth = $this->dobDay = null;
    }

    private function hideSummary(): void
    {
        $this->showSummary = false;
        $this->summary = null;
        $this->disableSubmitOnRender = false;
    }

    private function resetEntryState(): void
    {
        if (isset($this->breedStateFactory)) {
            $s = $this->breedStateFactory->reset();
            $this->breedId      = $s->breedId;
            $this->breedSearch  = $s->breedSearch;
            $this->breedChoice  = $s->breedChoice;
            $this->breedMixText = $s->breedMixText;
        } else {
            $this->breedId = null;
            $this->breedSearch = '';
            $this->breedChoice = null;
            $this->breedMixText = null;
        }

        $this->dobYear = $this->dobMonth = $this->dobDay = null;
        $this->userSubmit = false;
    }
}
