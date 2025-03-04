<?php

namespace App\Form;

use App\Entity\CentreDeDon;
use App\Entity\DemandeDonSang;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class DemandeDonSangType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $bloodTypes = [
            'A+' => 'A+',
            'A-' => 'A-',
            'B+' => 'B+',
            'B-' => 'B-',
            'AB+' => 'AB+',
            'AB-' => 'AB-',
            'O+' => 'O+',
            'O-' => 'O-',
        ];

        $statusChoices = [
            'Pending' => 'pending',
            'Refused' => 'refused',
            'Accepted' => 'accepted',  // Optionally add more status options if required
        ];

        $builder
            ->add('groupesanguain', ChoiceType::class, [
                'choices' => $bloodTypes,
                'label' => 'Blood Type',
                'placeholder' => 'Choose a blood type', // Optional: Add a placeholder
            ])
            ->add('quantite')
            ->add('status', ChoiceType::class, [
                'choices' => $statusChoices,
                'label' => 'Status',
                'placeholder' => 'Choose a status',
                'data' => 'pending',
            ])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('CentreDeDon', EntityType::class, [
                'class' => CentreDeDon::class,
                'choice_label' => 'name',
            ])
            ->add('createdAt', DateTimeType::class, [
                'data' => new \DateTime(),
                'widget' => 'single_text',  
                'required' => false,        
                'attr' => ['readonly' => true], 
            ]);

        // Ensure status is set to 'pending' if not already set
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            // If the status is not set, set it to "pending"
            if (!$data || null === $data->getStatus()) {
                $data->setStatus('pending');
                $event->setData($data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => DemandeDonSang::class,
        ]);
    }
}
