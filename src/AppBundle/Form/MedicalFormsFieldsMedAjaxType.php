<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\MedicalFormsFields;

class MedicalFormsFieldsMedAjaxType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', 'hidden')
                ->add('field', 'choice', array(
                    'placeholder' => 'Seleccione',
                    'choices' => array(
                        'text' => 'Pregunta corta',
                        'textarea' => 'Pregunta larga',
                        'check' => 'Pregunta cerrada',
                    ), 'required' => true, 'attr' => array('class' => 'typeField'), 'label' => 'Tipo de pregunta',
                ))
                ->add('label', null, array('label' => 'Pregunta'))
                ->add('data', 'textarea', array('label' => 'Lista de valores permitidos', 'attr' => array('class' => 'tinymce'), 'required' => false,))
                ->add('medicalFormsFieldset', 'entity_id', array('class' => 'AppBundle\Entity\MedicalFormsFieldsets',))
                /*->add('subgroup', 'entity', array(
                    'class' => 'AppBundle:MedicalFormsFields',
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('f')
                                ->where('(f.field = :lab OR f.field = :grid) AND f.medicalFormsFieldset = :idset')
                                ->setParameter('idset', $options['ids'])
                                ->setParameter('lab', "group")
                                ->setParameter('grid', "grid");
                    },
                    'choice_label' => 'label', 'placeholder' => 'Seleccione', 'label' => 'Grupo o Rejilla', 'required' => false,
                ))*/
                ->add('subgroup', 'entity_id', array('class' => 'AppBundle\Entity\MedicalFormsFields',))
                //->add('orderid', null, array('label' => 'Orden', 'data' => '0'))
                ->add('required', 'checkbox', array('value' => 0, 'required' => false, 'label' => 'Requerido'))
                ->add('showlabel', HiddenType::class, array(
                    'data' => '1',
                ))
                ->add('physician', HiddenType::class, array(
                    'data' => '',
                ))
                ->add('orderid', HiddenType::class, array(
                    'data' => '',
                ))            
                /*->add('orderid', 'entity', array(
                    'class' => 'AppBundle:MedicalFormsFields',
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('f')
                                ->where('f.medicalFormsFieldset = :idset')
                                ->setParameter('idset', $options['ids'])
                                ->orderBy('f.orderid', 'ASC');
                    },
                    'choice_label' => 'label',
                    'placeholder' => 'Después de',
                    'label' => 'Seleccione ',
                    'required' => false,
                    'choice_value' => function (MedicalFormsFields $entity = null) {
                        return $entity ? $entity->getOrderid() : 0;
                    },
        ))*/
            ;

        
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MedicalFormsFields',
            'ids' => '',
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_medicalformsfields';
    }

}