<?php

namespace App\Form;


use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la sortie'
            ])
            ->add('datHeureDebut', DateTimeType::class, [
                'label' => 'Date et heure de la sortie',
                'date_format' => 'dd-MM-yyyy',

            ])
            ->add('dateLimiteInscription', DateType::class, [
                'label'=> 'Date limite d\'inscription',
                'format' => 'dd-MM-yyyy',
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label'=>'Nombre de places'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée en minutes'
            ])
            ->add('infosSortie', TextareaType::class, [
                'label'=>'Description et infos'
            ])
            ->add('lieu', EntityType::class, [
                'class'=>Lieu::class,
                'choice_label'=> 'nom',
                'multiple'=>false,
                'expanded'=>false,
                'label'=> 'lieu',
                'placeholder' => 'Veuillez choisir un lieu'
            ])
            /*->add('enregistrer', SubmitType::class)
            ->add('publier', SubmitType::class)
            //->add('annuler2', ResetType::class)*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
