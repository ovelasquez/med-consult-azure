<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConsultationsEditType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder            
            ->add('answer', 'ckeditor', array ( 'label' => false, 'config_name' => 'my_custom_config', ))
            ->add('resume', 'ckeditor', array ( 'label' => false, 'config_name' => 'my_custom_config', ))        
            
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Consultations'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_consultations';
    }
}
