<?php

namespace App\Form;

use App\Entity\Evenements;
use App\Entity\Categorieevenement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class EvenementsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'événement',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Nom de l\'événement'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Description de l\'événement'
                ]
            ])
            ->add('Datedebut', DateType::class, [
                'label' => 'Date de début',
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'placeholder' => 'Date de début'
                ]
            ])
            ->add('Datefin', DateType::class, [
                'label' => 'Date de fin',
                'required' => true,
                'widget' => 'single_text',
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d'),
                    'placeholder' => 'Date de fin'
                ]
            ])
            ->add('Lieu', TextType::class, [
                'label' => 'Lieu',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Lieu de l\'événement'
                ]
            ])
            ->add('categorie', null, [
                'label' => 'Catégorie',
                'required' => true,
                'choice_label' => 'nom', 
            ])
            ->add('image', FileType::class, [
                'label' => 'Image de l\'événement (optionnel)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpeg ou png)',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Evenements::class,
        ]);
    }
}
