<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class PrePhysiciansUpdateType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('firtsName', null, array(
                    'label' => 'Nombre*',
                    'attr' => array("readonly"=>"readonly"),
                ))
                ->add('middleName', null, array(
                    'label' => 'Inicial segundo nombre*',
                    'attr' => array("readonly"=>"readonly"),
                ))
                ->add('lastName', null, array(
                    'label' => 'Apellido*',
                    'attr' => array("readonly"=>"readonly"),
                ))
                ->add('abms', null, array(
                    'label' => 'Board certification (ABMS)*',
                    'attr' => array("readonly"=>"readonly"),
                ))
                ->add('practiceType', null, array(
                    'label' => 'Tipo de práctica*',
                    'attr' => array("readonly"=>"readonly"),
                ))
                ->add('postalCode', null, array(
                    'label' => 'Código postal donde practica*',
                    'attr' => array("readonly"=>"readonly"),
                ))
                ->add('phone', null, array(
                    'label' => 'Teléfono*',
                    'attr' => array("readonly"=>"readonly"),
                ))
                ->add('email', 'email', array(
                    'label' => 'Email*',
                    'attr' => array("readonly"=>"readonly"),
                ))
                ->add('hearAboutUs', null, array( 'attr' => array("readonly"=>"readonly"), 'label' => 'Cómo te enteraste de medeconsult/nombre de persona que te refirió *' ))
                ->add('recaptcha', EWZRecaptchaType::class, array(
                    'language' => 'es'
                    // ...
                ))     
                ->add('status', 'checkbox', array('required'  => false,'label' => 'Habilitar' ))
                
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PrePhysicians'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_prephysicians';
    }

}
