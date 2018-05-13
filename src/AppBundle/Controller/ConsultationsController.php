<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Consultations;
use AppBundle\Entity\Alertas;
use AppBundle\Form\AlertasType;
use AppBundle\Entity\ModalityConsultations;
use AppBundle\Form\ConsultationsType;
use AppBundle\Form\ConsultationsEditType;
use AppBundle\Form\ConsultationsFormatoType;
use \AppBundle\Entity\Calendar;
use \AppBundle\Entity\MedicalForms;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Ps\PdfBundle\Annotation\Pdf;
use PHPPdf\DataSource\DataSource;
use Braintree_Configuration;
use Braintree_ClientToken;
use Braintree_Customer;
use Braintree_Exception_NotFound;
use Braintree_Transaction;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Validator\Constraints\DateTime;
use AppBundle\Entity\Promotion;
use AppBundle\Entity\PromotionLog;

/**
 * Consultations controller.
 *
 * @Route("/consultations")
 */
class ConsultationsController extends Controller {

    /**
     * Lists all Consultations entities.
     *
     * @Route("/", name="consultations")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Consultations')->findBy( array(), array('id' => 'DESC') );

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Consultations entity.
     *
     * @Route("/", name="consultations_create")
     * @Method("POST")
     * @Template("AppBundle:Consultations:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function createAction(Request $request) {
        $entity = new Consultations();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('consultations_show', array('id' => $entity->getId())));
        }



        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a new Alerta entity.
     */
    public function createAlertaAction($id, $user, $tipo) {

        //crear alerta traer datos??
        // el usuario en sesion es medico o paciente??


        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('AppUserBundle:User')->find($user);
        $usuario->getId();


        $pacient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $usuario));

        if ($pacient) {

            // alerta para el medico get phisians
            $phisians = $em->getRepository('AppBundle:Consultations')->find($id);
            $med = $phisians->getPhysician();
            $medico = $em->getRepository('AppBundle:Physicians')->find($med);
            $userAlert = $medico->getUser();
        } else {

            // alerta para el paciente get patient
            $patient = $em->getRepository('AppBundle:Consultations')->find($id);
            $pac = $patient->getPatient();
            $paciente = $em->getRepository('AppBundle:Patients')->find($pac);
            $userAlert = $paciente->getUser();
        }


        $usuario = $em->getRepository('AppUserBundle:User')->find($userAlert);

        if (!$usuario) {
            throw $this->createNotFoundException('no se encontro el usuario.');
        }

        //generar alerta

        $entity = new Alertas();
        $entity->setUser($usuario);
        $entity->setTipo($tipo);
        $entity->setStatus("Activo");
        $entity->setCreatedate(new \DateTime('now'));

        $idconsul = $em->getRepository('AppBundle:Consultations')->find($id);
        if (!$idconsul) {
            throw $this->createNotFoundException('no se encontro la consulta.');
        }
        $entity->setConsultation($idconsul);

        //$em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return true;
    }

    /**
     * Creates a form to create a Consultations entity.
     *
     * @param Consultations $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Consultations $entity) {
        $form = $this->createForm(new ConsultationsType(), $entity, array(
            'action' => $this->generateUrl('consultations_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Consultations entity.
     *
     * @Route("/new", name="consultations_new")
     * @Method("GET")
     * @Template()
     * @Security(" has_role('ROLE_ADMIN')") 
     */
    public function newAction() {
        $entity = new Consultations();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Consultations entity.
     *
     * @Route("/{id}", name="consultations_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS')")
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }


        $deleteForm = $this->createDeleteForm($id);

        $entityPat = $this->getPatientLog();

        $conn = $em->getConnection();
        $meeting = $conn->fetchArray('SELECT _meetings.url FROM _meetings LEFT JOIN _meetings_days ON _meetings.id=_meetings_days.id_meeting WHERE _meetings_days.id_consultation=?', array($id));
        $urlMeeting = '';
        if ($meeting !== false):
            $urlMeeting = $meeting[0];
        endif;

        $fecha = $conn->fetchArray('SELECT date(`update_date`) FROM `consultations` WHERE `id`=?', array($id));
        $fech = $fecha[0];
        $datetime1 = new \DateTime($fech);
        $datetime2 = new \DateTime('now');
        $interval = $datetime1->diff($datetime2);
        //echo $interval->format('%R%a');

        return array(
            'entity' => $entity,
            'patient' => $entityPat,
            'delete_form' => $deleteForm->createView(),
            'url_meeting' => $urlMeeting,
            'dias' => $interval->format('%a'),
        );
    }
    
    
    /**
     * Finds and displays a Consultations entity.
     *
     * @Route("/admin/{id}", name="consultations_show_admin")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAdminAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }


        $deleteForm = $this->createDeleteForm($id);

        $entityPat = $entity->getPatient();

        $conn = $em->getConnection();
        $meeting = $conn->fetchArray('SELECT _meetings.url FROM _meetings LEFT JOIN _meetings_days ON _meetings.id=_meetings_days.id_meeting WHERE _meetings_days.id_consultation=?', array($id));
        $urlMeeting = '';
        if ($meeting !== false):
            $urlMeeting = $meeting[0];
        endif;

        $fecha = $conn->fetchArray('SELECT date(`update_date`) FROM `consultations` WHERE `id`=?', array($id));
        $fech = $fecha[0];
        $datetime1 = new \DateTime($fech);
        $datetime2 = new \DateTime('now');
        $interval = $datetime1->diff($datetime2);
        //echo $interval->format('%R%a');

        return array(
            'entity' => $entity,
            'patient' => $entityPat,
            'delete_form' => $deleteForm->createView(),
            'url_meeting' => $urlMeeting,
            'dias' => $interval->format('%a'),
        );
    }
    

    /**
     * Finds and displays a Consultations entity.
     *
     * @Route("/patient/shared/{id}/{pat}", name="consultations_show_patient_shared")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS') or has_role('ROLE_GUESS')")
     */
    public function showSharedAction($id, $pat) {
        //$this->denyAccessUnlessGranted('ROLE_PHYSICIANS', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }

        $shared = null;
        $token = null;

        $entityPat = $em->getRepository('AppBundle:Patients')->find($pat);
        if (!$entityPat) {
            $shared = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $pat));
            if (!$shared) {
                throw $this->createNotFoundException('Unable to find Patients Token entity.');
            }
            $token = $pat;
            $entityPat = $shared->getPatient();
        }

        if (null === $shared) {
            $user = null;
            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->get('security.context')->getToken()->getUser();
            }

            if (!$user) {
                throw $this->createNotFoundException('Unable to find user entity.');
            }
        }


        if (!$entityPat) {
            throw $this->createNotFoundException('Unable to find Patient.');
        }

        /* Vista solo para usuarios relacionados por consulta */
        if ($this->get('security.context')->isGranted('ROLE_PHYSICIANS')) :
            $entityPhy = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
            $entityCons = $em->getRepository('AppBundle:Consultations')->findOneBy(
                    array('physician' => $entityPhy, 'patient' => $entityPat)
            );

            if ($entityCons === null):
                return $this->redirect($this->generateUrl('physicians_show_front', array('id' => $entityPhy->getId())));
            endif;
        endif;

        /* Vista solo para usuarios relacionados por compartir */
        if ($this->get('security.context')->isGranted('ROLE_GUESS')) :
            $user = $this->get('security.context')->getToken()->getUser();
            $entitySh = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(
                    array('patient' => $entityPat, 'email' => $user->getEmail())
            );

            if ($entitySh === null):
                return $this->redirect($this->generateUrl('patientssharemedicalhistory_guess'));
            endif;
        endif;
        $dom = new \DOMDocument;
        if ($entity->getResume() !== null && $entity->getResume() !== ''):

            $dom->loadHTML(($entity->getResume()));
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//@*');
            foreach ($nodes as $node) {
                if ($node->nodeName !== 'style'):
                    $node->parentNode->removeAttribute($node->nodeName);
                endif;
            }
            $entity->setResume(html_entity_decode(strip_tags($dom->saveHTML(), '<p><ol><ul><li><a><div><span><strong><table><tr><td>')));

        endif;


        if ($entity->getAnswer() !== null && $entity->getAnswer() !== ''):
            $dom->loadHTML(($entity->getAnswer()));
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//@*');
            foreach ($nodes as $node) {
                if ($node->nodeName !== 'style'):
                    $node->parentNode->removeAttribute($node->nodeName);
                endif;
            }
            $entity->setAnswer(html_entity_decode(strip_tags($dom->saveHTML(), '<p><ol><ul><li><a><div><span><strong><table><tr><td>')));

        endif;


        return array(
            'entity' => $entity,
            'patient' => $entityPat,
            'token' => $token,
        );
    }

    /**
     * Displays a form to edit an existing Consultations entity.
     *
     * @Route("/{id}/edit", name="consultations_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS') ")
     */
    public function editAction($id) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || (!$this->get('security.context')->isGranted('ROLE_ADMIN') && !$this->get('security.context')->isGranted('ROLE_PHYSICIANS') )) {
            return $this->redirect($this->generateUrl('fos_user_security_login', array('r' => $this->generateUrl('consultations_edit', array('id' => $id), true))));
        }


        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }


        $entityPhy = $this->getPhysicianLog();



        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        //DATOS DE PACIENTE
        $twig = $this->container->get('twig');
        $globals = $twig->getGlobals();

        $entityForm = $em->getRepository('AppBundle:MedicalForms')->findOneBy(array('formName' => $globals['form_hs']));
        if (!$entityForm) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }
        $entitySp = $em->getRepository('AppBundle:MedicalFormsViews')->findOneBy(array('specialty' => $entity->getSpecialty()->getId()));
        if ($entitySp) {
            $fieldsForms = $this->getFieldsForms($entityForm, $em, false, $entitySp->getId(), $entity->getPatient()->getUser(), $entity->getId(), null, 0);
        } else {
            $fieldsForms = null;
        }
        $entityCal = $em->getRepository('AppBundle:Calendar')->findOneBy(array('consultation' => $entity->getId()));

//        echo"<pre>";
//        \Doctrine\Common\Util\Debug::dump($fieldsForms[15][29]->fields,2);
//        echo"</pre>";
//        exit();
        $dom = new \DOMDocument;
        if ($entity->getResume() !== null && $entity->getResume() !== ''):
            $dom->loadHTML(($entity->getResume()));
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//@*');
            foreach ($nodes as $node) {
                if ($node->nodeName !== 'style'):
                    $node->parentNode->removeAttribute($node->nodeName);
                endif;
            }
            $entity->setResume(html_entity_decode(strip_tags($dom->saveHTML(), '<p><ol><ul><li><a><div><span><strong><table><tr><td>')));

        endif;

        if ($entity->getAnswer() !== null && $entity->getAnswer() !== ''):
            $dom->loadHTML(($entity->getAnswer()));
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//@*');
            foreach ($nodes as $node) {
                if ($node->nodeName !== 'style'):
                    $node->parentNode->removeAttribute($node->nodeName);
                endif;
            }
            $entity->setAnswer(html_entity_decode(strip_tags($dom->saveHTML(), '<p><ol><ul><li><a><div><span><strong><table><tr><td>')));

        endif;

        $conn = $em->getConnection();
        $meeting = $conn->fetchArray('SELECT _meetings.url FROM _meetings LEFT JOIN _meetings_days ON _meetings.id=_meetings_days.id_meeting WHERE _meetings_days.id_consultation=?', array($id));
        $urlMeeting = '';
        if ($meeting !== false):
            $urlMeeting = $meeting[0];
        endif;

        $fecha = $conn->fetchArray('SELECT date(`update_date`) FROM `consultations` WHERE `id`=?', array($id));


        $fech = $fecha[0];
        $datetime1 = new \DateTime($fech);
        $datetime2 = new \DateTime('now');
        $interval = $datetime1->diff($datetime2);
        // echo $interval->format('%R%a');



        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'physician' => $entityPhy,
            'fieldsForms' => $fieldsForms,
            'entityform' => $entityForm,
            'entityCal' => $entityCal,
            'url_meeting' => $urlMeeting,
            'dias' => $interval->format('%a'),
        );
    }

    /**
     * Creates a form to edit a Consultations entity.
     *
     * @param Consultations $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Consultations $entity) {
        $form = $this->createForm(new ConsultationsEditType(), $entity, array(
            'action' => $this->generateUrl('consultations_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        //$form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing Consultations entity.
     *
     * @Route("/{id}", name="consultations_update")
     * @Method("PUT")
     * @Template("AppBundle:Consultations:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS') ")
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }

        //$deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);


        if ($editForm->isValid() || (null !== $request->request->get("cancel") || null !== $request->request->get("confirm"))) :

            $dateConsPt = "00-00-0000";
            $dateMeet = "00-00-0000";
            if ($entity->getModalityConsultation()->getTag() === 'inlive') {
                $entityCal = $em->getRepository('AppBundle:Calendar')->findOneBy(array('consultation' => $entity->getId()));
                if ($entityCal):
                    $dateConsPt = $entityCal->getDatetimePatient()->format("d-m-Y") . " A LAS " . $entityCal->getDatetimePatient()->format("H:i");
                    $dateMeet = $entityCal->getDatetimePatient()->format("Y-m-d");
                endif;
            }

            if (null !== $request->request->get("cancel")):
                $entity->setStatus(-1);
                $entity->setUpdateDate(new \DateTime("now"));
                $em->flush();

                $message = \Swift_Message::newInstance()
                        ->setSubject('Tu cita por video llamada necesita ser re-agendada')
                        ->setFrom('noreply@medeconsult.com')
                        ->setTo($entity->getPatient()->getUser()->getEmail())
                        ->setBody(
                        $this->renderView(
                                'AppBundle:Consultations:email.canceled.html.twig', array('email' => $entity->getPatient()->getUser()->getEmail(), 'type' => $entity->getModalityConsultation()->getTag(), 'cons' => $entity->getModalityConsultation()->getName(), 'pat_name' => $entity->getPatient()->getUser()->getName(), 'phy_name' => $entity->getPhysician()->getUser()->getName(), 'datec' => $dateConsPt)
                        ), 'text/html'
                );
                $this->get('mailer')->send($message);

            elseif (null !== $request->request->get("confirm")):
                /*                 * **************MEETING */
                $conn = $em->getConnection();
                $meeting = $conn->fetchArray('SELECT _meetings.url,_meetings.id FROM _meetings WHERE ( SELECT COUNT(*) FROM _meetings_days WHERE day_date=? AND _meetings.id=_meetings_days.id_meeting )<=0 LIMIT 1', array($dateMeet));
                if ($meeting !== false):
                    $conn->executeUpdate('INSERT INTO _meetings_days (id_meeting,day_date,id_consultation) VALUES (?,?,?)', array($meeting[1], $dateMeet, $id));
                else:
                    $message = \Swift_Message::newInstance()
                            ->setSubject('Consulta sin Meeting')
                            ->setFrom('noreply@medeconsult.com')
                            ->setTo('info@medeconsult.com')
                            ->setBody(
                            $this->renderView(
                                    'AppBundle:Consultations:email.confirmed.html.twig', array('type' => $entity->getModalityConsultation()->getTag(), 'cons' => $entity->getModalityConsultation()->getName(), 'pat_name' => $entity->getPatient()->getUser()->getName(), 'phy_name' => $entity->getPhysician()->getUser()->getName(), 'datec' => $dateConsPt)
                            ), 'text/html'
                    );
                    $this->get('mailer')->send($message);
                endif;

                /*                 * **************MEETING */

                $entity->setStatus(1);
                $em->flush();

                $message = \Swift_Message::newInstance()
                        ->setSubject('Tu Videollamada ha sido confirmada')
                        ->setFrom('noreply@medeconsult.com')
                        ->setTo($entity->getPatient()->getUser()->getEmail())
                        ->setBody(
                        $this->renderView(
                                'AppBundle:Consultations:email.confirmed.html.twig', array('email' => $entity->getPatient()->getUser()->getEmail(), 'type' => $entity->getModalityConsultation()->getTag(), 'cons' => $entity->getModalityConsultation()->getName(), 'pat_name' => $entity->getPatient()->getUser()->getName(), 'phy_name' => $entity->getPhysician()->getUser()->getName(), 'datec' => $dateConsPt)
                        ), 'text/html'
                );
                $this->get('mailer')->send($message);
            elseif (null !== $request->request->get("send")):
                $entity->setStatus(2);
                $entity->setUpdateDate(new \DateTime("now"));
                $em->flush();

                $message = \Swift_Message::newInstance()
                        ->setSubject('Tu ' . $entity->getModalityConsultation()->getName() . ' ha sido respondida')
                        ->setFrom('noreply@medeconsult.com')
                        ->setTo($entity->getPatient()->getUser()->getEmail())
                        ->setBody(
                        $this->renderView(
                                'AppBundle:Consultations:email.completed.html.twig', array('email' => $entity->getPatient()->getUser()->getEmail(), 'type' => $entity->getModalityConsultation()->getTag(), 'cons' => $entity->getModalityConsultation()->getName(), 'pat_name' => $entity->getPatient()->getUser()->getName(), 'phy_name' => $entity->getPhysician()->getUser()->getName(), 'datec' => $dateConsPt, 'urlc' => $this->generateUrl('consultations_show', array('id' => $entity->getId()), true))
                        ), 'text/html'
                );
                $this->get('mailer')->send($message);
            elseif (null !== $request->request->get("save")):
                $entity->setStatus(1);
                $entity->setUpdateDate(new \DateTime("now"));
                $em->flush();
            endif;

            //inicio generar notificacion Alerta

            $user = $this->getUser();
            $user = $user->getId();
            $tipo = "Respuesta de Consulta";
            $id = $entity->getId();

            $alerta = $this->createAlertaAction($id, $user, $tipo);

            //fin Notificacion

            return $this->redirect($this->generateUrl('consultations_list_physician', array('type' => 0)));
        endif;

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
                //'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Consultations entity.
     *
     * @Route("/{id}", name="consultations_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Consultations')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Consultations entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('consultations'));
    }

    /**
     * Creates a form to delete a Consultations entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('consultations_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

    /**
     * Lists all Consultations entities.
     *
     * @Route("/patient/list", name="consultations_list_patient")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') ")
     */
    public function listPatientAction() {
        $em = $this->getDoctrine()->getManager();

        $prePhSel = $this->getRequest()->get('physician');

        $entityPat = $this->getPatientLog();

        $entitiesMC = $em->getRepository('AppBundle:ModalityConsultations')->findAll();
        $entities = $em->getRepository('AppBundle:Consultations')->findAllWithCalendar($entityPat->getId());
        $entitiesAll = array();

        for ($i = 0; $i < count($entities); $i++) {
            $ent = $entities[$i];
            $entNext = '';
            if ($i + 1 < count($entities)):
                $entNext = $entities[$i + 1];
            endif;
            if (is_a($ent, '\AppBundle\Entity\Consultations')):
                $entitiesAll[] = (object) array("con" => $ent, "cal" => $entNext);
            endif;
        }
//        echo"<pre>";
//        \Doctrine\Common\Util\Debug::dump($entitiesAll[1]->con);
//        echo"</pre>";
//        exit();

        return array(
            'patient' => $entityPat,
            'entities' => $entitiesAll,
            'entitiesMC' => $entitiesMC,
            'prePhSel' => $prePhSel,
        );
    }

    /**
     * Lists all Consultations entities.
     *
     * @Route("/patient/list/shared/{id}", name="consultations_list_shared_patient")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS') or has_role('ROLE_GUESS')")
     */
    public function listSharedPatientAction($id) {
        $em = $this->getDoctrine()->getManager();

        $shared = null;
        $token = null;

        $entityPat = $em->getRepository('AppBundle:Patients')->find($id);
        if (!$entityPat) {
            $shared = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $id));
            if (!$shared) {
                throw $this->createNotFoundException('Unable to find Patients Token entity.');
            }
            $token = $id;
            $entityPat = $shared->getPatient();
        }

        if (null === $shared) {
            $user = null;
            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->get('security.context')->getToken()->getUser();
            }

            if (!$user) {
                throw $this->createNotFoundException('Unable to find user entity.');
            }
        }

        /* Vista solo para usuarios relacionados por consulta */
        if ($this->get('security.context')->isGranted('ROLE_PHYSICIANS')) :
            $user = $this->get('security.context')->getToken()->getUser();
            $entityPhy = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
            $entityCons = $em->getRepository('AppBundle:Consultations')->findOneBy(
                    array('physician' => $entityPhy, 'patient' => $entityPat)
            );

            if ($entityCons === null):
                return $this->redirect($this->generateUrl('physicians_show_front', array('id' => $entityPhy->getId())));
            endif;
        endif;

        /* Vista solo para usuarios relacionados por compartir */
        if ($this->get('security.context')->isGranted('ROLE_GUESS')) :
            $user = $this->get('security.context')->getToken()->getUser();
            $entitySh = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(
                    array('patient' => $entityPat, 'email' => $user->getEmail())
            );

            if ($entitySh === null):
                return $this->redirect($this->generateUrl('patientssharemedicalhistory_guess'));
            endif;
        endif;

        $entities = $em->getRepository('AppBundle:Consultations')->findAllWithCalendar($entityPat->getId());
        $entitiesAll = array();

        for ($i = 0; $i < count($entities); $i++) {
            $ent = $entities[$i];
            $entNext = '';
            if ($i + 1 < count($entities)):
                $entNext = $entities[$i + 1];
            endif;
            if (is_a($ent, '\AppBundle\Entity\Consultations')):
                $entitiesAll[] = (object) array("con" => $ent, "cal" => $entNext);
            endif;
        }

        return array(
            'patient' => $entityPat,
            'entities' => $entitiesAll,
            'token' => $token
        );
    }

    /**
     * Creates a new Consultations type entity.
     *
     * @Route("/type", name="consultations_create_type")
     * @Method("POST")
     * @Template("AppBundle:Consultations:newType.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') ")
     */
    public function createTypeAction(Request $request) {
        $entity = new Consultations();
        $form = $this->createCreateTypeForm($entity);
        $form->handleRequest($request);

        $entityPat = $this->getPatientLog();
        $em = $this->getDoctrine()->getManager();

        if ($form->isValid()) {


            $entity->setStatus(-2);
            $entity->setQuestion(strlen($entity->getQuestion()) > 200 ? substr($entity->getQuestion(), 0, 200) : $entity->getQuestion());
            $dateCons = $entity->getCreationDate();
            $entity->setCreationDate(new \DateTime("now"));
            $em->persist($entity);
            $em->flush();

            $dateConsPt = "00-00-0000";
            $dateConsPh = "00-00-0000";
            if ($entity->getModalityConsultation()->getTag() === 'inlive') {
                $entityCal = new Calendar();
                $entityCal->setDatetimePatient(new \DateTime($dateCons));
                $dateTimeZonePhy = new \DateTimeZone($entity->getPhysician()->getTimezone());
                $dateTimeZonePat = new \DateTimeZone($entity->getPatient()->getTimezone());
                $dateTimePhy = new \DateTime($dateCons, $dateTimeZonePat);
                $dateTimePhy->setTimezone($dateTimeZonePhy);
                $entityCal->setDatetimeConsultation($dateTimePhy);
                $entityCal->setStatus(-2);
                $entityCal->setConsultation($entity);
                $em->persist($entityCal);
                $em->flush();
                $dateConsPt = $entityCal->getDatetimePatient()->format("d-m-Y") . " A LAS " . $entityCal->getDatetimePatient()->format("H:i");
                $dateConsPh = $entityCal->getDatetimeConsultation()->format("d-m-Y") . " A LAS " . $entityCal->getDatetimeConsultation()->format("H:i");
            }


            if ($entity->getModalityConsultation()->getTag() === 'consice') {
                if ($em->getRepository('AppBundle:Consultations')->findAllByPatientConsice($entityPat) > 0):
                    return $this->redirect($this->generateUrl('consultations_pay', array('idc' => $entity->getId())));
                else:
                    //MENS PAT
                    $message = \Swift_Message::newInstance()
                            ->setSubject('Tu ' . $entity->getModalityConsultation()->getName() . ' ha sido enviada exitosamente')
                            ->setFrom('noreply@medeconsult.com')
                            ->setTo($entity->getPatient()->getUser()->getEmail())
                            ->setBody(
                            $this->renderView(
                                    'AppBundle:Consultations:email.new.html.twig', array('email' => $entity->getPatient()->getUser()->getEmail(), 'type' => $entity->getModalityConsultation()->getTag(), 'cons' => $entity->getModalityConsultation()->getName(), 'pat_name' => $entity->getPatient()->getUser()->getName(), 'phy_name' => $entity->getPhysician()->getUser()->getName(), 'datec' => $dateConsPt)
                            ), 'text/html'
                    );
                    $this->get('mailer')->send($message);

                    //MENS PHY
                    $messagePh = \Swift_Message::newInstance()
                            ->setSubject('Tienes una nueva solicitud de ' . $entity->getModalityConsultation()->getName() . ' ')
                            ->setFrom('noreply@medeconsult.com')
                            ->setTo($entity->getPhysician()->getUser()->getEmail())
                            ->setBody(
                            $this->renderView(
                                    'AppBundle:Consultations:email.new.physician.html.twig', array('email' => $entity->getPhysician()->getUser()->getEmail(), 'urlc' => $this->generateUrl('consultations_edit', array('id' => $entity->getId()), true), 'type' => $entity->getModalityConsultation()->getTag(), 'cons' => $entity->getModalityConsultation()->getName(), 'pat_name' => $entity->getPatient()->getUser()->getName(), 'phy_name' => $entity->getPhysician()->getUser()->getLastName(), 'datec' => $dateConsPh)
                            ), 'text/html'
                    );
                    $this->get('mailer')->send($messagePh);
                    return $this->redirect($this->generateUrl('consultations_completed', array('idc' => $entity->getId())));
                endif;
            } else {
                $twig = $this->container->get('twig');
                $globals = $twig->getGlobals();
                $entitySp = $em->getRepository('AppBundle:MedicalFormsViews')->findOneBy(array('specialty' => $entity->getSpecialty()->getId()));
                if (!$entitySp) {
                    throw $this->createNotFoundException('Not Found Unable to find Specialty.');
                }
                return $this->redirect($this->generateUrl('medicalforms_fill_consultations', array('id' => $globals['form_hs'], 'idc' => $entity->getId(), 'filter' => $entitySp->getId(), 'page' => 0)));
            }
        }

        return array(
            'patient' => $entityPat, 'entity' => $entity, 'form' => $form->createView(), 'type' => $entity->getModalityConsultation()->getTag(), 'modality' => $entity->getModalityConsultation(), "isfirts" => $em->getRepository('AppBundle:Consultations')->findAllByPatientConsice($entityPat),
        );
    }

    /**
     * Creates a form to create a Consultations type entity.
     *
     * @param Consultations $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateTypeForm(Consultations $entity) {
        $form = $this->createForm(new ConsultationsType(), $entity, array(
            'action' => $this->generateUrl('consultations_create_type'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new Consultations entity.
     *
     * @Route("/type/new/{type}", name="consultations_new_type")
     * @Method("GET")
     * @Template()
     * 
     */
    public function newTypeAction($type) {

        $prePhSel = $this->getRequest()->get('prep');
        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || (!$this->get('security.context')->isGranted('ROLE_ADMIN') && !$this->get('security.context')->isGranted('ROLE_PATIENT') )) {
            return $this->redirect($this->generateUrl('fos_user_security_login', array('r' => $this->generateUrl('consultations_new_type', array('type' => $type, 'prep' => $prePhSel), true))));
        }

        $em = $this->getDoctrine()->getManager();


        $entityPh = null;
        if ($prePhSel !== null):
            $entityPh = $em->getRepository('AppBundle:Physicians')->find($prePhSel);
        endif;

        $entityPat = $this->getPatientLog();

        $entityMc = $em->getRepository('AppBundle:ModalityConsultations')->findOneBy(array('tag' => $type));
        if (!$entityMc) {
            throw $this->createNotFoundException('Unable to find ModalityConsultations.');
        }

        $entity = new Consultations();
        $entity->setPatient($entityPat);
        $entity->setModalityConsultation($entityMc);
        $form = $this->createCreateTypeForm($entity);

        return array(
            'entityPh' => $entityPh, 'patient' => $entityPat, 'entity' => $entity, 'form' => $form->createView(), 'type' => $type, 'modality' => $entityMc, "isfirts" => $em->getRepository('AppBundle:Consultations')->findAllByPatientConsice($entityPat),
        );
    }

    /**
     * Displays a form to create a new Consultations entity.
     *
     * @Route("/type/new/{type}/phy/{esp}", name="consultations_new_type_esp")
     * @Method("GET")
     * @Template()
     * 
     */
    public function newTypeEspAction($type, $esp) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY') || (!$this->get('security.context')->isGranted('ROLE_ADMIN') && !$this->get('security.context')->isGranted('ROLE_PATIENT') )) {
            return $this->redirect($this->generateUrl('fos_user_security_login', array('r' => $this->generateUrl('consultations_new_type', array('type' => $type), true))));
        }

        $em = $this->getDoctrine()->getManager();

        $prePhSel = $esp;
        $entityPh = null;
        if ($prePhSel !== null):
            $entityPh = $em->getRepository('AppBundle:Physicians')->find($prePhSel);
        else:
            throw $this->createNotFoundException('Unable to find Physician.');
        endif;

        $entityPat = $this->getPatientLog();

        $entityMc = $em->getRepository('AppBundle:ModalityConsultations')->findOneBy(array('tag' => $type));
        if (!$entityMc) {
            throw $this->createNotFoundException('Unable to find ModalityConsultations.');
        }

        $entity = new Consultations();
        $entity->setPatient($entityPat);
        $entity->setModalityConsultation($entityMc);
        $form = $this->createCreateTypeForm($entity);

        return array(
            'entityPh' => $entityPh, 'patient' => $entityPat, 'entity' => $entity, 'form' => $form->createView(), 'type' => $type, 'modality' => $entityMc, "isfirts" => $em->getRepository('AppBundle:Consultations')->findAllByPatientConsice($entityPat),
        );
    }

    /**
     * pay.
     *
     * @Route("/buy/getstarted/{idc}", name="consultations_pay")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")
     */
    public function payAction($idc) {

        $em = $this->getDoctrine()->getManager();

        $twig = $this->container->get('twig');
        $globals = $twig->getGlobals();
        if ($globals['promotion'] === 1):
            $msgE = "";

            if (strpos($idc, "#") !== false):
                $msgE = "Por favor verifique sus datos de pago";
                $idc = explode("#", $idc);
                $idc = $idc[0];
            endif;
        endif;


        $entityPat = $this->getPatientLog();

        Braintree_Configuration::environment('production');
        Braintree_Configuration::merchantId('3y7wcyqwgtwqjsh6');
        Braintree_Configuration::publicKey('zqqbgc2d8d7r7fkx');
        Braintree_Configuration::privateKey('cec344dd2994fd83f4b93b3c970b092e');
        $clientToken = Braintree_ClientToken::generate();

        $cons = $em->getRepository('AppBundle:Consultations')->find($idc);

        return array(
            'patient' => $entityPat,
            'idc' => $idc,
            'cToken' => $clientToken,
            'msg' => $msgE,
            'cons' => $cons,
        );
    }

    /**
     * pay.
     *
     * @Route("/buy/completed/{idc}", name="consultations_pay_completed")
     * @Method("POST")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")
     */
    public function payCompletedAction(Request $request) {
        $em = $this->getDoctrine()->getManager();

        $entityPat = $this->getPatientLog();

        $cons = new Consultations();
        $cons = $em->getRepository('AppBundle:Consultations')->find($request->request->get('consultation'));
        if (!$cons) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }

        $twig = $this->container->get('twig');
        $globals = $twig->getGlobals();

        $successRes = false;

        if ($globals['promotion'] === 1 && null !== $request->request->get("codePromotion") && '' !== $request->request->get("codePromotion")):

            if (null !== $request->request->get("codePromotion")):
                $promo = new Promotion();
                $promo = $em->getRepository('AppBundle:Promotion')->findOneBy(array("code" => $request->request->get('codePromotion')));
                if ($promo !== null && $promo->getUsageAmount() < 5):
                    $successRes = true;
                    $promo->setUsageAmount($promo->getUsageAmount() + 1);
                    $em->persist($promo);
                    $em->flush();

                    $promoLog = new PromotionLog();
                    $promoLog->setPromotion($promo);
                    $promoLog->setConsultation($cons);
                    $promoLog->setPatient($entityPat);

                    $em->persist($promoLog);
                    $em->flush();

                endif;
            endif;

            $result = "{}";

        elseif ($globals['payment'] === 1):

//            Braintree_Configuration::environment('sandbox');
//            Braintree_Configuration::merchantId('spfr3hqm8b573hjv');
//            Braintree_Configuration::publicKey('mhd2zt996w9ytp34');
//            Braintree_Configuration::privateKey('3d13f1ac11aa8dfa7535ae6eef17cc97');
            Braintree_Configuration::environment('production');
            Braintree_Configuration::merchantId('3y7wcyqwgtwqjsh6');
            Braintree_Configuration::publicKey('zqqbgc2d8d7r7fkx');
            Braintree_Configuration::privateKey('cec344dd2994fd83f4b93b3c970b092e');

            try {
                $customer = Braintree_Customer::find($entityPat->getId() + 10000000);
                $idCustomer = $customer->id;
            } catch (Braintree_Exception_NotFound $e) {
                $result = Braintree_Customer::create([
                            'id' => $entityPat->getId() + 10000000,
                            'firstName' => $request->request->get('firstName'),
                            'lastName' => $request->request->get('lastName'),
                ]);
                $result->success;
                $customer = $result->customer;
                $idCustomer = $customer->id;
            }

            $result = Braintree_Transaction::sale([
                        'amount' => $cons->getModalityConsultation()->getPrice(), //'1.00',
                        'orderId' => (int) $request->request->get('consultation') + 1000000,
                        'paymentMethodNonce' => $request->request->get('payment_method_nonce'),
                        'customerId' => $entityPat->getId() + 10000000,
                        'billing' => [
                            'firstName' => $request->request->get('firstName'),
                            'lastName' => $request->request->get('lastName'),
                            'streetAddress' => $request->request->get('streetAddress'),
                            'extendedAddress' => $request->request->get('extendedAddress'),
                            'locality' => $request->request->get('locality'),
                            'region' => $request->request->get('region'),
                            'postalCode' => $request->request->get('postalCode'),
                            'countryCodeAlpha2' => $request->request->get('countryCode')
                        ],
                        'options' => [
                            'submitForSettlement' => true
                        ],
                        'channel' => 'MyShoppingCartProvider'
            ]);
            $successRes = $result->success;

        else:

            $successRes = true;
            $result = "{}";

        endif;

        /* Crear entidad de pagos
         * 
         */

        if (!$successRes):
            return $this->redirect($this->generateUrl('consultations_pay', array('idc' => $request->request->get('consultation') . "#CP")));
        endif;

        $pay = new \AppBundle\Entity\Payment();
        $pay->setClientId($entityPat->getId());
        $pay->setClientEmail($entityPat->getUser()->getEmail());
        $pay->setCurrencyCode('USD');
        $pay->setDescription($cons->getModalityConsultation()->getName());
        $pay->setDetails($result);
        $pay->setIdp($cons->getId());
        $pay->setNumber(uniqid());
        $pay->setTotalAmount($cons->getModalityConsultation()->getPrice());
        $pay->setType($cons->getModalityConsultation()->getTag());
        $pay->setDate(new \DateTime());

        $em->persist($pay);
        $em->flush();

        $entity = $cons;

        $dateConsPt = "00-00-0000";
        $dateConsPh = "00-00-0000";

        if ($entity->getModalityConsultation()->getTag() == "inlive"):
            $entityCal = $em->getRepository('AppBundle:Calendar')->findOneBy(array("consultation" => $entity));
            $dateConsPt = $entityCal->getDatetimePatient()->format("d-m-Y") . " A LAS " . $entityCal->getDatetimePatient()->format("H:i");
            $dateConsPh = $entityCal->getDatetimeConsultation()->format("d-m-Y") . " A LAS " . $entityCal->getDatetimeConsultation()->format("H:i");

        endif;

        //MENS PAT
        $message = \Swift_Message::newInstance()
                ->setSubject('Tu ' . $entity->getModalityConsultation()->getName() . ' ha sido enviada exitosamente')
                ->setFrom('noreply@medeconsult.com')
                ->setTo($entity->getPatient()->getUser()->getEmail())
                ->setBody(
                $this->renderView(
                        'AppBundle:Consultations:email.new.html.twig', array('email' => $entity->getPatient()->getUser()->getEmail(), 'type' => $entity->getModalityConsultation()->getTag(), 'cons' => $entity->getModalityConsultation()->getName(), 'pat_name' => $entity->getPatient()->getUser()->getName(), 'phy_name' => $entity->getPhysician()->getUser()->getName(), 'datec' => $dateConsPt)
                ), 'text/html'
        );
        $this->get('mailer')->send($message);

        //MENS PHY
        $messagePh = \Swift_Message::newInstance()
                ->setSubject('Tienes una nueva solicitud de ' . $entity->getModalityConsultation()->getName() . ' ')
                ->setFrom('noreply@medeconsult.com')
                ->setTo($entity->getPhysician()->getUser()->getEmail())
                ->setBody(
                $this->renderView(
                        'AppBundle:Consultations:email.new.physician.html.twig', array('email' => $entity->getPhysician()->getUser()->getEmail(), 'urlc' => $this->generateUrl('consultations_edit', array('id' => $entity->getId()), true), 'type' => $entity->getModalityConsultation()->getTag(), 'cons' => $entity->getModalityConsultation()->getName(), 'pat_name' => $entity->getPatient()->getUser()->getName(), 'phy_name' => $entity->getPhysician()->getUser()->getLastName(), 'datec' => $dateConsPh)
                ), 'text/html'
        );
        $this->get('mailer')->send($messagePh);

        if ($entity->getModalityConsultation()->getTag() == "inlive"):
            $entity->setStatus(0);
            $entityCal->setStatus(1);
        else:
            $entity->setStatus(1);
        endif;
        $em->flush();

        //inicio generar notificacion Alerta

        $user = $this->getUser();
        $user = $user->getId();
        $tipo = "Nueva Consulta";
        $id = $entity->getId();

        $alerta = $this->createAlertaAction($id, $user, $tipo);

        //fin Notificacion

        return array(
            'patient' => $entityPat,
            'idc' => $pay->getId(),
            'cons' => $cons,
            'dateConsPt' => $dateConsPt,
        );
    }

    /**
     * Lists all Consultations entities.
     *
     * @Route("/physician/list/{type}", name="consultations_list_physician")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS')")
     */
    public function listPhysicianAction($type) {
        $em = $this->getDoctrine()->getManager();

        $entityPhy = $this->getPhysicianLog();

        if ($type == 0) {

            $entities = $em->getRepository('AppBundle:Consultations')->findAllPhysicianWithCalendar($entityPhy->getId());
            $entitiesAll = array();

            for ($i = 0; $i < count($entities); $i++) {
                $ent = $entities[$i];
                $entNext = '';
                if ($i + 1 < count($entities)):
                    $entNext = $entities[$i + 1];
                endif;
                if (is_a($ent, '\AppBundle\Entity\Consultations')):
                    $entitiesAll[] = (object) array("con" => $ent, "cal" => $entNext);
                endif;
            }
        }else {


            $entities = $em->getRepository('AppBundle:Consultations')->findAllPhysicianCalenFiltro($entityPhy->getId(), $type);
            $entitiesAll = array();

            for ($i = 0; $i < count($entities); $i++) {
                $ent = $entities[$i];
                $entNext = '';
                if ($i + 1 < count($entities)):
                    $entNext = $entities[$i + 1];
                endif;
                if (is_a($ent, '\AppBundle\Entity\Consultations')):
                    $entitiesAll[] = (object) array("con" => $ent, "cal" => $entNext);
                endif;
            }
        }


        $entitiesCon = $em->getRepository('AppBundle:Consultations')->findAllByPhysicianPending($entityPhy->getId());

        $countCon = array("total" => count($entitiesCon), "consice" => 0, "electronic" => 0, "inlive" => 0);

        for ($i = 0; $i < count($entitiesCon); $i++):
            $countCon[$entitiesCon[$i]->getModalityConsultation()->getTag()] = $countCon[$entitiesCon[$i]->getModalityConsultation()->getTag()] + 1;
        endfor;


        $qb = $em->getRepository('AppBundle:Consultations')->createQueryBuilder('a')
                ->select('COUNT(a)')
                ->leftjoin('AppBundle:Calendar', 'c', 'WITH', 'a.id=c.consultation')
                ->where('a.physician=:ph and a.status=0')
                ->setParameter('ph', $entityPhy->getId());

        $countconfirm = $qb->getQuery()->getSingleScalarResult();

        return array(
            'physician' => $entityPhy,
            'entities' => $entitiesAll,
            'countCon' => (object) $countCon,
            'type' => $type,
            'porconfirm' => $countconfirm
        );
    }

    /**
     * Consultation Completed.
     *
     * @Route("/request/completed/{idc}", name="consultations_completed")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")
     */
    public function consultationCompletedAction($idc) {
        $em = $this->getDoctrine()->getManager();

        $entityPat = $this->getPatientLog();

        return array(
            'patient' => $entityPat,
            'idc' => $idc,
        );
    }

    public function getPatientLog() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user === null) {
            throw $this->createNotFoundException('Unable to find user.');
        }
        $entityPat = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));
        if (!$entityPat) {
            throw $this->createNotFoundException('Unable to find Patient.');
        }

        return $entityPat;
    }

    public function getPhysicianLog() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user === null) {
            throw $this->createNotFoundException('Unable to find user.');
        }
        $entityPat = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        if (!$entityPat) {
            throw $this->createNotFoundException('Unable to find Physician.');
        }

        return $entityPat;
    }

    /**
     * @return JsonResponse
     */
    public function getDiffTzAction($tzph, $tzpt, $dt) {

        $dateTimeZonePhy = new DateTimeZone($tzph);
        $dateTimeZonePat = new DateTimeZone($tzpt);

        $dateTimePhy = new DateTime($dt, $dateTimeZonePhy);
        $dateTimePat = new DateTime($dt, $dateTimeZonePat);

        $timeOffset = $dateTimeZonePat->getOffset($dateTimePhy);

        return new JsonResponse($timeOffset / 3600);
    }

    /**
     * @Pdf()
     */
    public function showpdfAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }
        $dom = new \DOMDocument;
        if ($entity->getResume() !== null && $entity->getResume() !== ''):

            $dom->loadHTML(($entity->getResume()));
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//@*');
            foreach ($nodes as $node) {
                if ($node->nodeName !== 'style'):
                    $node->parentNode->removeAttribute($node->nodeName);
                endif;
            }
            $entity->setResume(html_entity_decode(strip_tags($dom->saveHTML(), '<p><ol><ul><li><a><div><span><strong><table><tr><td>')));

        endif;


        if ($entity->getAnswer() !== null && $entity->getAnswer() !== ''):
            $dom->loadHTML(($entity->getAnswer()));
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//@*');
            foreach ($nodes as $node) {
                if ($node->nodeName !== 'style'):
                    $node->parentNode->removeAttribute($node->nodeName);
                endif;
            }
            $entity->setAnswer(html_entity_decode(strip_tags($dom->saveHTML(), '<p><ol><ul><li><a><div><span><strong><table><tr><td>')));

        endif;



        if ($this->get('security.context')->isGranted('ROLE_PHYSICIANS')) {
            $entityPat = $entity->getPatient();            
        }else{
            $entityPat = $this->getPatientLog();
        }
        
        $facade = $this->get('ps_pdf.facade');
        $response = new Response();
        $this->render('AppBundle:Consultations:showpdf.pdf.twig', array('entity' => $entity, 'entityPat' => $entityPat,), $response);

        $xml = $response->getContent();

        $content = $facade->render($xml, DataSource::fromFile(__DIR__ . '/stylesheet.xml'));

        return new Response($content, 200, array('content-type' => 'application/pdf'));
    }

    /**
     * @Pdf()
     */
    public function showpdfmedicoAction($id, $paciente) {
        $em = $this->getDoctrine()->getManager();

        $consulta = new Consultations();

        $entity = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }

        //$headerDoc = '<temp>' . utf8_encode(html_entity_decode($consulta->getResume())) . '</temp>'; 
        //$resume = new \SimpleXMLElement($headerDoc);
        //$headerDoc2 = '<temp>' . utf8_encode(html_entity_decode($consulta->getAnswer())) . '</temp>'; 
        //$answer = new \SimpleXMLElement($headerDoc2);  
        /* $consulta->setResume(html_entity_decode($consulta->getResume()));
          $consulta->setAnswer(html_entity_decode($consulta->getAnswer())); */
        $dom = new \DOMDocument;
        if ($entity->getResume() !== null && $entity->getResume() !== ''):

            $dom->loadHTML(($entity->getResume()));
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//@*');
            foreach ($nodes as $node) {
                if ($node->nodeName !== 'style'):
                    $node->parentNode->removeAttribute($node->nodeName);
                endif;
            }
            $entity->setResume(html_entity_decode(strip_tags($dom->saveHTML(), '<p><ol><ul><li><a><div><span><strong><table><tr><td>')));

        endif;


        if ($entity->getAnswer() !== null && $entity->getAnswer() !== ''):
            $dom->loadHTML(($entity->getAnswer()));
            $xpath = new \DOMXPath($dom);
            $nodes = $xpath->query('//@*');
            foreach ($nodes as $node) {
                if ($node->nodeName !== 'style'):
                    $node->parentNode->removeAttribute($node->nodeName);
                endif;
            }
            $entity->setAnswer(html_entity_decode(strip_tags($dom->saveHTML(), '<p><ol><ul><li><a><div><span><strong><table><tr><td>')));

        endif;

        $entityPat = $em->getRepository('AppBundle:Patients')->find($paciente);


        $facade = $this->get('ps_pdf.facade');
        $response = new Response();
        $this->render('AppBundle:Consultations:showpdf.pdf.twig', array('entity' => $entity, 'entityPat' => $entityPat,), $response);

        $xml = $response->getContent();

        $content = $facade->render($xml, DataSource::fromFile(__DIR__ . '/stylesheet.xml'));

        return new Response($content, 200, array('content-type' => 'application/pdf'));
    }

    /**
     * @Pdf()
     */
    public function showInvoicepdfAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Payment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Payment entity.');
        }

        $entityPat = $this->getPatientLog();
        $entityCon = $em->getRepository('AppBundle:Consultations')->find($entity->getIdp());

        $facade = $this->get('ps_pdf.facade');
        $response = new Response();

        $dateTimeZonePat = new \DateTimeZone($entityPat->getTimezone());
        $dateTimeR = new \DateTime($entity->getDate()->format("d-m-Y H:i:s"), $dateTimeZonePat);

        $this->render('AppBundle:Consultations:showInvoicepdf.pdf.twig', array('entity' => $entity, 'entityPat' => $entityPat, 'entityCon' => $entityCon, 'dateR' => $dateTimeR,), $response);

        $xml = $response->getContent();

        $content = $facade->render($xml, DataSource::fromFile(__DIR__ . '/stylesheet.xml'));

        return new Response($content, 200, array('content-type' => 'application/pdf'));
    }

    public function getFieldsForms($entity, $em, $actu = false, $filter = null, $user = null, $idC = null, $action = null, $page = 0) {


        if ($user === null && $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.context')->getToken()->getUser();
        }

        $fieldsFilter = array();
        $fieldsetsFilter = array();
        if ($filter !== null):
            $filters = $em->getRepository('AppBundle:MedicalFormsViews')->find($filter);
            if ($filters):
                if ($filters->getFields() !== ''):
                    $fieldsFilter = explode(',', $filters->getFields());
                endif;
                if ($filters->getFieldsets() !== ''):
                    $fieldsetsFilter = explode(',', $filters->getFieldsets());
                endif;
            endif;
        endif;

        $entities = $em->getRepository('AppBundle:MedicalFormsFieldsets')->findBy(array("medicalForm" => $entity->getId()), array("position" => "ASC"));
        $entitiesAll = array();
        $entitiesbyPage = array();



        $entityset = (object) array("fieldset" => '', "fields" => '');
        $classColor = array("azulOscuro blancoColor", "celeste", "rojo", "gris", "lila", "celeste", "rojoFuerte", "azulNormal");
        $itc = 0;
        $contPage = 0;
        if ($page == 0 && count($entities) > 0) {
            $page = $entities[0]->getId();
        }
        foreach ($entities as $entityFs) :
            if (1):

                if (($filter === null ) || (in_array($entityFs->getId(), $fieldsetsFilter))):
                    $classC = ($entityFs->getType() == "page") ? $classColor[$itc] : "";
                    $entityset = (object) array("fieldset" => '', "fields" => '', "classColor" => $classC);
                    $itc = ($entityFs->getType() == "page") ? (($itc === count($classColor) - 1) ? 0 : $itc + 1) : $itc;
                    $entityset->fieldset = $entityFs;
                    $rsm = new ResultSetMappingBuilder($em);
                    $rsm->addRootEntityFromClassMetadata('AppBundle\Entity\MedicalFormsFields', 'f');

                    $nameTableCont = $entity->getFormName();
                    if ($idC !== null && $action === null):
                        $nameTableCont = $nameTableCont . "_view";
                    endif;


                    if ($user !== NULL && $actu === false):
                        if ($idC !== null && $action === null):
                            $query = $em->createNativeQuery(""
                                    //. "SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og, FV.value_data as value_temp, FV.key_enc as key_enc FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid ) F2 ON F2.subgroup=F3.subgroup LEFT JOIN _mffd_" . $nameTableCont . " FV ON FV.medical_forms_field_name=F3.name AND FV.fos_user_id=:idu AND (FV.consultation_id=(SELECT consultation_id FROM _mffd_informaci_n_general_view order by consultation_id desc LIMIT 1 ) Or FV.consultation_id is null ) WHERE F3.medical_forms_fieldset_id=:id  ORDER BY oi ASC,  og  ASC, orderid ASC"
                                    . "CALL GetFieldsByFieldsetUserCons(:id,:idu,'" . $nameTableCont . "',:cons)"
                                    . "", $rsm);
                            $query->setParameter('cons', $idC);
                        else:
                            $query = $em->createNativeQuery(""
                                    //. "SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og, FV.value_data as value_temp, FV.key_enc as key_enc FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid ) F2 ON F2.subgroup=F3.subgroup LEFT JOIN _mffd_" . $nameTableCont . " FV ON FV.medical_forms_field_name=F3.name AND FV.fos_user_id=:idu  WHERE F3.medical_forms_fieldset_id=:id ORDER BY oi ASC,  og  ASC, orderid ASC "
                                    . "CALL GetFieldsByFieldsetUser(:id,:idu,'" . $nameTableCont . "')"
                                    . "", $rsm);
                        endif;
                        $query->setParameter('idu', $user->getId());
                    else:
                        $query = $em->createNativeQuery(""
                                //. "SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid) F2 ON F2.subgroup=F3.subgroup WHERE F3.medical_forms_fieldset_id=:id ORDER BY oi ASC,  og  ASC, orderid ASC "
                                . "CALL GetFieldsByFieldset(:id)"
                                . "", $rsm);
                    endif;
                    $query->setParameter('id', $entityFs->getId());


                    $entitiesFl = $query->getResult(); //$query->getResult();


                    $entitiesFlAux = array();
                    foreach ($entitiesFl as $field) :
                        if (in_array($field->getName(), $fieldsFilter)):
                            $entitiesFlAux[] = $field;
                        endif;
                    endforeach;
                    if ($filter !== null):
                        $entitiesFl = $entitiesFlAux;
                    endif;

                    $entityset->fields = $entitiesFl;
                    array_push($entitiesAll, $entityset);

                    if ($entityFs->getType() != "page"):
                        if ($entityFs->getPage() !== null):
                            $entitiesbyPage[$entityFs->getPage()->getId()][$entityFs->getId()] = $entityset;
                        else:
                            $entitiesbyPage[$entityFs->getId()]['field'] = $entityFs;
                            $entitiesbyPage[$entityFs->getId()][$entityFs->getId()] = $entityset;
                        endif;
                    else:
                        $entitiesbyPage[$entityFs->getId()]['field'] = $entityFs;
                    endif;
                endif;
            endif;
            $contPage++;
        endforeach;



        return $entitiesbyPage;
    }

    /**
     * @return JsonResponse
     */
    public function webjobsAction() {
        $em = $this->getDoctrine()->getManager();

        $entityCon = new Consultations();
        $entityCal = new Calendar();


        $fromDate = new \DateTime('now');
        $fromDate->setTime(0, 0, 0);
        $sDate = clone $fromDate;
        $sDate->modify('+1 day');
        $eDate = clone $sDate;
        $eDate->modify('+1 day');

        $listCal = $em->getRepository('AppBundle:Calendar')->findAllByDatetime($sDate->format('Y-m-d H:i:s'), $eDate->format('Y-m-d H:i:s'));
        $listCalJs = array();
        if (is_array($listCal)):
            foreach ($listCal as $cal) :
                $listCalJs[] = $cal->getConsultation()->getId();

                $dateConsPt = $cal->getDatetimePatient()->format("d-m-Y") . " A LAS " . $cal->getDatetimePatient()->format("H:i");

                $message = \Swift_Message::newInstance()
                        ->setSubject('Recordatorio de Videollamada')
                        ->setFrom('noreply@medeconsult.com')
                        ->setTo($cal->getConsultation()->getPatient()->getUser()->getEmail())
                        ->setCc('marianamff@gmail.com')
                        ->setBody(
                        $this->renderView(
                                'AppBundle:Consultations:email.reminberpat.html.twig', array('email' => $cal->getConsultation()->getPatient()->getUser()->getEmail(), 'type' => $cal->getConsultation()->getModalityConsultation()->getTag(), 'cons' => $cal->getConsultation()->getModalityConsultation()->getName(), 'pat_name' => $cal->getConsultation()->getPatient()->getUser()->getName(), 'phy_name' => $cal->getConsultation()->getPhysician()->getUser()->getName(), 'datec' => $dateConsPt)
                        ), 'text/html'
                );
                $this->get('mailer')->send($message);

                $dateConsPh = $cal->getDatetimeConsultation()->format("d-m-Y") . " A LAS " . $cal->getDatetimeConsultation()->format("H:i");

                $message = \Swift_Message::newInstance()
                        ->setSubject('Recordatorio de Videollamada')
                        ->setFrom('noreply@medeconsult.com')
                        ->setTo($cal->getConsultation()->getPhysician()->getUser()->getEmail())
                        ->setCc('marianamff@gmail.com')
                        ->setBody(
                        $this->renderView(
                                'AppBundle:Consultations:email.reminberphy.html.twig', array('email' => $cal->getConsultation()->getPhysician()->getUser()->getEmail(), 'type' => $cal->getConsultation()->getModalityConsultation()->getTag(), 'cons' => $cal->getConsultation()->getModalityConsultation()->getName(), 'pat_name' => $cal->getConsultation()->getPatient()->getUser()->getName(), 'phy_name' => $cal->getConsultation()->getPhysician()->getUser()->getName(), 'datec' => $dateConsPh)
                        ), 'text/html'
                );
                $this->get('mailer')->send($message);

            endforeach;
        endif;

        return new JsonResponse($listCalJs);
    }

}
