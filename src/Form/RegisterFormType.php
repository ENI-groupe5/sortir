<?php

namespace App\Form;

use App\Entity\Participant;
use App\Entity\Site;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class)
            ->add('prenom',TextType::class)
            ->add('telephone',TextType::class,[
                'required'=>false
            ])
            ->add('email',EmailType::class)
            ->add('username',TextType::class,['label'=>'nom d\'utilisateur'])
            ->add('password',RepeatedType::class,[
        'type' => PasswordType::class,
        'invalid_message' => 'Les mots de passe doivent correspondre.',
        'options' => ['attr' => ['class' => 'form-control']],
        'required' => true,
        'first_options'  => ['label' => 'Mot de passe'],
        'second_options' => ['label' => 'Répéter mot de passe'],
    ])
            ->add('avatar',FileType::class,[
                'required'=>false
            ])
            ->add('site',EntityType::class,[
                'class'=>Site::class,
                'label'=>'Site de rattachement',
                'choice_label'=>'nom',
                'placeholder'=>'Sélectionnez un site'
            ])
            ->add('roles',ChoiceType::class,[
                'choices'=>[
                    'ROLE_ADMIN'=>'ROLE_ADMIN',
                    'ROLE_USER'=>'ROLE_USER'
                ],
                'multiple'=>true,
                'empty_data'=>'ROLE_USER',

            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }
}
