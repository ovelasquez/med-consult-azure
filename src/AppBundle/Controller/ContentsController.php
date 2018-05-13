<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Contents;
use AppBundle\Form\ContentsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Contents controller.
 *
 * @Route("/contents")
 */
class ContentsController extends Controller
{

    /**
     * Lists all Contents entities.
     *
     * @Route("/", name="contents")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Contents')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Contents entity.
     *
     * @Route("/", name="contents_create")
     * @Method("POST")
     * @Template("AppBundle:Contents:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function createAction(Request $request)
    {
        $entity = new Contents();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            $entity->setCreated(new \DateTime("now"));   
            $entity->setWeight(0);     
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('contents_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Contents entity.
     *
     * @param Contents $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Contents $entity)
    {
        $form = $this->createForm(new ContentsType(), $entity, array(
            'action' => $this->generateUrl('contents_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar','attr' => array('class' => 'submit btnAdm rojoFuerte')));
        
      

        return $form;
    }

    /**
     * Displays a form to create a new Contents entity.
     *
     * @Route("/new", name="contents_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function newAction()
    {
        $entity = new Contents();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Contents entity.
     *
     * @Route("/{id}", name="contents_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Contents')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contents entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Contents entity.
     *
     * @Route("/{id}/edit", name="contents_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Contents')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contents entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Contents entity.
    *
    * @param Contents $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Contents $entity)
    {
        $form = $this->createForm(new ContentsType(), $entity, array(
            'action' => $this->generateUrl('contents_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar','attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }
    /**
     * Edits an existing Contents entity.
     *
     * @Route("/{id}", name="contents_update")
     * @Method("PUT")
     * @Template("AppBundle:Contents:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Contents')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contents entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            //return $this->redirect($this->generateUrl('contents_edit', array('id' => $id)));
            return $this->redirect($this->generateUrl('contents_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Contents entity.
     *
     * @Route("/{id}", name="contents_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Contents')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Contents entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contents'));
    }

    /**
     * Creates a form to delete a Contents entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('contents_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class' => 'submit btnAdm lila')))
            ->getForm()
        ;
    }
    
    
    
}
