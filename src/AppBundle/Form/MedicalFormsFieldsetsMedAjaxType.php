<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\MedicalFormsFieldsets;

class MedicalFormsFieldsetsMedAjaxType extends AbstractType {

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
                ->add('position', HiddenType::class, array(
                    'data' => '',
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