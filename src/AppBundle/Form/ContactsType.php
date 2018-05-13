<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContactsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email','email', array(
                'label' => 'Correo electrónico*',
                'required'  => true
            ))
            ->add('subject',null, array(
                'label' => 'Asunto*',
                'required'  => true
            ))
            ->add('message',null, array(
                'label' => 'Mensaje*',
                'required'  => true
            ))
            ->add('sendcopia', 'checkbox', array('attr'=>array('class'=>"checker"),'label'=>false,'value'     => 0,'required'  => false))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Contacts'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_contacts';
    }
}
