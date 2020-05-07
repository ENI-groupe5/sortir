<?php


namespace App\Form;

use App\Entity\Site;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('avatarFile', VichImageType::class, [
            'required' => false,
            'allow_delete' => true,
            'download_uri' => false,
            'image_uri' => false,
            'asset_helper' => true,
            'label'=>'charger un avatar'
        ]);
        $builder->add('username', TextType::class, [
            "label" => "Pseudo",
            "required" => true,
        ]);
        $builder->add('prenom', TextType::class, [
            "label" => "Prénom",
            "required" => true,
        ]);
        $builder->add('nom', TextType::class, [
            "label" => "Nom",
            "required" => true,
        ]);
        $builder->add('telephone', TextType::class, [
            "label" => "Téléphone",
            "required" => false,
        ]);

        $builder->add('email', EmailType::class, [
            "label" => "Email",
            "required" => true,
        ]);
        $builder->add('oldPassword', PasswordType::class, [
            'label' => 'Mot de passe actuel',
            'required' => false,
            'invalid_message' => 'Veuillez indiquer votre mot de passe actuel.',
        ]);
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent être identiques.',
            'required' => false,
            'first_options'  => ['label' => 'Nouveau mot de passe'],
            'second_options' => ['label' => 'Confirmation nouveau mot de passe'],
        ]);
        $builder->add('site',EntityType::class, [
            'required' => true,
            'class'=>Site::class,
            'choice_label'=>'nom',
            'label'=>'Campus',
            'trim'=>true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }

}