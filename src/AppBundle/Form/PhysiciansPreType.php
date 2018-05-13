<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \AppUserBundle\Form\RegistrationType;

class PhysiciansType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('jobtitle', null, array('label' => 'form.jobtitle', 'translation_domain' => 'AppBundle'))
                ->add('education', null, array('required' => false, 'label' => 'form.education', 'translation_domain' => 'AppBundle'))
                ->add('abms', null, array('label' => 'form.abms', 'translation_domain' => 'AppBundle'))
                ->add('university', null, array('label' => 'form.university', 'translation_domain' => 'AppBundle'))
                ->add('postalcode', null, array('label' => 'form.postalcode', 'translation_domain' => 'AppBundle'))
                ->add('specialty', null, array('label' => 'form.subspecialty', 'translation_domain' => 'AppBundle'))
                ->add('subspecialty', null, array('label' => 'form.subspecialty', 'translation_domain' => 'AppBundle'))
                ->add('research', null, array('label' => 'form.research', 'translation_domain' => 'AppBundle'))
                ->add('languages', null, array('label' => 'form.languages', 'translation_domain' => 'AppBundle'))
                ->add('postalcode', null, array('label' => 'form.postalcode', 'translation_domain' => 'AppBundle'))
                ->add('phone', null, array('label' => 'form.phone', 'translation_domain' => 'AppBundle'))
                ->add('file', 'file', array('attr' => array('class' => 'files'), 'label' => 'form.photo', 'translation_domain' => 'AppBundle', 'required' => true))
                ->add('user', new RegistrationType())
                ->add('biography', 'textarea', array('required' => false, 'label' => 'Biografía'))
                ->add('linkedinProfile', 'text', array('required' => false, 'label' => 'Perfil de LinkedIn'))
                ->add('webSite', 'text', array('required' => false, 'label' => 'Sitio Web'))
                ->add('volunteeringVzla', 'checkbox', array('value' => 0, 'required' => false))
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
        return 'appbundle_physicians';
    }

}
