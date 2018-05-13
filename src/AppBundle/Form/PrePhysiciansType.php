<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class PrePhysiciansType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('firtsName', null, array(
                    'label' => 'Nombre*',
                    'required' => true
                ))
                ->add('middleName', null, array(
                    'label' => 'Inicial segundo nombre*',
                    'required' => true
                ))
                ->add('lastName', null, array(
                    'label' => 'Apellido*',
                    'required' => true
                ))
                ->add('abms', null, array(
                    'label' => 'Board certification (ABMS)*',
                    'required' => true
                ))
                ->add('practiceType', null, array(
                    'label' => 'Tipo de práctica*',
                    'required' => true
                ))
                ->add('postalCode', null, array(
                    'label' => 'Código postal donde practica*',
                    'required' => true
                ))
                ->add('phone', null, array(
                    'label' => 'Teléfono*',
                    'required' => true
                ))
                ->add('email', 'email', array(
                    'label' => 'Email*',
                    'required' => true
                ))
                ->add('hearAboutUs', null, array( 'required' => true, 'label' => 'Cómo te enteraste de medeconsult/nombre de persona que te refirió *' ))
                ->add('recaptcha', EWZRecaptchaType::class, array(
                    'language' => 'es'
                    // ...
                ))               
                
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
