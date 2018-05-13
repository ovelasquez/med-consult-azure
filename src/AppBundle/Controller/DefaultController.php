<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\FormPartners;
use AppBundle\Form\FormPartnersType;
use AppBundle\Entity\PrePhysicians;
use AppBundle\Form\PrePhysiciansType;

class DefaultController extends Controller {

    /**
     * @Route("/", name="homepage")
     * 
     * 
     */
    public function indexAction(Request $request) {
//        $tag = "home";
//        $em = $this->getDoctrine()->getManager();
//        $banners = $em->getRepository('AppBundle:Contents')->findBy(
//                array('status' => 1, 'tag' => 'banner')
//        );
//
//        $cuadros = $em->getRepository('AppBundle:Contents')->findOneBy(
//                array('status' => 1, 'tag' => 'home-cuadros')
//        );
//        $footer = $em->getRepository('AppBundle:Contents')->findOneBy(
//                array('status' => 1, 'tag' => 'home-footer')
//        );
//
//        return $this->render('default/index.html.twig', array(
//                    'banners' => $banners,
//                    'cuadros' => $cuadros,
//                    'footer' => $footer,
//                    'tag' => $tag
//        ));
        
        return $this->redirect($this->generateUrl('fos_user_security_login'));
    }

    /**
     * @Route("/consultas-servicios", name="consultasservicios")
     * 
     * 
     */
    public function consultasserviciosAction($tag = "cons-serv-item") {

        $em = $this->getDoctrine()->getManager();
        $contents = $em->getRepository('AppBundle:Contents')->findBy(
                array('status' => 1, 'tag' => $tag), array('weight' => 'DESC')
        );

        return $this->render('default/services.html.twig', array(
                    'contents' => $contents,
                    'tag' => $tag
        ));
    }

    /**
     * @Route("/acerca-medeconsult", name="acercamedeconsult")
     * 
     * 
     */
    public function acercamedeconsultAction($tag = "acerca-medeconsult") {

        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => $tag)
        );

        $submenu = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => 'menu-acerca-medecons')
        );
        $submenu->setProcBody($this->generateUrl('homepage'));      

        return $this->render('default/content.html.twig', array(
                    'content' => $content,
                    'tag' => $tag,
                    'submenu' => $submenu,
                    'subtag' => 'profesionales-salud',
        ));
    }

    /**
     * @Route("/profesionales-salud", name="profesionalessalud")
     * 
     * 
     */
    public function profesionalessaludAction($tag = "profesionales-salud") {

        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => $tag)
        );

        $entity = new FormPartners();
        $form = $this->createForm(new FormPartnersType(), $entity, array(
            'action' => $this->generateUrl('formpartners_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        $entity_prep = new PrePhysicians();
        $form_prep = $this->createForm(new PrePhysiciansType(), $entity_prep, array(
            'action' => $this->generateUrl('prephysicians_create'),
            'method' => 'POST',
        ));

        $form_prep->add('submit', 'submit', array('label' => 'Crear'));

        $submenu = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => 'menu-acerca-medecons')
        );
        $submenu->setProcBody($this->generateUrl('homepage'));      

        return $this->render('default/profesionales-salud.html.twig', array(
                    'content' => $content,
                    'tag' => $tag,
                    'form' => $form->createView(),
                    'form_prep' => $form_prep->createView(),
                    'submenu' => $submenu,
                    'subtag' => 'profesionales-salud',
        ));
    }

    /**
     * @Route("/como-funciona", name="comofunciona")
     * 
     * 
     */
    public function comofuncionaAction($tag = "como-funciona") {

        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => $tag)
        );

        return $this->render('default/content.html.twig', array(
                    'content' => $content,
                    'tag' => $tag
        ));
    }

    /**
     * @Route("/adm", name="admpage")
     */
    public function indexadmAction(Request $request) {
        // replace this example code with whatever you need
        return $this->render('default/indexadm.html.twig', array(
                    'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
        ));
    }

    /**
     * @Route("/conoce-mas", name="registro_conoce")
     */
    public function registroAction() {
        // replace this example code with whatever you need
        return $this->render('default/registro.html.twig', array(
                    'tag' => 'acerca-medeconsult',
                    'content' => '',
        ));
    }
    
    /**
     * @Route("/terminos-condiciones", name="terminos")
     * 
     * 
     */
    public function terminosAction() {

        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => "terminos")
        );

        return $this->render('default/terminos.html.twig', array(
                    'content' => $content,                    
        ));
    }
    
    /**
     * @Route("/terminos-condiciones-medicos", name="terminosmed")
     * 
     * 
     */
    public function terminosmedAction() {

        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => "terminosmed")
        );

        return $this->render('default/terminosmed.html.twig', array(
                    'content' => $content,                    
        ));
    }
    
    /**
     * @Route("/telesalvavidas", name="telesalvavidas")
     * 
     * 
     */
    public function telesalvavidasAction() {

        $em = $this->getDoctrine()->getManager();
        $content = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => "telesalvavidas")
        );

        return $this->render('default/telesalvavidas.html.twig', array(
                    'content' => $content,                    
        ));
    }

}
