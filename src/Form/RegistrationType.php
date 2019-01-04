<?php

namespace App\Form;

use App\Entity\User;
use App\Form\ApplicationType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RegistrationType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', TextType::class, $this->getConfig("Prénom","Entrez votre prénom"))
            ->add('lastName',TextType::class, $this->getConfig("Nom","Entrez votre nom"))
            ->add('email',EmailType::class, $this->getConfig("Email","Entrez votre adresse E-mail"))
            ->add('picture',UrlType::class , $this->getConfig("Avatar","Donnez votre avatar (optionnel)"))
            ->add('hash', PasswordType::class,$this->getConfig("Mot de passe", "Donnez un bon mot de passe"))
            ->add('password', PasswordType::class,$this->getConfig("Confirmation de mot de passe", "Confirmez votre mot de passe"))
            ->add('introduction', TextType::class, $this->getConfig("Introduction","Entrez une phrase courte"))
            ->add('description',TextareaType::class, $this->getConfig("Description", "Donnez une description de vous"))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
