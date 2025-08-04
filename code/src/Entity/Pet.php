<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 - 2025 All rights reserved
 */

namespace App\Entity;

use App\Enum\Sex;
use App\Repository\PetRepository;
use Doctrine\DBAL\Types\Types;
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

    #[Assert\NotBlank]
    #[ORM\Column(length: 55)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 55)]
    private ?string $type = null;

    #[Assert\NotBlank]
    #[ORM\Column(length: 55)]
    private ?string $breed = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dateOfBirth = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $approximateAge = null;

    #[Assert\NotBlank]
    #[Assert\Choice(
        callback: [Sex::class, 'values'],
        message: 'Choose a valid sex.'
    )]
    #[ORM\Column(length: 5)]
    private ?string $sex = null;

    #[ORM\Column]
    private ?bool $isDangerous = null;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getBreed(): ?string
    {
        return $this->breed;
    }

    public function setBreed(string $breed): self
    {
        $this->breed = $breed;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(?\DateTime $dateOfBirth): self
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

    public function getSex(): ?string
    {
        return $this->sex;
    }

    public function setSex(string $sex): static
    {
        $this->sex = $sex;

        return $this;
    }

    public function isDangerous(): ?bool
    {
        return $this->isDangerous;
    }

    public function setIsDangerous(bool $isDangerous): self
    {
        $this->isDangerous = $isDangerous;

        return $this;
    }
}
