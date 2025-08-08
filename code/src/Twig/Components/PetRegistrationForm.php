<?php

namespace App\Twig\Components;

use App\Entity\Pet;
use App\Entity\PetBreed;
use App\Entity\PetType;
use App\Enum\Sex;
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

#[AsLiveComponent]
class PetRegistrationForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    #[LiveProp(writable: true)]
    public ?Pet $data = null;

    #[LiveProp(writable: true)]
    public ?PetType $type = null;

    #[LiveProp(writable: true)]
    public ?string $breedSearch = null;

    #[LiveProp]
    public array $filteredBreeds = [];

    public function __construct(private EntityManagerInterface $em) {}

    public function mount(): void
    {
        $this->data ??= new Pet();
    }

    protected function instantiateForm(): FormInterface
    {
        return $this->createForm(PetRegistrationType::class);
    }

    #[LiveAction]
    public function save()
    {
        $breed = $this->em->getRepository(PetBreed::class)->findOneBy(['name' => 'British']);

        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            return;
        }

        /** @var Pet $pet */
        $pet = $this->getForm()->getData();

        $pet->setSex(Sex::Male);
        $pet->setBreed($breed);
        $this->em->persist($pet);
        $this->em->flush();

        return null;
    }

    #[LiveAction]
    public function searchBreeds(): void
    {
        if (!$this->breedSearch || !$this->type instanceof PetType) {
            $this->filteredBreeds = [];
            return;
        }

        $breeds = $this->em
            ->getRepository(PetBreed::class)
            ->findBySearch($this->type, $this->breedSearch);

        if ($breeds) {
            $this->filteredBreeds = array_map(fn($breed) => $breed->getName(), $breeds);
        }
    }


    #[LiveAction]
    public function setBreed(#[LiveArg] string $breed): void
    {
        $this->breedSearch = $breed;

        $entity = $this->em->getRepository(PetBreed::class)->findOneBy(['name' => $breed]);

        if ($entity) {
            $this->getForm()->get('breed')->setData($entity);
        }

        $this->filteredBreeds = [];
    }

}
