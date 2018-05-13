<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\StoragePlans;
use AppBundle\Form\StoragePlansType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * StoragePlans controller.
 *
 * @Route("/storageplans")
 */
class StoragePlansController extends Controller
{

    /**
     * Lists all StoragePlans entities.
     *
     * @Route("/", name="storageplans")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")      
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:StoragePlans')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    
    /**
     * Creates a new StoragePlans entity.
     *
     * @Route("/", name="storageplans_create")
     * @Method("POST")
     * @Template("AppBundle:StoragePlans:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')")      
     */
    public function createAction(Request $request)
    {
        $entity = new StoragePlans();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('storageplans_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a StoragePlans entity.
     *
     * @param StoragePlans $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(StoragePlans $entity)
    {
        $form = $this->createForm(new StoragePlansType(), $entity, array(
            'action' => $this->generateUrl('storageplans_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear','attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }

    /**
     * Displays a form to create a new StoragePlans entity.
     *
     * @Route("/new", name="storageplans_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")      
     */
    public function newAction()
    {
        $entity = new StoragePlans();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a StoragePlans entity.
     *
     * @Route("/{id}", name="storageplans_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")      
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:StoragePlans')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StoragePlans entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing StoragePlans entity.
     *
     * @Route("/{id}/edit", name="storageplans_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")      
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:StoragePlans')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StoragePlans entity.');
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
    * Creates a form to edit a StoragePlans entity.
    *
    * @param StoragePlans $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(StoragePlans $entity)
    {
        $form = $this->createForm(new StoragePlansType(), $entity, array(
            'action' => $this->generateUrl('storageplans_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update','attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }
    /**
     * Edits an existing StoragePlans entity.
     *
     * @Route("/{id}", name="storageplans_update")
     * @Method("PUT")
     * @Template("AppBundle:StoragePlans:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')")      
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:StoragePlans')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find StoragePlans entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('storageplans_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a StoragePlans entity.
     *
     * @Route("/{id}", name="storageplans_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")      
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:StoragePlans')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find StoragePlans entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('storageplans'));
    }

    /**
     * Creates a form to delete a StoragePlans entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('storageplans_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class' => 'submit btnAdm lila')))
            ->getForm()
        ;
    }
    
    /**
     * Lists all StoragePlans entities for subscriptions
     *
     * @Route("/list/planes", name="storageplans-list")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")      
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:StoragePlans')->findAll();
        
        $user = null;
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.context')->getToken()->getUser();
        }

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user entity.');
        }

        $patient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));

        if (!$patient) {
            throw $this->createNotFoundException('Unable to find patient entity.');
        }

        return array(
            'entities' => $entities,
            'patient' => $patient,
           
        );
    }
}
