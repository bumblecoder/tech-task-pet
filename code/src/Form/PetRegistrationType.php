<?php

namespace App\Form;

use App\Entity\Pet;
use App\Entity\PetType;
use App\Entity\PetBreed;
use App\Enum\Sex;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('breed', TextType::class, [
                'label' => 'Breed',
                'required' => false,
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
            ]);

//        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
//            /** @var Pet $pet */
//            $pet = $event->getData();
//            $form = $event->getForm();
//
//            $dobKnown = filter_var($form->get('dobKnown')->getData(), FILTER_VALIDATE_BOOLEAN);
//
//            if ($dobKnown) {
//                if (!$pet->getDateOfBirth()) {
//                    $form->get('dateOfBirth')->addError(new \Symfony\Component\Form\FormError('Please provide the date of birth.'));
//                }
//                $pet->setApproximateAge(null);
//            } else {
//                if (!$pet->getApproximateAge()) {
//                    $form->get('approximateAge')->addError(new \Symfony\Component\Form\FormError('Please select approximate age.'));
//                }
//                $pet->setDateOfBirth(null);
//            }
//        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pet::class,
        ]);
    }
}
