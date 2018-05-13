<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Specialties;
use AppBundle\Form\SpecialtiesType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


/**
 * Specialties controller.
 *
 * @Route("/specialties")
 */
class SpecialtiesController extends Controller
{

    /**
     * Lists all Specialties entities.
     *
     * @Route("/", name="specialties")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Specialties')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Specialties entity.
     *
     * @Route("/", name="specialties_create")
     * @Method("POST")
     * @Template("AppBundle:Specialties:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function createAction(Request $request)
    {
        $entity = new Specialties();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('specialties_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Specialties entity.
     *
     * @param Specialties $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Specialties $entity)
    {
        $form = $this->createForm(new SpecialtiesType(), $entity, array(
            'action' => $this->generateUrl('specialties_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear','attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }

    /**
     * Displays a form to create a new Specialties entity.
     *
     * @Route("/new", name="specialties_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function newAction()
    {
        $entity = new Specialties();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Specialties entity.
     *
     * @Route("/{id}", name="specialties_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Specialties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialties entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Specialties entity.
     *
     * @Route("/{id}/edit", name="specialties_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Specialties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialties entity.');
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
    * Creates a form to edit a Specialties entity.
    *
    * @param Specialties $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Specialties $entity)
    {
        $form = $this->createForm(new SpecialtiesType(), $entity, array(
            'action' => $this->generateUrl('specialties_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update','attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }
    /**
     * Edits an existing Specialties entity.
     *
     * @Route("/{id}", name="specialties_update")
     * @Method("PUT")
     * @Template("AppBundle:Specialties:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Specialties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialties entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('specialties_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Specialties entity.
     *
     * @Route("/{id}", name="specialties_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Specialties')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Specialties entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('specialties'));
    }

    /**
     * Creates a form to delete a Specialties entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('specialties_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class' => 'submit btnAdm lila')))
            ->getForm()
        ;
    }
}
