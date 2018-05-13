<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \AppUserBundle\Form\RegistrationType;
use \AppBundle\Entity\Physicians;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class PhysiciansEditAvailabilityType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

        $builder
                ->remove('recaptcha')
                ->add('datetimeAvailable', null, array('label' => 'Disponibilidad'))

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Physicians'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_physicians_edit_availability';
    }

}
