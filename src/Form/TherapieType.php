<?php

namespace App\Form;

use App\Entity\Therapie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class TherapieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('imageFile', FileType::class, [
            'label' => 'Image (JPG, PNG file)',
            'mapped' => false, // not directly mapped to the Therapie entity
            'required' => false, // adjust as needed; in "new" you might set required => true
            'constraints' => [
                new File([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                    ],
                    'mimeTypesMessage' => 'Please upload a valid image file (JPEG, PNG, GIF)',
                ])
            ],
            'attr' => [
                'class' => 'form-control',
            ],
        ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le nom de la thérapie'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la description',
                    'rows' => 4
                ]
            ])
            ->add('objectif', TextareaType::class, [
                'label' => 'Objectif',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez l\'objectif',
                    'rows' => 3
                ]
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez la durée'
                ]
            ])
            ->add('type', TextType::class, [
                'label' => 'Type',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le type de thérapie'
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter Thérapie',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Therapie::class,
        ]);
    }
}
