<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use \AppUserBundle\Form\RegistrationType;
use \AppBundle\Entity\Physicians;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaType;

class PhysiciansType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $pEntity = $builder->getForm()->getData();
        $builder
            ->add('jobtitle', null, array('label' => 'form.jobtitle', 'translation_domain' => 'AppBundle'))
            ->add('education', null, array( 'label' => 'form.education', 'translation_domain' => 'AppBundle'))
            ->add('abms', null, array('label' => 'form.abms', 'translation_domain' => 'AppBundle'))
            ->add('university', null, array('label' => 'form.university', 'translation_domain' => 'AppBundle'))
            ->add('postalcode', null, array('label' => 'form.postalcode', 'translation_domain' => 'AppBundle'))
            ->add('specialty','entity', array(
                'class' => 'AppBundle:Specialties',
                'choice_label' => 'name',
                'placeholder'=>'Seleccione',
                'label'=>'form.specialty', 'translation_domain' => 'AppBundle'
            ))
            ->add('subspecialty', null, array('label' => 'form.subspecialty', 'translation_domain' => 'AppBundle'))
            ->add('research', null, array('label' => 'form.research', 'translation_domain' => 'AppBundle'))
            ->add('languages', null, array('label' => 'form.languages', 'translation_domain' => 'AppBundle'))
            ->add('postalcode', null, array('label' => 'form.postalcode', 'translation_domain' => 'AppBundle'))
            ->add('phone', null, array('label' => 'form.phone', 'translation_domain' => 'AppBundle'))
            //->add('file','file', array('attr' => array('class' => 'files'),'label' => 'form.photo', 'translation_domain' => 'AppBundle','required'  => true))      
            ->add('photo', 'comur_image', array(
                'uploadConfig' => array(
                        'uploadRoute' => 'comur_api_upload', //optional
                        'uploadUrl' => $pEntity->getUploadRootDir(), // required - see explanation below (you can also put just a dir path)
                        'webDir' => 'consultas-medicas/web/'.$pEntity->getUploadDir(), // required - see explanation below (you can also put just a dir path)
                        'fileExt' => '*.jpg;*.gif;*.png;*.jpeg', //optional
                        'libraryDir' => null, //optional
                        'libraryRoute' => 'comur_api_image_library', //optional
                        'showLibrary' => false, //optional
                        'saveOriginal' => 'file'           //optional
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
                    )
                ))
                ->add('prePhysician', 'entity_id', array(
            'class' => 'AppBundle\Entity\PrePhysicians',
            ))   
            ->add('user',new RegistrationType())
            ->add('timezone', 'timezone',array( 'label' => 'Zona horaria'))
            ->add('biography','textarea',array('required'=>false,'label'=>'Biografía')) 
            ->add('linkedinProfile','text',array('required'=>false,'label'=>'Perfil de LinkedIn'))
            ->add('webSite','text',array('required'=>false,'label'=>'Sitio Web'))
            ->add('volunteeringVzla', 'checkbox', array('value'     => 0,'required'  => false))
            ->add('recaptcha', EWZRecaptchaType::class, array(
                    'required' => true, 'language' => 'es'
                        // ...
                ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Physicians'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'appbundle_physicians';
    }
}
