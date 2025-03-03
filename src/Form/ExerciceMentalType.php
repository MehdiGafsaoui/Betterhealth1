<?php

namespace App\Form;

use App\Entity\ExerciceMental;
use App\Entity\Therapie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
class ExerciceMentalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('videoUrl', TextType::class,[
                'label'=>'URL de la video',
                'attr'=>[
                    'class' => 'form-control',
                    'placeholder' => 'Entrez l\'URL de la video'
                ]
            ])
            ->add('titre', TextType::class,[
                'label'=>'Titre',
                'attr'=>[
                    'class' => 'form-control',
                    'placeholder' => 'Entrez le titre de l\'exercice'
                ]
                    ])
            ->add('description', TextareaType::class,[
                'label'=>'Description',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez la description',
                    'rows' => 4
                ]
            ])
            ->add('dureeMinutes', IntegerType::class,[
                'label'=>'Durée (en minutes)',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez la durée',
                    'min'=>1
                ],
                'constraints'=>[
                    new Assert\NotBlank(['message'=>'la durée est obligatoire.']),
                    new Assert\Positive(['message'=> 'La duree doit etre un nombre positif']),
                    new Assert\Type([
                        'type' => 'integer',
                        'message'=> 'La duree doit etre un entier'
                    ])
                    ],
                    'invalid_message'=>'La duree doit etre un entier'
            ])
            ->add('objectif', TextareaType::class,[
                'label'=>'Objectif',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez l\'objectif',
                    'rows' => 3
                ]
            ])
            ->add('therapie', EntityType::class, [
                'class' => Therapie::class,
                'choice_label' => 'nom',
                'label'=> 'Thérapie associée',
                'placeholder'=> 'Sélectionnez une thérapie',
                'attr'=>['class'=> 'form-control']
            ])
            ->add('submit',SubmitType::class,[
                'label'=> 'Enregistrer l\'exercice',
                'attr'=> ['class' => 'btn btn-primary mt-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExerciceMental::class,
        ]);
    }
}
