<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
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
                'label' => 'Date et heure de la sortie'
            ])
            ->add('dateLimiteInscription', DateTimeType::class, [
                'label'=> 'Date limite d\'inscription'
            ])
            ->add('nbInscriptionsMax', IntegerType::class, [
                'label'=>'Nombre de places'
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée'
            ])
            ->add('infosSortie', TextareaType::class, [
                'label'=>'Description et infos'
            ])
            ->add('lieu', EntityType::class, [
                //TODO details du lieu ac adresse et données gps
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
