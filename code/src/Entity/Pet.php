<?php

namespace App\Entity;

use App\Enum\Sex;
use App\Repository\PetRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\StringType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PetRepository::class)]
class Pet
{
    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    private ?Uuid $id = null;

    #[ORM\Column(name: 'name', type: 'string', length: 55)]
    #[Assert\NotBlank(message: 'Please enter a name for your pet.')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'The name must be at least {{ limit }} characters long.',
        maxMessage: 'The name cannot be longer than {{ limit }} characters.'
    )]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: PetType::class)]
    #[ORM\JoinColumn(name: 'type', referencedColumnName: 'id', nullable: false)]
    #[Assert\NotNull(message: 'Please select a type for your pet.')]
    private PetType $type;

    #[ORM\ManyToOne(targetEntity: PetBreed::class)]
    #[ORM\JoinColumn(name: 'breed', referencedColumnName: 'id', nullable: false)]
    private ?PetBreed $breed = null;

    #[ORM\Column(name: 'date_of_birth', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $dateOfBirth = null;

    #[ORM\Column(name: 'approximate_age', type: 'integer', nullable: true)]
    private ?int $approximateAge = null;

    #[ORM\Column(name: 'sex', length: 10, enumType: Sex::class)]
    private Sex $sex;

    #[ORM\Column(name: 'is_dangerous', type: 'boolean')]
    private bool $isDangerous = false;

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): PetType
    {
        return $this->type;
    }

    public function setType(PetType $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBreed(): ?PetBreed
    {
        return $this->breed;
    }

    public function setBreed(PetBreed $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    public function getDateOfBirth(): ?DateTimeImmutable
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?DateTimeImmutable $dateOfBirth): self
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    public function getApproximateAge(): ?int
    {
        return $this->approximateAge;
    }

    public function setApproximateAge(?int $approximateAge): self
    {
        $this->approximateAge = $approximateAge;

        return $this;
    }

    public function getSex(): Sex
    {
        return $this->sex;
    }

    public function setSex(Sex $sex): self
    {
        $this->sex = $sex;

        return $this;
    }

    public function isDangerous(): bool
    {
        return $this->isDangerous;
    }

    public function setIsDangerous(bool $value): self
    {
        $this->isDangerous = $value;

        return $this;
    }
}
