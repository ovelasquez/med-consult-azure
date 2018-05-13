<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\ModalityConsultations;
use AppBundle\Form\ModalityConsultationsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * ModalityConsultations controller.
 *
 * @Route("/modalityconsultations")
 */
class ModalityConsultationsController extends Controller
{

    /**
     * Lists all ModalityConsultations entities.
     *
     * @Route("/", name="modalityconsultations")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:ModalityConsultations')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new ModalityConsultations entity.
     *
     * @Route("/", name="modalityconsultations_create")
     * @Method("POST")
     * @Template("AppBundle:ModalityConsultations:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function createAction(Request $request)
    {
        $entity = new ModalityConsultations();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('modalityconsultations_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a ModalityConsultations entity.
     *
     * @param ModalityConsultations $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ModalityConsultations $entity)
    {
        $form = $this->createForm(new ModalityConsultationsType(), $entity, array(
            'action' => $this->generateUrl('modalityconsultations_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear','attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }

    /**
     * Displays a form to create a new ModalityConsultations entity.
     *
     * @Route("/new", name="modalityconsultations_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function newAction()
    {
        $entity = new ModalityConsultations();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a ModalityConsultations entity.
     *
     * @Route("/{id}", name="modalityconsultations_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:ModalityConsultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ModalityConsultations entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing ModalityConsultations entity.
     *
     * @Route("/{id}/edit", name="modalityconsultations_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:ModalityConsultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ModalityConsultations entity.');
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
    * Creates a form to edit a ModalityConsultations entity.
    *
    * @param ModalityConsultations $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ModalityConsultations $entity)
    {
        $form = $this->createForm(new ModalityConsultationsType(), $entity, array(
            'action' => $this->generateUrl('modalityconsultations_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update','attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }
    /**
     * Edits an existing ModalityConsultations entity.
     *
     * @Route("/{id}", name="modalityconsultations_update")
     * @Method("PUT")
     * @Template("AppBundle:ModalityConsultations:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:ModalityConsultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ModalityConsultations entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('modalityconsultations_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a ModalityConsultations entity.
     *
     * @Route("/{id}", name="modalityconsultations_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:ModalityConsultations')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ModalityConsultations entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('modalityconsultations'));
    }

    /**
     * Creates a form to delete a ModalityConsultations entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modalityconsultations_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class' => 'submit btnAdm lila')))
            ->getForm()
        ;
    }
}
