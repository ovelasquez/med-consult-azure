<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\PatientsShareMedicalHistory;
use AppBundle\Form\PatientsShareMedicalHistoryType;
use AppBundle\Form\PatientsShareMedicalHistoryUpdateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Finder\Exception\AccessDeniedException;
/**
 * PatientsShareMedicalHistory controller.
 *
 * @Route("/patients/share/medical/history")
 */
class PatientsShareMedicalHistoryController extends Controller {

    /**
     * Lists all PatientsShareMedicalHistory entities.
     *
     * @Route("/", name="patientssharemedicalhistory")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $entity = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user->getId()));
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patients entity.');
        }

        $entities = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findBy(array("patient"=>$entity));

        return array(
            'entities' => $entities,
            'entity' => $entity,
        );
    }

    /**
     * Creates a new PatientsShareMedicalHistory entity.
     *
     * @Route("/", name="patientssharemedicalhistory_create")
     * @Method("POST")
     * @Template("AppBundle:PatientsShareMedicalHistory:new.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function createAction(Request $request) {
        $entity = new PatientsShareMedicalHistory();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $user = $this->get('security.context')->getToken()->getUser();
            $patient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user->getId()));
            if (!$patient) {
                throw $this->createNotFoundException('Unable to find Patients entity.');
            }

            $entity->setPatient($patient);
            $entity->setDateTime(new \DateTime("now"));
            $entity->setToken($this->generateRandomString(6));
            $em->persist($entity);
            $em->flush();

            $message = \Swift_Message::newInstance()
                    ->setSubject($entity->getPatient()->getUser()->getName().' HA COMPARTIDO SU HISTORIAL MÉDICO CONTIGO')
                    ->setFrom('noreply@medeconsult.com')
                    ->setTo($entity->getEmail())
                    ->setBody(
                    $this->renderView(
                            'AppBundle:PatientsShareMedicalHistory:email.shared.html.twig', array('email'=>$entity->getEmail(),'username' => $entity->getPatient()->getUser()->getName(),'name'=>$entity->getName(),'token'=>$entity->getToken(),'sharedUrl'=>$this->generateUrl('patients_medical_history_shared', array('token' => $entity->getToken()),true))
                    ), 'text/html'
            );
            $this->get('mailer')->send($message);

            return $this->redirect($this->generateUrl('patientssharemedicalhistory_show', array('id' => $entity->getId(),'new' => 1)));
        }

        return array(
            'entity' => $entity,
            'patient' => $patient,
            'form' => $form->createView(),
        );
    }
    
    /**
     * Creates a new PatientsShareMedicalHistory entity for menu home.
     *    
     */
    public function createMenuAction() {
        $entity = new PatientsShareMedicalHistory();
        $form = $this->createCreateForm($entity);
        
        return $this->render(
                        'patientssharemedicalhistory/create_menu.html.twig', array(
                            'entity' => $entity,
                            'form' => $form->createView(),
                            )
        );
            
    }

    /**
     * Creates a form to create a PatientsShareMedicalHistory entity.
     *
     * @param PatientsShareMedicalHistory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(PatientsShareMedicalHistory $entity) {
        $form = $this->createForm(new PatientsShareMedicalHistoryType(), $entity, array(
            'action' => $this->generateUrl('patientssharemedicalhistory_create'),
            'method' => 'POST',
        ));

//        $form->add('submit', 'submit', array('label' => 'Compartir','attr'=>array('class'=>'submitGuardarDiez')));

        return $form;
    }

    /**
     * Displays a form to create a new PatientsShareMedicalHistory entity.
     *
     * @Route("/new", name="patientssharemedicalhistory_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function newAction() {
        $entity = new PatientsShareMedicalHistory();

        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();
        $patient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user->getId()));
        if (!$patient) {
            throw $this->createNotFoundException('Unable to find Patients entity.');
        }
        $entity->setPatient($patient);

        $form = $this->createCreateForm($entity);


        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'patient' => $patient,
        );
    }

    /**
     * Finds and displays a PatientsShareMedicalHistory entity.
     *
     * @Route("/{id}", name="patientssharemedicalhistory_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PatientsShareMedicalHistory entity.');
        }


        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'patient' => $entity->getPatient(),
        );
    }

    /**
     * Displays a form to edit an existing PatientsShareMedicalHistory entity.
     *
     * @Route("/{id}/edit", name="patientssharemedicalhistory_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PatientsShareMedicalHistory entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'patient' => $entity->getPatient(),
        );
    }

    /**
     * Creates a form to edit a PatientsShareMedicalHistory entity.
     *
     * @param PatientsShareMedicalHistory $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(PatientsShareMedicalHistory $entity) {
        $form = $this->createForm(new PatientsShareMedicalHistoryUpdateType(), $entity, array(
            'action' => $this->generateUrl('patientssharemedicalhistory_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

//        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing PatientsShareMedicalHistory entity.
     *
     * @Route("/{id}", name="patientssharemedicalhistory_update")
     * @Method("PUT")
     * @Template("AppBundle:PatientsShareMedicalHistory:edit.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find PatientsShareMedicalHistory entity.');
        }


        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('patientssharemedicalhistory_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'patient' => $entity->getPatient(),
        );
    }

    /**
     * Deletes a PatientsShareMedicalHistory entity.
     *
     * @Route("/{id}", name="patientssharemedicalhistory_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find PatientsShareMedicalHistory entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('patientssharemedicalhistory'));
    }

    /**
     * Creates a form to delete a PatientsShareMedicalHistory entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('patientssharemedicalhistory_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Eliminar', 'attr'=>array('class'=>'submit btnAdm lilaOscuro')))
                        ->getForm()
        ;
    }

    /**
     * Displays a form to create a new PatientsShareMedicalHistory entity.
     *
     * @Route("/register/guess/{token}", name="patientssharemedicalhistory_view_guess")
     * @Method("GET")
     * @Template()
     */
    public function registerguessAction($token) {
        $em = $this->getDoctrine()->getManager();
        $tkG = base64_encode('guess'); 
        
        //verificar si el token esta activo
        $shared = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $token));
        $now = new \DateTime("now");
        if (!$shared) {
            throw $this->createNotFoundException('Unable to find Patients Shared entity.');
        }elseif (($shared->getAvailable() > 0 && $now->diff($shared->getDateTime())->days > $shared->getAvailable())) {
            throw $this->createNotFoundException('Token vencido');
        }
        
        return array(           
            'tkG'=>$tkG,
            'token'=>$tkG,
            'tokenSh'=>$token,
        );
    }
    
    /**
     * Displays a form to create a new PatientsShareMedicalHistory entity.
     *
     * @Route("/login/guess/{token}", name="patientssharemedicalhistory_log_guess")
     * @Method("GET")
     * @Template()
     */
    public function loginguessAction($token) {
        //$tkG = base64_encode('guess'); 
        $em = $this->getDoctrine()->getManager();
        
        //verificar si el token esta activo
        $shared = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $token));
        $now = new \DateTime("now");
        if (!$shared) {
            throw $this->createNotFoundException('Unable to find Patients Shared entity.');
        }elseif (($shared->getAvailable() > 0 && $now->diff($shared->getDateTime())->days > $shared->getAvailable())) {
            throw $this->createNotFoundException('Token vencido');
        }
        
        return array(           
            'tkG'=>$token,
        );
    }
    
    
    /**
     * Displays a form to create a new PatientsShareMedicalHistory entity.
     *
     * @Route("/guess/view", name="patientssharemedicalhistory_guess")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_GUESS') or has_role('ROLE_ADMIN')") 
     */
    public function guessAction() {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        if (!$user) {
            throw $this->createNotFoundException('Unable to find User GUess entity.');
        }
        $entities = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findBy(array('email' => $user->getEmail()),array('dateTime' => 'DESC'));
        $now = new \DateTime("now");
        
        for($i=0;$i<count($entities);$i++):
            if (($entities[$i]->getAvailable() > 0 && $now->diff($entities[$i]->getDateTime())->days > $entities[$i]->getAvailable())) {
               $entities[$i]->setToken('');
            }
        endfor;
        return array(
            'entities' => $entities
        );
    }
    
    private function generateRandomString($length = 10) {
        return substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
    }
    
    

}
