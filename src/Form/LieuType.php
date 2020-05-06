<?php

namespace App\Form;

use    App\Entity\Lieu;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LieuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class,[
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'Nom du lieu'
                ]
            ])
            ->add('rue',TextType::class,[
                'required'=>true,
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'Rue'
                ]
            ])
            ->add('lieu_ville',EntityType::class,[
                'class'=>Ville::class,
                'choice_label'=>'nom',
                'multiple'=>false,
                'expanded'=>false,
                'required'=>true,
                'label'=>'ville',
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'recherche par nom de sortie'

                ]
            ])
            ->add('latitude',TextType::class,[
                'required'=>true,
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'latitude',
                    'hidden'=>true
                ]
            ])
            ->add('longitude',TextType::class,[
                'required'=>true,
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control',
                    'placeholder'=>'longitude',
                    'hidden'=>true
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Lieu::class,
        ]);
    }
}
