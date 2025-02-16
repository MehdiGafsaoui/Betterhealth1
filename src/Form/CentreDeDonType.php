<?php

namespace App\Form;

use App\Entity\CentreDeDon;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CentreDeDonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du centre',
                'attr' => [
                    'placeholder' => 'Entrez le nom du centre',
                    'minlength' => 4
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
                'attr' => [
                    'placeholder' => 'Entrez l\'adresse du centre'
                ]
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone',
                'attr' => [
                    'placeholder' => 'Ex: +21612345678'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'required' => false,
                'attr' => [
                    'placeholder' => 'exemple@exemple.com'
                ]
            ])
            ->add('createdAt', DateTimeType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => 'YYYY-MM-DD HH:MM:SS'
                ],
                'required' => false, // Make it optional
                'empty_data' => null, // Set empty data to null
            ]);
    }        

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CentreDeDon::class,
        ]);
    }
}
