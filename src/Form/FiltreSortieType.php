<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\SortieSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltreSortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('sites', EntityType::class,[
                'class'=>Site::class,
                'choice_label'=>'nom',
                'multiple'=>false,
                'expanded'=>false,
                'required'=>false,
                'label'=>'sites',
                'attr'=>[
                    'class'=>''
                ]
            ])
            ->add('libelle',TextType::class,[
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'search',
                    #TODO
                    'class'=>''
                ]
            ])
            ->add('dateDebut',DateType::class,[
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    #TODO
                    'class'=>''
                ]
            ])
            ->add('dateFin',DateType::class,[
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    #TODO
                    'class'=>''
                ]
            ])
            ->add('organisateur',CheckboxType::class,[
               'label' => 'Sorties dont je suis l\'organisateur(trice)',
                'required' =>false
            ])
            ->add('inscrit',CheckboxType::class,[
                'label' =>'Sorties auxquelles je suis inscrit(e)',
                'required'=>false
            ])
            ->add('noinscrit',CheckboxType::class,[
                'label'=>'Sorties auxuqlles je ne suis pas inscrit(e)',
                'required'=>false
            ])
            ->add('past',CheckboxType::class,[
                'label'=>'Sorties passées',
                'required'=>false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SortieSearch::class,
        ]);
    }
}
