<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('body', 'textarea', array('attr' => array('class' => 'tinymce'),))
            ->add('file','file', array('attr' => array('class' => 'files'),'required'  => false))
            ->add('tag')
            ->add('status', 'checkbox', array('value'     => 0,'required'  => false))
            ->add('weight', null, array('label' => 'Orden'))
            //->add('created')
            //->add('changed')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contents'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_contents';
    }
}
