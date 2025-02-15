<?php

namespace App\Form;

use App\Entity\Therapie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\Regex;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
class TherapieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('image', TextType::class,[
                'label'=>'URL de l\'image',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez l\'URL de l\'image'
                ],
                'constraints'=>[
                    new Assert\NotBlank(['message'=>'Ce champ est obligatoire.'])
                ]
            ])
            ->add('nom', TextType::class,[
                'label'=>'Nom',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez le nom de la thérapie'
                ],
                'constraints'=>[
                    new Assert\NotBlank(['message'=>'Ce champ est obligatoire.']),
                    new Assert\Length([
                        'min' => 3,
                        'minMessage' => 'Le nom doit comporter au moins 3 caractères'
                    ])
                ]
            ])
            ->add('description', TextareaType::class,[
                'label'=>'Description',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez la description',
                    'rows' => 4
                ],
                'constraints'=>[
                    new Assert\NotBlank(['message'=>'La description est obligatoire.']),
                    new Assert\Length([
                        'min' => 10,
                        'minMessage' => 'La description doit comporter au moins 10 caractères'
                    ])
                ]
            ])
            ->add('objectif', TextareaType::class,[
                'label'=>'Objectif',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez l\'objectif',
                    'rows' => 3
                ],
                'constraints'=>[
                    new Assert\NotBlank(['message'=>'L\'objectif est obligatoire.']),
                    new Assert\Length([
                        'min' => 5,
                        'minMessage' => 'L\'objectif doit comporter au moins 5 caractères'
                    ])
                ]
            ])
            ->add('duree', IntegerType::class,[
                'label'=>'Durée',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez la durée'
                ],
                'constraints'=>[
                    new Assert\NotBlank(['message'=>'la durée est obligatoire.']),
                    new Assert\Regex([
                        'pattern' => '/^\d+$/',
                        'message' => 'La durée doit etre un nombre entier positif'
                    ])
                ]
            ])
            ->add('type', TextType::class,[
                'label'=>'Type',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=> 'Entrez le type de therapie'
                ],
                'constraints'=>[
                    new Assert\NotBlank(['message'=>'le type est obligatoire.']),
                ]
            ])
            ->add('submit',SubmitType::class,[
                'label'=> 'Ajouter Thérapie',
                'attr'=> ['class' => 'btn btn-primary mt-3']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Therapie::class,
        ]);
    }
}
