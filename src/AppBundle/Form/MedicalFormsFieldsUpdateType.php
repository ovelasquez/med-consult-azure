<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MedicalFormsFieldsUpdateType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder                
                ->add('label')
                ->add('description','textarea', array('required' => false,'label' => 'Descripción','attr' => array('class' => 'tinymce')))
                ->add('help','textarea', array('required' => false,'label' => 'Mensaje de ayuda','attr' => array('class' => 'tinymce')))
                ->add('data', 'textarea', array(
                    'attr' => array('class' => 'tinymce','label' => 'Lista de valores permitidos'),
                    'required' => false,
                ))
                ->add('orderid',null,array('label' => 'Orden'))
                ->add('required', 'checkbox', array('required' => false,'label' => 'Requerido'))
                ->add('showlabel', 'checkbox', array('required' => false,'label' => 'Mostrar Etiqueta'))              
                
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MedicalFormsFields'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_medicalformsfields';
    }

}
