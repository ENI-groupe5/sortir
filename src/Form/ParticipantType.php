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
        ]);
        $builder->add('username', TextType::class, [
            "trim" => true,
            "label" => "Pseudo",
            "required" => true,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);
        $builder->add('prenom', TextType::class, [
            "trim" => true,
            "label" => "Prénom",
            "required" => true,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);
        $builder->add('nom', TextType::class, [
            "trim" => true,
            "label" => "Nom",
            "required" => true,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);
        $builder->add('telephone', TextType::class, [
            "trim" => true,
            "label" => "Téléphone",
            "required" => false,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);

        $builder->add('email', EmailType::class, [
            "trim" => true,
            "label" => "Email",
            "required" => true,
            'attr' => array('class' => 'form-control form-control-lg'),
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
            'options' => ['attr' => ['class' => 'form-control form-control-lg']],
            'first_options'  => ['label' => 'Nouveau mot de passe'],
            'second_options' => ['label' => 'Confirmation nouveau mot de passe'],
        ]);
        $builder->add('site',EntityType::class, [
            'required' => true,
            'class'=>Site::class,
            'choice_label'=>'nom',
            'label'=>'Campus',
            'trim'=>true,
            'attr'=> array('class'=>'form-control')
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Participant::class,
        ]);
    }

}