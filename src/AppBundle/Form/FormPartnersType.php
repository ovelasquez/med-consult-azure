<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class FormPartnersType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('name', null, array(
                    'label' => 'Nombre de la empresa*',
                    'required' => true
                ))
                ->add('nameContact', null, array(
                    'label' => 'Nombre de contacto dentro de la empresa*',
                    'required' => true
                ))
                ->add('typeCompany', null, array(
                    'label' => 'Tipo de empresa*',
                    'required' => true
                ))
                ->add('address', null, array(
                    'label' => 'Dirección*',
                    'required' => true
                ))
                ->add('phoneNumbers', null, array(
                    'label' => 'Números telefónicos*',
                    'required' => true
                ))
                ->add('email', 'email', array(
                    'label' => 'Email*',
                    'required' => true
                ))
                ->add('hearAboutUs', 'choice', array(
                    'placeholder' => 'Seleccione',
                    'choices' => array(
                        'anuncio' => 'Anuncio',
                        'email' => 'Email/Newsletter',
                        'redes_sociales' => 'Redes sociales',
                        'familia_amigo' => 'Familia o amigo',
                        'articulo_revista' => 'Artículo de revista',
                        'periodico' => 'Periódico',
                        'tv' => 'TV',
                        'pagina_buscador' => 'Página Web - Buscador',
                        'otro' => 'Otro'
                    ), 'required' => true, 'attr' => array('class' => 'typeField'), 'label' => 'Cómo se enteró de MedEConsult',
                ))
                ->add('message', null, array(
                    'label' => 'Mensaje (Describa como cree que podemos colaborar juntos)*',
                    'required' => true
                ))
                ->add('recaptcha', EWZRecaptchaType::class, array(
                    'language' => 'es'
                    // ...
                ))    
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\FormPartners'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_formpartners';
    }

}
