<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use AppBundle\Entity\Specialties;
use AppBundle\Form\MedicalFormsFieldsetsType;

class MedicalFormsUpdateType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null, array(
                 'label'=>'Nombre',
            ))
//            ->add('form_name','hidden',array(
//                'invalid_message' => 'The Nombre de Sistema ',
//            ))
//            ->add('specialtie','entity', array(
//                'class' => 'AppBundle:Specialties',
//                'choice_label' => 'name',
//                'placeholder'=>'Seleccione',
//                'label'=>'Especialidad',
//            ))
//            ->add('fieldsets',new MedicalFormsFieldsetsIniType(), array(
//                 'label'=>'Grupo inicial de campos',
//            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MedicalForms'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_medicalforms';
    }
}
