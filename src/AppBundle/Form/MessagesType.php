<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class MessagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('message','textarea',array('label'=>'Escribe tu Mensaje',
                    'attr' => array(
                    'class' => 'msgarea')
                ))
            ->add('consultation','entity', array('class' => 
                'AppBundle:Consultations',
                 'property'=>'id',
                 'label'=>false ,
                 'attr'=> array('style'=>'display:none;')))
            ->add('tomsg','entity',
                array('class'=>'AppUserBundle:User', 
                    'property'=>'id',
                     'label'=>false))

        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Messages'
        ));
    }

    public function getName()
    {
        return 'appbundle_messages';
    }
}
