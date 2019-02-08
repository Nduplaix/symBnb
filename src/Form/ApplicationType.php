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
     * @param array $options
     * @return array
     */    
    protected function getConfig($label,$placeholder, $options = [])
    {
        return array_merge([
            'label'=>$label,
            'attr'=>[
                'placeholder'=>$placeholder,
            ]
            ],$options);
    }
}
?>