<?php

namespace AppUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name',null,array('label' => 'Nombre'))
                ->add('lastname',null,array('label' => 'Apellido'))
                ->add('_5', 'hidden', array(
                    'mapped' => false
                ))
                ->add('_2', 'hidden', array(
                    'mapped' => false
                ));
    }

    public function getParent()
    {
        return 'fos_user_registration';
    }

    public function getName()
    {
        return 'app_user_registration';
    }
}