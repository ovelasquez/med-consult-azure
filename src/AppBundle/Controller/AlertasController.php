<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Alertas;
use AppBundle\Form\AlertasType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Alerta controller.
 *
 * @Route("/alertas")
 */
class AlertasController extends Controller
{

    
    /**
     * Lists all Alertas entities.
     *
     * @Route("/", name="alertas")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS')") 
     */


    public function alertaAction(Request $request)
    {
        $user=$this->getUser();
        $user->getId();

        $em = $this->getDoctrine()->getManager();
        $entities = $em->getRepository('AppBundle:Alertas')->findBy(array('status' =>"Activo" , 'user' => $user->getId()
         ));
        
               
        //$session = $request->getSession();
        
        $status="Activo";    

        
       $tipo1='Nueva Consulta';
       $tipo2='Respuesta de Consulta';
       $tipo3='Nuevo Mensaje';
        

        $qb = $em->getRepository('AppBundle:Alertas')->createQueryBuilder('a')
           ->select('COUNT(a.status)')
           ->where(' a.tipo = :tipo AND a.status = :act AND a.user = :usr')
           ->setParameter('act', $status)
            ->setParameter('tipo', $tipo1)
           ->setParameter('usr', $user);
        $countNew = $qb->getQuery()->getSingleScalarResult();


        $qb = $em->getRepository('AppBundle:Alertas')->createQueryBuilder('a')
           ->select('COUNT(a.status)')
           ->where(' a.tipo = :tipo AND a.status = :act AND a.user = :usr')
           ->setParameter('act', $status)
           ->setParameter('tipo', $tipo2)
           ->setParameter('usr', $user);
        $countResp = $qb->getQuery()->getSingleScalarResult();



        $qb = $em->getRepository('AppBundle:Alertas')->createQueryBuilder('a')
           ->select('COUNT(a.status)')
           ->where(' a.tipo = :tipo AND a.status = :act AND a.user = :usr')
           ->setParameter('act', $status)
           ->setParameter('tipo', $tipo3)
           ->setParameter('usr', $user);
        $countMsg = $qb->getQuery()->getSingleScalarResult();

        /*$session->set('count', $count);
        //$session->set('alertas', $entities);
        $alert=$session->set('alertas', array(
      'alertas' => $entities,));*/
            //header("Refresh: 4; URL='pagina.php'");
           
            return $this->render('AppBundle:Alertas:index.html.twig', array(
                        'alertas' =>$entities,
                        'countNew' => $countNew,
                        'countResp' => $countResp,
                        'countMsg' => $countMsg,

            ));


          
    }


   
    /**
     * Creates a new Alertas type entity.
     *
     * @Route("/create/{usr}/{id}", name="alertas_create")
     * @Method("POST")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS')")
     */
    public function createAction(Request $request)
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


        $form = $this->createCreateForm($entity);
       // $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('alertas_show', array('id' => $entity->getId())));
        }

        return $this->render('AppBundle:Alertas:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

   
    /**
     * Edits an existing Alertas entity.
     *
     * @Route("/{id}/update", name="alertas_update")
     * @Security("has_role('ROLE_ADMIN') or has_role('ROLE_PHYSICIANS') or has_role('ROLE_PATIENT') ")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Alertas')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Alertas entity.');
        }


        $entity->setStatus("Desactivada");
        $entity->setDesatdate(new \DateTime('now'));
        $em->flush();
   
        if ($entity->getTipo() == 'Nueva Consulta') {
                
        return $this->redirect($this->generateUrl('consultations_edit',
             array('id' =>$entity->getConsultation()->getId())));

         }elseif ($entity->getTipo() == 'Respuesta de Consulta') {

              return $this->redirect($this->generateUrl('consultations_show',
             array('id' =>$entity->getConsultation()->getId())));
         
         }else{
           
            //usuario de sesion y id de consulta si es physicians se pasa el doctor sino el paciente y la consulta 

         $user = $this->getUser();
         $user->getId();
         $usuario = $em->getRepository('AppUserBundle:User')->find($user);
         $usuario->getId();


        $pacient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $usuario));
         
         if($pacient) {

                // alerta para el medico get phisians {{ path('messages_new',{'usr': entity.patient.user.id,'id':entity.id}) }}
            $phisians = $em->getRepository('AppBundle:Consultations')->find($entity->getConsultation()->getId());
            $med = $phisians->getPhysician();
            $medico = $em->getRepository('AppBundle:Physicians')->find($med);
            $usermsg = $medico->getUser();
            
             return $this->redirect($this->generateUrl('messages_new',
             array('usr'=>$usermsg->getId(),
                    'id'=>$entity->getConsultation()->getId()
            )));


                 }else {

            // alerta para el paciente get patient
            $patient = $em->getRepository('AppBundle:Consultations')->find($entity->getConsultation()->getId());
            $pac = $patient->getPatient();
            $paciente = $em->getRepository('AppBundle:Patients')->find($pac);
            $usermsg = $paciente->getUser();
            
            return $this->redirect($this->generateUrl('messages_new',
             array('usr'=>$usermsg->getId(),
                    'id'=>$entity->getConsultation()->getId()
            )));

         }

         
       }


     }
        

            
    

   
}
