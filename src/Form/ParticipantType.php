<?php


namespace App\Form;

use App\Entity\Site;
use App\Entity\Participant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ParticipantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder-> add ( 'avatar' , FileType :: class , [
            'label' => 'Avatar (JPG, PNG ou JPEG)' ,
            'mapped' => false ,
            'required' => false ,
            'constraints' => [
                new File ([
                    'maxSize' => '1024k' ,
                    'mimeTypesMessage' => 'Veuillez upload un format d\'image valide.' ,
                ])
            ],
        ]);
        $builder->add('username', TextType::class, [
            "error_bubbling" => true,
            "trim" => true,
            "label" => "Pseudo :",
            "required" => true,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);
        $builder->add('prenom', TextType::class, [
            "error_bubbling" => true,
            "trim" => true,
            "label" => "Prénom :",
            "required" => true,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);
        $builder->add('nom', TextType::class, [
            "error_bubbling" => true,
            "trim" => true,
            "label" => "Nom :",
            "required" => true,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);
        $builder->add('telephone', TextType::class, [
            "error_bubbling" => true,
            "trim" => true,
            "label" => "Téléphone :",
            "required" => false,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);

        $builder->add('email', EmailType::class, [
            "error_bubbling" => true,
            "trim" => true,
            "label" => "Email :",
            "required" => true,
            'attr' => array('class' => 'form-control form-control-lg'),
        ]);
        $builder->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'invalid_message' => 'Les mots de passe doivent être identiques.',
            'required' => true,
            'options' => ['attr' => ['class' => 'form-control form-control-lg']],
            'first_options'  => ['label' => 'Mot de passe:'],
            'second_options' => ['label' => 'Confirmation:'],
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