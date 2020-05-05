<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password',RepeatedType::class,[
        'type' => PasswordType::class,
        'invalid_message' => 'Les mots de passe doivent être identiques.',
        'options' => [
            'attr' => ['class' => 'form-control']
        ],
        'required' => true,
        'first_options'  => ['label' => 'Nouveau mot de passe'],
        'second_options' => ['label' => 'Répéter le mot de passe'],
    ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
