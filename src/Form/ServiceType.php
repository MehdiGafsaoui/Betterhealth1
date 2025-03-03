<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('service', ChoiceType::class, [
                'choices' => [
                    'Nurse' => 'nurse',
                    'Physiotherapist' => 'kine',
                    'Babby Sitter' => 'babby sitters',
                    'Caregiver' => 'caregiver'
                ],
                'label' => 'Service'
            ])
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'required' => false,
                
                'empty_data' => null, // Set empty data to null
                'html5' => true,
                'attr' => ['min' => (new \DateTime())->format('Y-m-d\TH:i')],
            ])
            ->add('email', EmailType::class, [
                'required' => false,
                'empty_data' => null, // Set empty data to null
            ])
            ->add('latitude', HiddenType::class)
            ->add('longitude', HiddenType::class)
            ->add('save', SubmitType::class, ['label' => 'Book Appointment']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}