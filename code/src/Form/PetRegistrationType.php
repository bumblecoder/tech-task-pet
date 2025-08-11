<?php

namespace App\Form;

use App\Entity\Pet;
use App\Entity\PetType;
use App\Enum\Sex;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class PetRegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'required' => true,
                'label' => 'Pet’s Name',
            ])
            ->add('type', EntityType::class, [
                'class'        => PetType::class,
                'choice_label' => 'name',
                'expanded'     => true,
                'multiple'     => false,
            ])
            ->add('breed', HiddenType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('breedOther', HiddenType::class, [
                'required' => false,
            ])
            ->add('breedChoice', ChoiceType::class, [
                'choices' => [
                    "I don't know" => 'unknown',
                    "It is a mix"  => 'mix',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped'   => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please choose one option.',
                    ]),
                ],
            ])
            ->add('sex', ChoiceType::class, [
                'choices' => [
                    'Female' => Sex::Female,
                    'Male' => Sex::Male,
                ],
                'expanded' => true,
                'multiple' => false,
                'required' => true,
            ])
            ->add('dateOfBirth', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'html5' => false,
                'input' => 'datetime_immutable',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('approximateAge', ChoiceType::class, [
                'choices' => array_combine(range(1, 20), range(1, 20)),
                'placeholder' => 'Select age',
                'required' => false,
            ])
            ->add('dobKnown', ChoiceType::class, [
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
            ])
            ->add('isDangerous', ChoiceType::class, [
                'choices' => [
                    'No'  => false,
                    'Yes' => true,
                ],
                'expanded' => true,
                'multiple' => false,
                'data' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pet::class,
        ]);
    }
}
