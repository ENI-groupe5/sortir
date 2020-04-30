<?php

namespace App\Form;

use App\Entity\Site;
use App\Entity\SortieSearch;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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
                    'class'=>'form-control',
                    'placeholder'=>'recherche par nom de sortie'
                ]
            ])
            ->add('libelle',TextType::class,[
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    'placeholder'=>'search',
                    'class'=>'form-control'
                ]
            ])
            ->add('dateDebut',DateType::class,[
                'widget' => 'single_text',
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('dateFin',DateType::class,[
                'widget' => 'single_text',
                'required'=>false,
                'label'=>false,
                'attr'=>[
                    'class'=>'form-control'
                ]
            ])
            ->add('organisateur',CheckboxType::class,[
               'label' => 'Sorties dont je suis l\'organisateur(trice)',
                'required' =>false,
                'attr' => [
                    'class'=> 'form-check-input'
                ]
            ])
            ->add('inscrit',CheckboxType::class,[
                'label' =>'Sorties auxquelles je suis inscrit(e)',
                'required'=>false,
                'attr' => [
                    'class'=> 'form-check-input'
                ]
            ])
            ->add('noinscrit',CheckboxType::class,[
                'label'=>'Sorties auxuqlles je ne suis pas inscrit(e)',
                'required'=>false,
                'attr' => [
                    'class'=> 'form-check-input'
                ]
            ])
            ->add('past',CheckboxType::class,[
                'label'=>'Sorties passées',
                'required'=>false,
                'attr' => [
                    'class'=> 'form-check-input'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SortieSearch::class,
            'method'=>'GET',
        ]);
    }
}
