<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \AppUserBundle\Form\RegistrationEditType;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class PatientsEditType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $pEntity = $builder->getForm()->getData();
        $builder
                ->add('gender', 'choice', array(
                    'placeholder' => 'Seleccione',
                    'choices' => array(
                        'female' => 'Femenino',
                        'male' => 'Masculino'
                    ), 'required' => true, 'attr' => array('class' => 'typeField'), 'label' => 'Sexo',
                ))
                ->add('address1', null, array('label' => 'form.address1', 'translation_domain' => 'AppBundle'))
                ->add('address2', null, array('required' => false, 'label' => 'form.address2', 'translation_domain' => 'AppBundle'))
                ->add('locality', null, array('label' => 'form.locality', 'translation_domain' => 'AppBundle'))
                ->add('province', null, array('label' => 'form.province', 'translation_domain' => 'AppBundle'))
                ->add('country', 'country', array('label' => 'form.country', 'translation_domain' => 'AppBundle',
                    'attr' => [
                        'class' => 'input-country'
                    ]
                ))
                ->add('postalcode', null, array('label' => 'form.postalcode', 'translation_domain' => 'AppBundle'))
                ->add('phone', null, array('label' => 'form.phone', 'translation_domain' => 'AppBundle'))
                ->add('website', 'url', array('required' => false, 'label' => 'form.website', 'translation_domain' => 'AppBundle'))
                ->add('birthdate', 'date', [
                    'widget' => 'single_text',
                    'format' => 'dd-MM-yyyy',
                    'attr' => [
                        'class' => 'form-control input-inline datepicker',
                        'data-provide' => 'datepicker',
                        'data-date-format' => 'dd-mm-yyyy'
                    ],
                    'label' => 'form.birthdate', 'translation_domain' => 'AppBundle'
                ])
                ->add('user', new RegistrationEditType())
                ->add('photo', 'comur_image', array(
                    'uploadConfig' => array(
                        'uploadRoute' => 'comur_api_upload', //optional
                        'uploadUrl' => $pEntity->getUploadRootDir(), // required - see explanation below (you can also put just a dir path)
                        'webDir' =>  'consultas-medicas/web/'.$pEntity->getUploadDir(), // required - see explanation below (you can also put just a dir path)
                        'fileExt' => '*.jpg;*.gif;*.png;*.jpeg', //optional
                        'libraryDir' => null, //optional
                        'libraryRoute' => 'comur_api_image_library', //optional
                        'showLibrary' => false //optional
                        
                    ),
                    'cropConfig' => array(
                        'minWidth' => 240,
                        'minHeight' => 300,
                        'aspectRatio' => true, //optional
                        'cropRoute' => 'comur_api_crop', //optional
                        'forceResize' => false, //optional
                        'thumbs' => array(//optional
                            array(
                                'maxWidth' => 120,
                                'maxHeight' => 150,
                                'useAsFieldImage' => true  //optional
                            )
                        )
                    ),
                    'required' => false,
                    
                ))
                ->add('timezone', 'timezone',array( 'label' => 'Zona horaria'))
                ->add('recaptcha', EWZRecaptchaType::class, array(
                    'required' => true,
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
            'data_class' => 'AppBundle\Entity\Patients'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'appbundle_patients_edit';
    }

}
