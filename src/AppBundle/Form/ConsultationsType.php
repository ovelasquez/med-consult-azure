<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;


class ConsultationsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('modalityConsultation', 'entity_id', array('class' => 'AppBundle\Entity\ModalityConsultations','required'=>true,))
            ->add('specialty','entity', array(
                'class' => 'AppBundle:Specialties',
                'choice_label' => 'name',
                'placeholder'=>'Elige la especialidad',
                'label'=>'Especialidad',
                'required'=>true,
            ))
            ->add('physician','entity', array(
                'class' => 'AppBundle:Physicians',
                'choice_label' => 'user.name',
                'placeholder'=>'Elige tu doctor',
                'label'=>'Doctor',
                'required'=>true,
            ))
            ->add('question','textarea',array('required'=>true,'label'=>'Pregunta')) 
            ->add('creationDate','text',array('required'=>false,'label'=>false))

            ->add('tlf','text', array(
        'attr' => array(
             'placeholder' => 'INGRESE SU TELEFONO FIJO',
        ),
        'label' => false,
        'required'=>true,
     )
)
            ->add('patient', 'entity_id', array('class' => 'AppBundle\Entity\Patients',))
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
