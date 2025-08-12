<?php

declare(strict_types = 1);

/*
 * This file is a part of Anton Bielykh's test Application.
 *
 * Copyright © 2025 All rights reserved
 */

namespace App\Tests\Unit\Service\Pet;

use App\Entity\Pet;
use App\Entity\PetBreed;
use App\Service\Pet\PetBreedResolverInterface;
use App\Service\Pet\PetBreedSelectionApplier;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

final class PetBreedSelectionApplierTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testApplyWithBreedIdSetsBreedAndClearsOther(): void
    {
        $pet = $this->createMock(Pet::class);
        $breed = $this->createStub(PetBreed::class);
        $resolver = $this->createMock(PetBreedResolverInterface::class);
        $resolver->expects(self::once())->method('byId')->with('42')->willReturn($breed);

        $form = $this->createMock(FormInterface::class);
        $form->expects(self::never())->method('addError');

        $pet->expects(self::once())->method('setBreed')->with($breed);
        $pet->expects(self::once())->method('setBreedOther')->with(null);

        $applier = new PetBreedSelectionApplier($resolver);

        self::assertTrue($applier->apply($pet, '42', $form));
    }

    /**
     * @throws Exception
     */
    public function testApplyWithInvalidBreedIdAddsFormErrorAndFails(): void
    {
        $pet = $this->createMock(Pet::class);

        $resolver = $this->createMock(PetBreedResolverInterface::class);
        $resolver->expects(self::once())->method('byId')->with('404')->willReturn(null);

        $form = $this->createMock(FormInterface::class);
        $form->expects(self::once())
            ->method('addError')
            ->with(self::isInstanceOf(FormError::class));

        $pet->expects(self::never())->method('setBreed');
        $pet->expects(self::never())->method('setBreedOther');

        $applier = new PetBreedSelectionApplier($resolver);

        self::assertFalse($applier->apply($pet, '404', $form));
    }

    /**
     * @throws Exception
     */
    public function testApplyWithoutBreedIdAndNoChoiceAddsErrorOnChoice(): void
    {
        $pet = $this->createMock(Pet::class);
        $pet->expects(self::once())->method('setBreed')->with(null);

        $choiceField = $this->createMock(FormInterface::class);
        $choiceField->method('getData')->willReturn(null);
        $choiceField->expects(self::once())
            ->method('addError')
            ->with(self::isInstanceOf(FormError::class));

        $otherField = $this->createMock(FormInterface::class);

        $form = $this->createMock(FormInterface::class);
        $form->method('has')->willReturnMap([
            ['breedChoice', true],
            ['breedOther', true],
        ]);
        $form->method('get')->willReturnCallback(
            fn (string $name) => 'breedChoice' === $name ? $choiceField : $otherField
        );

        $resolver = $this->createMock(PetBreedResolverInterface::class);
        $resolver->expects(self::never())->method('byId');

        $applier = new PetBreedSelectionApplier($resolver);

        self::assertFalse($applier->apply($pet, null, $form));
    }

    /**
     * @throws Exception
     */
    public function testApplyMixWithoutTextAddsErrorOnOther(): void
    {
        $pet = $this->createMock(Pet::class);
        $pet->expects(self::once())->method('setBreed')->with(null);

        $choiceField = $this->createMock(FormInterface::class);
        $choiceField->method('getData')->willReturn('mix');

        $otherField = $this->createMock(FormInterface::class);
        $otherField->method('getData')->willReturn('   ');
        $otherField->expects(self::once())
            ->method('addError')
            ->with(self::isInstanceOf(FormError::class));

        $form = $this->createMock(FormInterface::class);
        $form->method('has')->willReturnMap([
            ['breedChoice', true],
            ['breedOther', true],
        ]);
        $form->method('get')->willReturnCallback(
            fn (string $name) => 'breedChoice' === $name ? $choiceField : $otherField
        );

        $resolver = $this->createMock(PetBreedResolverInterface::class);

        $applier = new PetBreedSelectionApplier($resolver);

        self::assertFalse($applier->apply($pet, '', $form));
    }

    /**
     * @throws Exception
     */
    public function testApplyMixWithTextSetsBreedOther(): void
    {
        $pet = $this->createMock(Pet::class);
        $pet->expects(self::once())->method('setBreed')->with(null);
        $pet->expects(self::once())->method('setBreedOther')->with('Husky');

        $choiceField = $this->createMock(FormInterface::class);
        $choiceField->method('getData')->willReturn('mix');

        $otherField = $this->createMock(FormInterface::class);
        $otherField->method('getData')->willReturn('  Husky  ');
        $otherField->expects(self::never())->method('addError');

        $form = $this->createMock(FormInterface::class);
        $form->method('has')->willReturnMap([
            ['breedChoice', true],
            ['breedOther', true],
        ]);
        $form->method('get')->willReturnCallback(
            fn (string $name) => 'breedChoice' === $name ? $choiceField : $otherField
        );

        $resolver = $this->createMock(PetBreedResolverInterface::class);

        $applier = new PetBreedSelectionApplier($resolver);

        self::assertTrue($applier->apply($pet, null, $form));
    }

    /**
     * @throws Exception
     */
    public function testApplyUnknownClearsBreedOther(): void
    {
        $pet = $this->createMock(Pet::class);
        $pet->expects(self::once())->method('setBreed')->with(null);
        $pet->expects(self::once())->method('setBreedOther')->with(null);

        $choiceField = $this->createMock(FormInterface::class);
        $choiceField->method('getData')->willReturn('unknown');

        $otherField = $this->createMock(FormInterface::class);

        $form = $this->createMock(FormInterface::class);
        $form->method('has')->willReturnMap([
            ['breedChoice', true],
            ['breedOther', true],
        ]);
        $form->method('get')->willReturnCallback(
            fn (string $name) => 'breedChoice' === $name ? $choiceField : $otherField
        );

        $resolver = $this->createMock(PetBreedResolverInterface::class);

        $applier = new PetBreedSelectionApplier($resolver);

        self::assertTrue($applier->apply($pet, null, $form));
    }
}
