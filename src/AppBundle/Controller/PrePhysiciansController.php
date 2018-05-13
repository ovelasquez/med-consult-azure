<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\PrePhysicians;
use AppBundle\Form\PrePhysiciansType;
use AppBundle\Form\PrePhysiciansUpdateType;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * PrePhysicians controller.
 *
 * @Route("/prephysicians")
 */
class PrePhysiciansController extends Controller {

    /**
     * Lists all PrePhysicians entities.
     *
     * @Route("/", name="prephysicians")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();
        
        $twig = $this->container->get('twig');
        $gl = $twig->getGlobals();

        $entities = $em->getRepository('AppBundle:PrePhysicians')->findBy(array(), array('id' => 'desc'), (int) $gl["for_page"]);

        $qb = $em->getRepository('AppBundle:PrePhysicians')->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $count = $qb->getQuery()->getSingleScalarResult();

        return array(
            'entities' => $entities,
            'p' => 1,
            'pCount' => ceil($count / (int) $gl["for_page"])
        );
    }

    /**
     * Creates a new PrePhysicians entity.
     *
     * @Route("/", name="prephysicians_create")
     * @Method("POST")
     * @Template("AppBundle:PrePhysicians:new.html.twig")
     */
    public function createAction(Request $request) {
        //This is optional. Do not do this check if you want to call the same action using a regular request.
//        if (!$request->isXmlHttpRequest()) {
//            return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
//        }

        $entity = new PrePhysicians();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->setDateTime(new \DateTime("now"));
            $entity->setStatus(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();
            
            $message = \Swift_Message::newInstance()
                    ->setSubject('Hemos recibido tu aplicación')
                    ->setFrom('noreply@medeconsult.com')
                    ->setTo($entity->getEmail())
                    ->setBody(
                    $this->renderView(
                            'AppBundle:PrePhysicians:email.welcome.html.twig', array('email'=>$entity->getEmail(),'username' => $entity->getName())
                    ), 'text/html'
            );
            $this->get('mailer')->send($message);

            //return new JsonResponse(array('message' => $entity->getFirtsName()), 200);
            return array(
                'entity' => $entity,                
                'form' => $form->createView(),
                'name' => $entity->getFirtsName()
            );
            //return $this->renderView('AppBundle:PrePhysicians:new.html.twig', array('name' => $entity->getFirtsName()));
        }


        $errors = array();

//        foreach ($form->getErrors() as $key => $error) {
//            if ($form->isRoot()) {
//                $errors['#'][] = $error->getMessage();
//            } else {
//                $errors[] = $error->getMessage();
//            }
//        }
        
         

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
        
        //return $this->renderView('AppBundle:PrePhysicians:new.html.twig', array('entity' => $entity,'form' => $form->createView(),));

//        $response = new JsonResponse(
//                array(
//            'message' => json_encode($errors),
//            'form' => $this->renderView('AppBundle:PrePhysicians:new.html.twig', array(
//                'entity' => $entity,
//                'form' => $form->createView(),
//            ))), 400);
//
//        return $response;
    }

    /**
     * Creates a form to create a PrePhysicians entity.
     *
     * @param PrePhysicians $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PrePhysicians $entity) {
        $form = $this->createForm(new PrePhysiciansType(), $entity, array(
            'action' => $this->generateUrl('prephysicians_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => '<i class="icon-check2"></i>Enviar', 'attr' => array(" class" => "submit btn btn-primary")));

        return $form;
    }

    /**
     * Lists all PrePhysicians entities pagination.
     *
     * @Route("/pag/{p}", name="prephysicians_pag")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function indexPagAction($p) {
        $em = $this->getDoctrine()->getManager();

        $twig = $this->container->get('twig');
        $gl = $twig->getGlobals();

        $entities = $em->getRepository('AppBundle:PrePhysicians')->findBy(array(), array('id' => 'desc'), $gl["for_page"], ((int)$p-1) * $gl["for_page"]);

        $qb = $em->getRepository('AppBundle:PrePhysicians')->createQueryBuilder('a');
        $qb->select('COUNT(a)');        
        $count = $qb->getQuery()->getSingleScalarResult();
        
        return array(
            'entities' => $entities,
            'p' => $p,
            'pCount' => ceil($count/(int)$gl["for_page"])
        );
    }
    
    /**
     * Displays a form to create a new PrePhysicians entity.
     *
     * @Route("/new", name="prephysicians_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new PrePhysicians();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a PrePhysicians entity.
     *
     * @Route("/{id}", name="prephysicians_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:PrePhysicians')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PrePhysicians entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing PrePhysicians entity.
     *
     * @Route("/{id}/edit", name="prephysicians_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:PrePhysicians')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PrePhysicians entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Creates a form to edit a PrePhysicians entity.
     *
     * @param PrePhysicians $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PrePhysicians $entity) {
        $form = $this->createForm(new PrePhysiciansUpdateType(), $entity, array(
            'action' => $this->generateUrl('prephysicians_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        if ($entity->getStatus()) {
            $form->remove('status');
        }
        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array(" class" => "submit btnAdm rojoFuerte")));

        return $form;
    }

    /**
     * Edits an existing PrePhysicians entity.
     *
     * @Route("/{id}", name="prephysicians_update")
     * @Method("PUT")
     * @Template("AppBundle:PrePhysicians:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:PrePhysicians')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PrePhysicians entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {

            if ($entity->getStatus()) {
                $tokenGenerator = $this->get('fos_user.util.token_generator');
                $entity->setConfirmationToken($tokenGenerator->generateToken());
                $url = $this->generateUrl('physicians_register', array('t' => $entity->getConfirmationToken()),true);
                $message = \Swift_Message::newInstance()
                        ->setSubject('¡FELICIDADES! HEMOS APROBADO TU SOLICITUD')
                        ->setFrom('noreply@medeconsult.com')
                        ->setTo($entity->getEmail())
                        ->setBody(
                        $this->renderView(
                                'AppBundle:PrePhysicians:email.html.twig', array('email'=>$entity->getEmail(),'username' => $entity->getName(), 'confirmationUrl' => $url)
                        ),'text/html'
                );

                $this->get('mailer')->send($message);
            }
           
            $em->flush();

            
            return $this->redirect($this->generateUrl('prephysicians_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a PrePhysicians entity.
     *
     * @Route("/{id}", name="prephysicians_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:PrePhysicians')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PrePhysicians entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('prephysicians'));
    }

    /**
     * Creates a form to delete a PrePhysicians entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('prephysicians_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
