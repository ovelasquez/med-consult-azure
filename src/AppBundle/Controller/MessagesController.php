<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\View\TwitterBootstrapView;
use AppBundle\Entity\Alertas;
use AppBundle\Entity\Messages;
use AppBundle\Form\MessagesType;
use AppBundle\Form\MessagesFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Messages controller.
 *
 * @Route("/messages")
 */
class MessagesController extends Controller {

    /**
     * Creates a new Messages entity.
     *
     * @Route("{consul}/create", name="messages_create")
     * @Method("POST")
     * @Template("AppBundle:Messages:new.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')or has_role('ROLE_PHYSICIANS')")
     */
    public function createAction(Request $request, $consul) {

        $user = $this->getUser();
        $user = $user->getId();

        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('AppUserBundle:User')->find($user);

        if (!$usuario) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }

        $entity = new Messages();
        $entity->setFrommsg($usuario);

        $entity->setCreateDate(new \DateTime('now'));

        $form = $this->createForm(new MessagesType(), $entity);
        $form->bind($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();


            $this->get('session')->getFlashBag()->add('success', 'flash.create.success');


            //obtener el correo para $to

            $email = $em->getRepository('AppUserBundle:User')->find($entity->getTomsg());
            $to = $email->getId();
            $mailer = $email->getEmail();

            //luego de crear mensaje enviar un correo al para ($to) $mailer
            $message = \Swift_Message::newInstance()
                    ->setSubject('Tiene un nuevo mensaje MedeConsult')
                    ->setFrom('noreply@medeconsult.com')
                    ->setTo($mailer)
                    ->setBody(
                    $this->renderView(
                            'AppBundle:Messages:email.txt.twig', array()
                    ), 'text/html'
            );
            $this->get('mailer')->send($message);

             $idconsul = $em->getRepository('AppBundle:Consultations')->find($consul);
          if (!$idconsul ) {
            throw $this->createNotFoundException('no se encontro la consulta.');
        }

         //inicio generar notificacion Alerta

               
                $tipo ="Nuevo Mensaje";
                $id= $entity->getConsultation()->getId();

              $alerta = $this->createAlertaAction($id, $user, $tipo);

              //fin Notificacion



            return $this->redirect($this->generateUrl('messages_new', array('usr' => $to, 'id' => $idconsul->getId(),
            )));
        }



        $queryBuilder = $em->getRepository('AppBundle:Messages')->createQueryBuilder('e')
                ->where('e.frommsg = :usr OR e.tomsg = :usr ')
                ->andwhere('e.consultation = :cl')
                ->setParameter('usr', $user)
                ->setParameter('cl', $idconsul->getId())
                ->orderBy('e.createdate', 'DESC');
        // get the Query from the QueryBuilder here ...
        $query = $queryBuilder->getQuery();
        // ... then call getResult() on the Query (not on the QueryBuilder)
        $mensaje = $query->getResult();

        $dias = $this->dias($entity->getConsultation()->getId());
        $entityPat = $this->getPatientLog();

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'mensaje' => $mensaje,
            'dias' => $dias->format('%a'),
            'patient' => $entityPat,
        );
    }


    public function createAlertaAction($id, $user, $tipo)
    {

        //crear alerta traer datos??

        // el usuario en sesion es medico o paciente??


        $em = $this->getDoctrine()->getManager();

        $usuario = $em->getRepository('AppUserBundle:User')->find($user);
        $usuario->getId();


        $pacient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $usuario));
         
         if($pacient) {

                // alerta para el medico get phisians
            $phisians = $em->getRepository('AppBundle:Consultations')->find($id);
            $med = $phisians->getPhysician();
            $medico = $em->getRepository('AppBundle:Physicians')->find($med);
            $userAlert = $medico->getUser();

         }else {

            // alerta para el paciente get patient
            $patient = $em->getRepository('AppBundle:Consultations')->find($id);
            $pac = $patient->getPatient();
            $paciente = $em->getRepository('AppBundle:Patients')->find($pac);
            $userAlert = $paciente->getUser();

         }

          
        $usuario = $em->getRepository('AppUserBundle:User')->find($userAlert);

        if (!$usuario ) {
            throw $this->createNotFoundException('no se encontro el usuario.');
        }

        //generar alerta

        $entity = new Alertas();
        $entity->setUser($usuario);
        $entity->setTipo($tipo);
        $entity->setStatus("Activo");
        $entity->setCreatedate(new \DateTime('now'));

        $idconsul = $em->getRepository('AppBundle:Consultations')->find($id);
        if (!$idconsul ) {
            throw $this->createNotFoundException('no se encontro la consulta.');
        }
        $entity->setConsultation($idconsul);

            //$em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return true;
        }

    /**
     * Displays a form to create a new Messages entity.
     *
     * @Route("/new/{usr}/{id}", name="messages_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')or has_role('ROLE_PHYSICIANS')")
     */
    public function newAction($usr, $id) {

        $em = $this->getDoctrine()->getManager();

        $consulta = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$consulta) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }

        $user = $em->getRepository('AppUserBundle:User')->find($usr);

        if (!$user) {
            throw $this->createNotFoundException('no se encontro el usuario to');
        }

        $from = $this->getUser();
        $from = $from->getId();


        $entity = new Messages();
        $entity->setConsultation($consulta);
        $entity->setTomsg($user);

        $form = $this->createForm(new MessagesType(), $entity);

        $queryBuilder = $em->getRepository('AppBundle:Messages')->createQueryBuilder('e')
                ->where('e.frommsg = :usr OR e.tomsg = :usr ')
                ->andwhere('e.consultation = :cl')
                ->setParameter('usr', $from)
                ->setParameter('cl', $consulta->getId())
                ->orderBy('e.createdate', 'DESC');
        // get the Query from the QueryBuilder here ...
        $query = $queryBuilder->getQuery();

        // ... then call getResult() on the Query (not on the QueryBuilder)
        $mensaje = $query->getResult();

        $dias = $this->dias($id);
        //echo $dias->format('%a');
        //
        $entityPat = $this->getPatientLog();

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'mensaje' => $mensaje,
            'dias' => $dias->format('%a'),
            'patient' => $entityPat,
        );
    }

    /**
     * Displays a form to create a new Messages entity.
     *
     * @Route("/show/{usr}/{id}", name="messages_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAction($usr, $id) {

        $em = $this->getDoctrine()->getManager();

        $consulta = $em->getRepository('AppBundle:Consultations')->find($id);

        if (!$consulta) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }

        $user = $em->getRepository('AppUserBundle:User')->find($usr);

        if (!$user) {
            throw $this->createNotFoundException('no se encontro el usuario to');
        }

        $from = $consulta->getPatient()->getUser();
        $from = $from->getId();


        $entity = new Messages();
        $entity->setConsultation($consulta);
        $entity->setTomsg($user);

        $form = $this->createForm(new MessagesType(), $entity);

        $queryBuilder = $em->getRepository('AppBundle:Messages')->createQueryBuilder('e')
                ->where('e.consultation = :cl')
                ->setParameter('cl', $consulta->getId())
                ->orderBy('e.createdate', 'DESC');
        // get the Query from the QueryBuilder here ...
        $query = $queryBuilder->getQuery();

        // ... then call getResult() on the Query (not on the QueryBuilder)
        $mensaje = $query->getResult();

        $dias = $this->dias($id);
        //echo $dias->format('%a');
        //
        $entityPat = $this->getPatientLog();

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'mensaje' => $mensaje,
            'dias' => $dias->format('%a'),
            'patient' => $entityPat,
        );
    }
    
    
    public function dias($id) {

        $em = $this->getDoctrine()->getManager();
        $conn = $em->getConnection();
        $fecha = $conn->fetchArray('SELECT date(`update_date`) FROM `consultations` WHERE `id`=?', array($id));
        $fech = $fecha[0];
        $datetime1 = new \DateTime($fech);
        $datetime2 = new \DateTime('now');
        $interval = $datetime1->diff($datetime2);

        return $interval;
    }

    public function getPatientLog() {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        if ($user === null) {
            throw $this->createNotFoundException('Unable to find user.');
        }
        $entityPat = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user->getId()));
        if (!$entityPat) {
            $entityPat = "no es paciente";
        }

        return $entityPat;
    }

}
