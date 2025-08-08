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

#[AsLiveComponent(name: 'PetRegistrationForm')]
class PetRegistrationForm extends AbstractController
{
    use ComponentWithFormTrait;
    use DefaultActionTrait;

    public function __construct(private EntityManagerInterface $em) {}

    #---------------------------------------
    # ДАННЫЕ ФОРМЫ / LIVE ПРОПЫ
    #---------------------------------------

    // Объект формы
    #[LiveProp(writable: true)]
    public ?Pet $data = null;

    // Текущий выбранный тип (для фильтрации пород и отображения)
    #[LiveProp]
    public ?PetType $type = null;

    // Поиск породы
    #[LiveProp(writable: true)]
    public ?string $breedSearch = null;

    // Выбранная порода (ID) — чтобы не затиралось при submitForm()
    #[LiveProp(writable: true)]
    public ?string $breedId = null;

    // Выпадающий список пород: [['id' => ..., 'name' => ...], ...]
    #[LiveProp]
    public array $filteredBreeds = [];

    // Ветка возраста/даты рождения
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

    #---------------------------------------
    # ИНИЦИАЛИЗАЦИЯ
    #---------------------------------------

    public function mount(): void
    {
        $this->data ??= new Pet();
    }

    protected function instantiateForm(): FormInterface
    {
        // ВАЖНО: форма привязана к $this->data
        return $this->createForm(PetRegistrationType::class, $this->data);
    }

    #---------------------------------------
    # LIVE ACTIONS
    #---------------------------------------

    /**
     * Клик по табу типа (radio/label):
     * - ставим $this->type (для Live),
     * - синхронизируем поле формы 'type' (для сабмита/валидации),
     * - чистим автокомплит пород при смене типа.
     */
    #[LiveAction]
    public function pickType(#[LiveArg] string $id): void
    {
        $type = $this->em->getRepository(PetType::class)->find($id);
        $this->type = $type;

        // синхронизируем с формой — теперь сабмит пройдёт корректно
        $this->getForm()->get('type')->setData($type);

        // сбросим выбранную породу при смене типа
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

    /**
     * Сабмит формы.
     * Здесь также можно собрать dateOfBirth из dobDay/Month/Year,
     * если dobKnown === true, и обнулить approximateAge, и наоборот.
     */
    #[LiveAction]
    public function save()
    {
//        // Если известен ДР — собрать дату и обнулить примерный возраст
//        if ($this->dobKnown === true) {
//            if ($this->dobYear && $this->dobMonth && $this->dobDay) {
//                try {
//                    $date = new \DateTimeImmutable(sprintf('%04d-%02d-%02d', $this->dobYear, $this->dobMonth, $this->dobDay));
//                    $this->getForm()->get('dateOfBirth')->setData($date);
//                    $this->data?->setDateOfBirth($date);
//                } catch (\Throwable) {
//                    // оставим валидации формы отработать ошибку
//                }
//            }
//            $this->approximateAge = null;
//            $this->getForm()->get('approximateAge')->setData(null);
//        } else {
//            // Если неизвестен ДР — обнуляем дату, требуем approximateAge
//            $this->getForm()->get('dateOfBirth')->setData(null);
//            $this->data?->setDateOfBirth(null);
//        }

        // Перед submitForm() убедимся, что hidden инпут с breedId есть в DOM
        // (в Twig ты рендеришь: <input type="hidden" name="{{ form.breed.vars.full_name }}" value="{{ this.breedId ?? '' }}">)

        $this->submitForm();

        if (!$this->getForm()->isValid()) {
            return;
        }

        /** @var Pet $pet */
        $pet = $this->getForm()->getData();
        $pet->setSex(Sex::Male);
        if (!$pet->getBreed() && $this->breedId) {
            if ($breed = $this->em->getRepository(PetBreed::class)->find($this->breedId)) {
                $pet->setBreed($breed);
            }
        }

        $this->em->persist($pet);
        $this->em->flush();

        return null;
    }
}
