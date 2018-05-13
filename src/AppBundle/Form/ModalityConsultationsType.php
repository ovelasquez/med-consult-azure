<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModalityConsultationsType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('tag')
                ->add('name')
                ->add('resume')
                ->add('description', 'textarea', array('attr' => array('class' => 'tinymce'),))
                ->add('price')

        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\ModalityConsultations'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_modalityconsultations';
    }

}
