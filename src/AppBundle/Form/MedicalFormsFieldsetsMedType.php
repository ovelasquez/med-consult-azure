<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\MedicalFormsFieldsets;

class MedicalFormsFieldsetsMedType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('label')
                ->add('medicalForm', 'entity_id', array('class' => 'AppBundle\Entity\MedicalForms',))
                ->add('type', HiddenType::class, array(
                    'data' => 'fieldset',
                ))
                ->add('page', 'entity_id', array('class' => 'AppBundle\Entity\MedicalFormsFieldsets',))
                ->add('physician', HiddenType::class, array(
                    'data' => '',
                ))
                ->add('position','entity', array(
                    'class' => 'AppBundle:MedicalFormsFieldsets',
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('f')
                                ->where('f.type = :typ AND f.page = :idp AND  f.medicalForm = :idf')
                                ->setParameter('idf', $options['idf'])
                                ->setParameter('idp', $options['idfp'])
                                ->setParameter('typ', "fieldset")
                                ->orderBy('f.position', 'ASC');
                    },
                    'choice_label' => 'label',
                    'placeholder' => 'Después de', 
                    'label' => 'Después de ', 
                    'required' => false,
                    'choice_value' => function (MedicalFormsFieldsets $entity = null) {
                        return $entity ? $entity->getPosition() : 0;
                    },
                ))
                //->add('className')
        // ->add('medicalForm')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MedicalFormsFieldsets',
            'idfp' => '',
            'idf' => '',
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_medicalformsfieldsets';
    }

}
