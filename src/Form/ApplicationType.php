<?php
namespace App\Form;

use Symfony\Component\Form\AbstractType;

class ApplicationType extends AbstractType
{
    /**
     * renvoi un tableau de configuration de base
     *
     * @param string $label
     * @param string $placeholder
     * @return array
     */    
    protected function getConfig($label,$placeholder)
    {
        return['label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder
            ]
            ];
    }
}
?>