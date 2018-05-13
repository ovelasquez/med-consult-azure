<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Contacts;
use AppBundle\Form\ContactsType;

/**
 * Contacts controller.
 *
 * @Route("/contacts")
 */
class ContactsController extends Controller {

    /**
     * Lists all Contacts entities.
     *
     * @Route("/", name="contacts")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Contacts')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Contacts entity.
     *
     * @Route("/", name="contacts_create")
     * @Method("POST")
     * @Template("AppBundle:Contacts:new.html.twig")
     */
    public function createAction(Request $request) {
        $entity = new Contacts();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                    ->setSubject($entity->getSubject())
                    ->setFrom($entity->getEmail())
                    ->setTo('info@medeconsult.com')
                    ->setBody(
                    $this->renderView(
                            'AppBundle:Contacts:email.txt.twig', array('email' => $entity->getEmail(), 'message' => $entity->getMessage())
                    ), 'text/html'
            );
            if ($entity->getSendcopia()) {
                $message->addCc($entity->getEmail());
            }
            $this->get('mailer')->send($message);
            $em = $this->getDoctrine()->getManager();
            $entity->setDateTime(new \DateTime("now"));
            $entity->setIp($this->get('request')->getClientIp());
            $em->persist($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('contact_res'));
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tag' => 'contacto'
        );
    }

    /**
     * Creates a form to create a Contacts entity.
     *
     * @param Contacts $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Contacts $entity) {
        $form = $this->createForm(new ContactsType(), $entity, array(
            'action' => $this->generateUrl('contacts_create'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Contacts entity.
     *
     * @Route("/new", name="contacts_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction() {
        $entity = new Contacts();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tag' => 'contacto',
        );
    }

    /**
     * Displays a form to create a new Contacts entity.
     *
     * @Route("/new/result", name="contacts_new_result")
     * @Method("GET")
     * @Template()
     */
    public function resultAction() {

        return array(
            'tag' => 'contacto',
        );
    }

    /**
     * Finds and displays a Contacts entity.
     *
     * @Route("/{id}", name="contacts_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Contacts')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Contacts entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Contacts entity.
     *
     * @Route("/{id}", name="contacts_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Contacts')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Contacts entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('contacts'));
    }

    /**
     * Creates a form to delete a Contacts entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('contacts_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Eliminar','attr' => array('class' => 'submit btnAdm lila')))
                        ->getForm()
        ;
    }

}
