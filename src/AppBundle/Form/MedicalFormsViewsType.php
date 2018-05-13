<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Specialties;
use AppBundle\Form\MedicalFormsFieldsetsType;

class MedicalFormsViewsType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', null, array('label' => 'Nombre'))
                ->add('medicalForm', 'entity_id', array('class' => 'AppBundle\Entity\MedicalForms',))
                ->add('specialty', 'entity', array(
                    'class' => 'AppBundle:Specialties',
                    'choice_label' => 'name',
                    'placeholder' => 'Seleccione',
                    'label' => 'Especialidad',
                ))
//                ->add('fields', 'hidden')
//                ->add('fieldsets', 'hidden')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MedicalFormsViews'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_medicalforms_views';
    }

}
