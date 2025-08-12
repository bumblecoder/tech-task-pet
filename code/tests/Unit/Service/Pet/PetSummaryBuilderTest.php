<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\Pet;

use App\Entity\Pet;
use App\Entity\PetBreed;
use App\Entity\PetType;
use App\Service\Pet\PetSummaryBuilder;
use App\Enum\Sex;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;

final class PetSummaryBuilderTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testBuildWithBreedAndDob(): void
    {
        $pet   = $this->createMock(Pet::class);
        $type  = $this->createMock(PetType::class);
        $breed = $this->createMock(PetBreed::class);

        $pet->method('getName')->willReturn('Barsik');
        $type->method('getName')->willReturn('Cat');
        $breed->method('getName')->willReturn('Persian');

        $pet->method('getType')->willReturn($type);
        $pet->method('getBreed')->willReturn($breed);
        $pet->method('getBreedOther')->willReturn(null);
        $pet->method('getSex')->willReturn(Sex::Male);
        $pet->method('isDangerous')->willReturn(false);

        $dob = new \DateTimeImmutable('2024-05-10');
        $pet->method('getDateOfBirth')->willReturn($dob);
        $pet->method('getApproximateAge')->willReturn(null);

        $builder = new PetSummaryBuilder();
        $summary = $builder->build($pet);

        self::assertSame('Barsik', $summary['name']);
        self::assertSame('Cat', $summary['type']);
        self::assertSame('Persian', $summary['breed']);
        self::assertSame('No', $summary['mix']);
        self::assertNull($summary['mixText']);
        self::assertSame('Male', $summary['gender']);
        self::assertSame('2024-05-10', $summary['ageOrDob']);
        self::assertFalse($summary['dangerous']);
    }

    /**
     * @throws Exception
     */
    public function testBuildWithMixAndApproximateAge(): void
    {
        $pet  = $this->createMock(Pet::class);
        $type = $this->createMock(PetType::class);

        $pet->method('getName')->willReturn('');
        $type->method('getName')->willReturn('Dog');

        $pet->method('getType')->willReturn($type);
        $pet->method('getBreed')->willReturn(null);
        $pet->method('getBreedOther')->willReturn('Husky x Shepherd');
        $pet->method('getSex')->willReturn(Sex::Female);
        $pet->method('isDangerous')->willReturn(true);

        $pet->method('getDateOfBirth')->willReturn(null);
        $pet->method('getApproximateAge')->willReturn(4);

        $builder = new PetSummaryBuilder();
        $summary = $builder->build($pet);

        self::assertSame('—', $summary['name']);
        self::assertSame('Dog', $summary['type']);
        self::assertSame('Mix', $summary['breed']);
        self::assertSame('Yes', $summary['mix']);
        self::assertSame('Husky x Shepherd', $summary['mixText']);
        self::assertSame('Female', $summary['gender']);
        self::assertSame('~4 years', $summary['ageOrDob']);
        self::assertTrue($summary['dangerous']);
    }

    /**
     * @throws Exception
     */
    public function testBuildUnknownBreedAndNoAgeOrDob(): void
    {
        $pet = $this->createMock(Pet::class);
        $type = $this->createMock(PetType::class);

        $pet->method('getName')->willReturn(null);

        $type->method('getName')->willReturn('');
        $pet->method('getType')->willReturn($type);

        $pet->method('getBreed')->willReturn(null);
        $pet->method('getBreedOther')->willReturn(null);
        $pet->method('getSex')->willReturn(Sex::Male);
        $pet->method('isDangerous')->willReturn(false);

        $pet->method('getDateOfBirth')->willReturn(null);
        $pet->method('getApproximateAge')->willReturn(null);

        $builder = new PetSummaryBuilder();
        $summary = $builder->build($pet);

        self::assertSame('—', $summary['name']);
        self::assertSame('—', $summary['type']);
        self::assertSame('Unknown', $summary['breed']);
        self::assertSame('No', $summary['mix']);
        self::assertNull($summary['mixText']);
        self::assertSame('Male', $summary['gender']);
        self::assertSame('—', $summary['ageOrDob']);
        self::assertFalse($summary['dangerous']);
    }
}
