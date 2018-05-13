<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\FormPartners;
use AppBundle\Form\FormPartnersType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * FormPartners controller.
 *
 * @Route("/formpartners")
 */
class FormPartnersController extends Controller {

    /**
     * Lists all FormPartners entities.
     *
     * @Route("/", name="formpartners")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     *       
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:FormPartners')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new FormPartners entity.
     *
     * @Route("/", name="formpartners_create")
     * @Method("POST")
     * @Template("AppBundle:FormPartners:new.html.twig")
     */
    public function createAction(Request $request) {

        //This is optional. Do not do this check if you want to call the same action using a regular request.
//        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
//        }

        $entity = new FormPartners();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setDateTime(new \DateTime("now"));
            $entity->setIp($this->get('request')->getClientIp());

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $message = \Swift_Message::newInstance()
                    ->setSubject('Hemos recibido tu datos')
                    ->setFrom('noreply@medeconsult.com')
                    ->setTo($entity->getEmail())
                    ->setBody(
                    $this->renderView(
                            'AppBundle:FormPartners:email.welcome.html.twig', array('email'=>$entity->getEmail(),'username' => $entity->getNameContact())
                    ), 'text/html'
            );
            $this->get('mailer')->send($message);

            //return new JsonResponse(array('message' => 'Success!'), 200);
            return array(
                'entity' => $entity,                
                'form' => $form->createView(),
                'name' => $entity->getName()
            );
        }

//        $errors = $this->get('app.errorsform')->getErrors($form);
        
        $errors=array();
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[] = $child->getErrors()->getChildren()->getMessage();
                 
            }
        }

        return array(
                'entity' => $entity,                
                'form' => $form->createView(),
                'name' => '',
                'messages' => $errors,
            );
        


//        $response = new JsonResponse(
//                array(
//            'message' => json_encode($errors),
//            'form' => $this->renderView('AppBundle:FormPartners:new.html.twig', array(
//                'entity' => $entity,
//                'form' => $form->createView(),
//            ))), 400);
//
//        return $response;
    }

    /**
     * Creates a form to create a FormPartners entity.
     *
     * @param FormPartners $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(FormPartners $entity) {
        $form = $this->createForm(new FormPartnersType(), $entity, array(
            'action' => $this->generateUrl('formpartners_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar','attr'=>  array(" class"=>"submitProfesionales lilaOscuro")));

        return $form;
    }

    /**
     * Displays a form to create a new FormPartners entity.
     *
     * @Route("/new", name="formpartners_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new FormPartners();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a FormPartners entity.
     *
     * @Route("/{id}", name="formpartners_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")      
     * 
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:FormPartners')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find FormPartners entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a FormPartners entity.
     *
     * @Route("/{id}", name="formpartners_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")      
     * 
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:FormPartners')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find FormPartners entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('formpartners'));
    }

    /**
     * Creates a form to delete a FormPartners entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('formpartners_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Eliminar', 'attr' => array(" class" => "submit btnAdm lila")))
                        ->getForm()
        ;
    }

}
