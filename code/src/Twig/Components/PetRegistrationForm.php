<?php

namespace App\Twig\Components;

use App\Entity\Pet;
use App\Entity\PetBreed;
use App\Entity\PetType;
use App\Form\PetRegistrationType;
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

    public function __construct(private EntityManagerInterface $em) {}

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

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PetRegistrationType::class, new Pet());
    }

    public function shouldSubmitFormOnRender(): bool
    {
        return !$this->disableSubmitOnRender;
    }

    #[LiveAction]
    public function pickType(#[LiveArg] string $id): void
    {
        $type = $this->em->getRepository(PetType::class)->find($id);
        $this->type = $type;

        $this->getForm()->get('type')->setData($type);

        $this->breedId = null;
        $this->breedSearch = null;
        $this->filteredBreeds = [];

        $this->breedChoice = null;
        $this->breedMixText = null;
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

        $type = $this->type ?? $this->getForm()->get('type')->getData();

        if (!$this->breedSearch || !$type instanceof PetType) {
            $this->filteredBreeds = [];
            $this->breedChoice = null;
            $this->breedMixText = null;
            return;
        }

        $breeds = $this->em->getRepository(PetBreed::class)->findBySearch($type, $this->breedSearch);

        $this->filteredBreeds = array_map(
            fn(PetBreed $b) => ['id'=>(string)$b->getId(),'name'=>$b->getName()],
            $breeds ?? []
        );

        if (!empty($this->filteredBreeds)) {
            $this->breedChoice = null;
            $this->breedMixText = null;
        }
    }

    #[LiveAction]
    public function setBreed(#[LiveArg('id')] string $id): void
    {
        $this->breedId = $id;

        if ($breed = $this->em->getRepository(PetBreed::class)->find($id)) {
            $this->breedSearch = $breed->getName();
            $this->selectedBreedName = $breed->getName();
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

            $choice = $form->has('breedChoice') ? $form->get('breedChoice')->getData() : null; // 'unknown' | 'mix' | null
            $mixTxt = $form->has('breedOther') ? (string) $form->get('breedOther')->getData() : '';

            if ($choice === 'mix') {
                $mixTxt = trim($mixTxt);
                if ($mixTxt == '') {
                    $form->addError(new FormError('Please describe the mix breed.'));
                    return;
                }
                $pet->setBreedOther($mixTxt);
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

        $this->resetForm();

        $this->userSubmit   = false;
        $this->breedId      = null;
        $this->breedSearch  = '';
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
}
