<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends AbstractType
{

    /**
     * renvoi un tableau de configuration de base
     *
     * @param string $label
     * @param string $placeholder
     * @return arry
     */    
    private function getConfig($label,$placeholder)
    {
        return['label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder
            ]
            ];
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfig('Titre','Tappez le titre de votre annonce'))
            ->add('coverImage', UrlType::class, $this->getConfig('Url de l\'image principale','Donnez l\'adresse d\'une image'))
            ->add('introduction', TextType::class, $this->getConfig('Introduction', 'Donnez une description globale de l\'annoce'))
            ->add('content', TextareaType::class, $this->getConfig('Description', 'Donnez une description complete de l\'annonce'))
            ->add('rooms', IntegerType::class,$this->getConfig('Nombre de chambres','Nombre de chambres disponibles'))
            ->add('price', MoneyType::class, $this->getConfig('Prix par nuit', 'Entrez le prix de location par nuit'))
            ->add('images', CollectionType::class, [
                'entry_type'=>ImageType::class,//permet de faire plusieurs formulaires ImageType
                'allow_add'=>true,//autorise le fait d'ajouter un formulaire
                'allow_delete'=>true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
