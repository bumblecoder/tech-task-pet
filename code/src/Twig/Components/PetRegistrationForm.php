<?php

namespace App\Twig\Components;

use App\Entity\Pet;
use App\Entity\PetBreed;
use App\Entity\PetType;
use App\Form\PetRegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PetRegistrationType::class, $this->data);
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
    }

    #[LiveAction]
    public function searchBreeds(): void
    {
        $type = $this->type ?? $this->getForm()->get('type')->getData();
        if (!$this->breedSearch || !$type instanceof PetType) {
            $this->filteredBreeds = [];
            return;
        }

        $breeds = $this->em->getRepository(PetBreed::class)->findBySearch($type, $this->breedSearch);
        $this->filteredBreeds = array_map(
            fn(PetBreed $b) => ['id' => (string) $b->getId(), 'name' => $b->getName()],
            $breeds ?? []
        );
    }

    #[LiveAction]
    public function setBreed(#[LiveArg('id')] string $id): void
    {
        $this->breedId = $id;

        if ($breed = $this->em->getRepository(PetBreed::class)->find($id)) {
            $this->breedSearch = $breed->getName();
        }

        $this->filteredBreeds = [];
    }

    #[LiveAction]
    public function save()
    {
        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            return;
        }

        /** @var Pet $pet */
        $pet = $this->getForm()->getData();

        if (!$pet->getBreed() && $this->breedId) {
            if ($breed = $this->em->getRepository(PetBreed::class)->find($this->breedId)) {
                $pet->setBreed($breed);
            }
        }

        if ($this->dobKnown) {
            $date = null;
            if ($this->dobYear && $this->dobMonth && $this->dobDay) {
                try {
                    $date = new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $this->dobYear, $this->dobMonth, $this->dobDay));
                } catch (\Throwable) {}
            }
            $pet->setDateOfBirth($date);
            $pet->setApproximateAge(null);
        } else {
            $pet->setDateOfBirth(null);
        }

        $this->em->persist($pet);
        $this->em->flush();

        return null;
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
