<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PatientsShareMedicalHistoryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','email',array('label' => 'Correo de destinatario'))
            ->add('name',null,array('label' => 'Nombre'))
            ->add('message','textarea',array('label' => 'Mensaje'))
            ->add('available','integer',array('label' => 'Días disponible'))            
            ->add('patient', 'entity_id', array(
            'class' => 'AppBundle\Entity\Patients',
            ))  
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\PatientsShareMedicalHistory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_patientssharemedicalhistory';
    }
}
