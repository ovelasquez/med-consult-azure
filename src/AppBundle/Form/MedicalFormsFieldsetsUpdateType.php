<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MedicalFormsFieldsetsUpdateType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('label')
                ->add('page', 'entity', array(
                    'class' => 'AppBundle:MedicalFormsFieldsets',
                    'query_builder' => function (EntityRepository $er) use ($options) {
                        return $er->createQueryBuilder('f')
                                ->where('f.type = :typ AND f.medicalForm = :idf')
                                ->setParameter('idf', $options['idf'])
                                ->setParameter('typ', "page");
                    },
                    'choice_label' => 'label', 'placeholder' => 'Seleccione', 'label' => 'Pagina', 'required' => false,
                ))
                ->add('position')
                ->add('className')
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\MedicalFormsFieldsets',
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
