<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Physicians;
use AppBundle\Form\PhysiciansType;
use AppBundle\Form\PhysiciansEditAdminType;
use AppBundle\Form\PhysiciansEditType;
use AppBundle\Form\PhysiciansEditAvailabilityType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use AppBundle\Entity\Contents;
use AppBundle\Entity\PrePhysicians;
use AppBundle\Entity\Patients;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use AppBundle\Entity\Calendar;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 * Physicians controller.
 *
 * @Route("/physicians")
 */
class PhysiciansController extends Controller {

    /**
     * Lists all Physicians entities.
     *
     * @Route("/", name="physicians")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")        
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $twig = $this->container->get('twig');
        $gl = $twig->getGlobals();

        $entities = $em->getRepository('AppBundle:Physicians')->findBy(array(), array('id' => 'asc'), (int) $gl["for_page"]);

        $qb = $em->getRepository('AppBundle:Physicians')->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $count = $qb->getQuery()->getSingleScalarResult();

        return array(
            'entities' => $entities,
            'p' => 1,
            'pCount' => ceil($count / (int) $gl["for_page"])
        );
    }

    /**
     * Lists all Physicians entities pagination.
     *
     * @Route("/pag/{p}", name="physicians_pag")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function indexPagAction($p) {
        $em = $this->getDoctrine()->getManager();

        $twig = $this->container->get('twig');
        $gl = $twig->getGlobals();

        $entities = $em->getRepository('AppBundle:Physicians')->findBy(array(), array('id' => 'asc'), $gl["for_page"], ((int) $p - 1) * $gl["for_page"]);

        $qb = $em->getRepository('AppBundle:Physicians')->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $count = $qb->getQuery()->getSingleScalarResult();

        return array(
            'entities' => $entities,
            'p' => $p,
            'pCount' => ceil($count / (int) $gl["for_page"])
        );
    }

    /**
     * Lists all Physicians entities public.
     *
     * @Route("/medicos", name="physicians_list")
     * @Method("GET")
     * @Template("AppBundle:Physicians:list.html.twig")
     *    
     */
    public function listAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Physicians')->findAll(); //CAMBIAR A SOLO ACTIVOS  

        $submenu = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => 'menu-acerca-medecons')
        );
        $submenu->setProcBody($this->generateUrl('homepage'));



        return array(
            'entities' => $entities,
            'tag' => 'acerca-medeconsult',
            'subtag' => 'medicos',
            'submenu' => $submenu,
        );
    }

    /**
     * Creates a new Physicians entity.
     *
     * @Route("/", name="physicians_create")
     * @Method("POST")
     * @Template("AppBundle:Physicians:new.html.twig")
     * 
     */
//    public function createAction(Request $request) {
//        $entity = new Physicians();
//        $form = $this->createCreateForm($entity);
//        $form->handleRequest($request);
//
//        if ($form->isValid()) {
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($entity);
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('physicians_show', array('id' => $entity->getId())));
//        }
//
//        return array(
//            'entity' => $entity,
//            'form' => $form->createView(),
//        );
//    }

    /**
     * Creates a form to create a Physicians entity.
     *
     * @param Physicians $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createCreateForm(Physicians $entity) {
//        $form = $this->createForm(new PhysiciansType(), $entity, array(
//            'action' => $this->generateUrl('physicians_create'),
//            'method' => 'POST',
//        ));
//
//        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submit btnAdm rojoFuerte')));
//
//        return $form;
//    }

    /**
     * Displays a form to create a new Physicians entity.
     *
     * @Route("/new", name="physicians_new")
     * @Method("GET")
     * @Template()
     * 
     */
//    public function newAction() {
//        $entity = new Physicians();
//        $form = $this->createCreateForm($entity);
//
//        return array(
//            'entity' => $entity,
//            'form' => $form->createView(),
//        );
//    }

    /**
     * Finds and displays a Physicians entity.
     *
     * @Route("/{id}", name="physicians_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Physicians')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Physicians entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Finds and displays a Physicians entity.
     *
     * @Route("/{id}/profile", name="physicians_show_public")
     * @Method("GET")
     * @Template()
     * 
     */
    public function showPublicAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Physicians')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Physicians entity.');
        }

        $submenu = $em->getRepository('AppBundle:Contents')->findOneBy(
                array('status' => 1, 'tag' => 'menu-acerca-medecons')
        );
        $submenu->setProcBody($this->generateUrl('homepage'));

        return array(
            'entity' => $entity,
            'submenu' => $submenu,
            'subtag' => 'medicos',
            'tag' => 'acerca-medeconsult',
        );
    }

    /**
     * Displays a form to edit an existing Physicians entity.
     *
     * @Route("/{id}/edit", name="physicians_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")        
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Physicians')->find($id);

        if (!$entity) {           
            
            if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                throw $this->createNotFoundException('Unable to find Physicians entity.');
            }else{
                $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array("user" => $id));
                if (!$entity) throw $this->createNotFoundException('Unable to find Physicians entity.');
            }
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
     * Creates a form to edit a Physicians entity.
     *
     * @param Physicians $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Physicians $entity) {
        $form = $this->createForm(new PhysiciansEditAdminType(), $entity, array(
            'action' => $this->generateUrl('physicians_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }

    /**
     * Edits an existing Physicians entity.
     *
     * @Route("/{id}", name="physicians_update")
     * @Method("PUT")
     * @Template("AppBundle:Physicians:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Physicians')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Physicians entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('physicians_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Physicians entity.
     *
     * @Route("/{id}", name="physicians_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Physicians')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Physicians entity.');
            }

            $userManager = $this->get('fos_user.user_manager');
            $entity->getUser()->setEnabled(!$entity->getUser()->isEnabled());
            $userManager->updateUser($entity->getUser());
//            $em->remove($entity);
//            $em->flush();
        }

        return $this->redirect($this->generateUrl('physicians'));
    }

    /**
     * Creates a form to delete a Physicians entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('physicians_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Eliminar', 'attr' => array('class' => 'submit btnAdm lila')))
                        ->getForm()
        ;
    }

    //FRONT

    /**
     * Creates a new Physicians entity.
     *
     * @Route("/registro", name="physicians_create_front")
     * @Method("POST")
     * @Template("AppBundle:Physicians:new_front.html.twig")
     */
    public function createfrontAction(Request $request) {
        $entity = new Physicians();
        $form = $this->createCreateFrontForm($entity);
        $form->handleRequest($request);

        $userExist = false;
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        if (isset($request->request->get("appbundle_physicians")["user"]["email"]) && null !== $user = $userManager->findUserByEmail($request->request->get("appbundle_physicians")["user"]["email"])):
            $userExist = true;
            $form->get('user')->get('email')->addError(new FormError('El correo está en uso'));
        endif;
        if (isset($request->request->get("appbundle_physicians")["user"]["username"]) && null !== $user = $userManager->findUserByUsername($request->request->get("appbundle_physicians")["user"]["username"])):
            $userExist = true;
            $form->get('user')->get('username')->addError(new FormError('El usuario está en uso'));
        endif;

        $errors = $this->get('app.errorsform')->getErrors($form);


//        echo "<pre>".var_dump($errors)."</pre>";
//        exit(\Doctrine\Common\Util\Debug::dump($errors));

        if ($form->isValid() && !$userExist) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            /*             * ** PPHY INI************************* */
            $entity->getPrePhysician()->setPhysician($entity);
            $em->persist($entity->getPrePhysician());
            $em->flush();
            /*             * ** PPHY FIN************************* */

            /*             * ** USER INI************************* */
            $user = $entity->getUser();
            $user->addRole('ROLE_PHYSICIANS');
            $user->setEnabled(true);

            //$userManager->updateUser($user);
            /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
            $dispatcher = $this->get('event_dispatcher');

            $event = new GetResponseUserEvent($user, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

            $event = new FormEvent($form, $request);
            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

            if (null === $response = $event->getResponse()) {
                $url = $this->generateUrl('fos_user_registration_confirmed');
                $response = new RedirectResponse($url);
            }
            $userManager->updateUser($user);

            $regCompleted = true;

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            /*             * ** USER FIN************************* */

            $message = \Swift_Message::newInstance()
                    ->setSubject('¡FELICIDADES! TU REGISTRO SE HA COMPLETADO')
                    ->setFrom('noreply@medeconsult.com')
                    ->setTo($entity->getUser()->getEmail())
                    ->setBody(
                    $this->renderView(
                            'AppBundle:Physicians:email.welcome.html.twig', array('email' => $entity->getUser()->getEmail(), 'username' => $entity->getUser()->getName())
                    ), 'text/html'
            );
            $this->get('mailer')->send($message);

            return $this->redirect($this->generateUrl('physicians_view_front', array('id' => $user->getId())));
        }
//        dump($errors);
        //$errors = array();
//        foreach ($form->all() as $child) {
//            if (!$child->isValid()) {
//                $children = $child->all();
//                if (!is_array($children)):
//                    $errors[] = $child->getErrors()->getChildren()->getMessage();
//                else:
//                    foreach ($child->all() as $schild) {
//                        if (!$schild->isValid()) {
//                            if (!is_array($schild->getErrors()->getChildren())):
//                                $errors[] = $schild->getErrors()->getChildren()->getMessage();
//                            endif;
//                        }
//                    }
//                endif;
//            }
//        }

        $errors_txt = array();
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($errors));
        foreach ($it as $v) {
            $errors_txt[] = $v;
        }
//        dump((array_merge($errors_txt)));
//        die();
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tag' => "registro",
            'messages' => $errors_txt,
        );
    }

    /**
     * Creates a form to create a Physicians entity.
     *
     * @param Physicians $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateFrontForm(Physicians $entity) {
        $form = $this->createForm(new PhysiciansType(), $entity, array(
            'action' => $this->generateUrl('physicians_create_front'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Physicians entity.
     *
     * @Route("/registro/medicos/{t}", name="physicians_new_front")
     * @Method("GET")
     * @Template("AppBundle:Physicians:new_front.html.twig")
     */
    public function newfrontAction($t) {
        $user = $this->getUser();
        if (null !== $user && $user->hasRole("ROLE_PHYSICIANS")) :
            return $this->redirect($this->generateUrl('physicians_view_front', array('id' => $user->getId())));
        endif;


        $em = $this->getDoctrine()->getManager();
        $entityP = $em->getRepository('AppBundle:PrePhysicians')->findOneBy(array('confirmationToken' => $t, 'physician' => NULL));

        if (!$entityP) {
            throw $this->createNotFoundException('Unable to find confirmation Token.');
        }

        $entity = new Physicians();
        $entity->setPrePhysician($entityP);

        $form = $this->createCreateFrontForm($entity);

        //exit(\Doctrine\Common\Util\Debug::dump($form->get('user')->get('name')));

        $name = explode('.', $entityP->getName());
        $form->get('user')->get('name')->setData($name[0] . '.');
        $form->get('user')->get('lastname')->setData($name[1]);
        $form->get('user')->get('email')->setData($entityP->getEmail());
        $form->get('abms')->setData($entityP->getAbms());

        //$form->get('user')->remove('lastname');

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tag' => "registro",
        );
    }

    /**
     * Finds and displays a Physicians entity.
     *
     * @Route("/medico/{id}", name="physicians_show_front")
     * @Method("GET")
     * @Template("AppBundle:Physicians:show_front.html.twig")      
     * @Security("has_role('ROLE_PHYSICIANS') or has_role('ROLE_ADMIN')")    
     */
    public function showfrontAction($id) {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));


        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Physicians entity.');
        }


        if (null === $entity->getDatetimeAvailable() || 'null' === $entity->getDatetimeAvailable() || '' === $entity->getDatetimeAvailable()) {
            return $this->redirect($this->generateUrl('physicians_edit_availability', array('id' => $user->getId())));
        }

        $entitiesCon2 = $em->getRepository('AppBundle:Consultations')->findAllByPhysicianPending($entity->getId());


        $countCon = array("total" => count($entitiesCon2), "consice" => 0, "electronic" => 0, "inlive" => 0);

        for ($i = 0; $i < count($entitiesCon2); $i++):
            $countCon[$entitiesCon2[$i]->getModalityConsultation()->getTag()] = $countCon[$entitiesCon2[$i]->getModalityConsultation()->getTag()] + 1;
        endfor;


        $entitiesCon = $em->getRepository('AppBundle:Consultations')->findAllPhysicianWithCalendar($entity->getId(), 10);

        $entitiesAll = array();

        for ($i = 0; $i < count($entitiesCon); $i++) {
            /*  if ( ! isset($entitiesCon[$i])) {
              $entitiesCon[$i] = null;
              } */
            $ent = $entitiesCon[$i];
            $entNext = '';
            if ($i + 1 < count($entitiesCon)):
                $entNext = $entitiesCon[$i + 1];
            endif;
            if (is_a($ent, '\AppBundle\Entity\Consultations')):
                $entitiesAll[] = (object) array("con" => $ent, "cal" => $entNext);
            endif;
        }


        return array(
            'entity' => $entity,
            'tag' => "registro",
            'countCon' => (object) $countCon,
            'entities' => $entitiesAll,
        );
    }

    /**
     * Creates a new Patients entity.
     *
     * @Route("/editar/{id}", name="physicians_update_profile")
     * @Method("PUT")
     * @Template("AppBundle:Physicians:update_profile.html.twig")
     * @Security("has_role('ROLE_PHYSICIANS') or has_role('ROLE_ADMIN')")  
     */
    public function updateprofileAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            $user = $this->getUser();
            $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Physicians entity.');
            }
        }else{
            $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array("user" => $id));
            if (!$entity):                
                throw $this->createNotFoundException('Unable to find Physicians entity.');
            else:
                $user = $entity->getUser();           
            endif;
        }
            
        //$user = $this->getUser();
       
        $username = $user->getUsername();
        $params = $request->request->all();
        //$params['appbundle_physicians_edit']['user']['username'] = $username;
        //$params['appbundle_physicians_edit']['user']['lastname'] = ' ';
        $request->request->add($params);

        $editForm = $this->updateprofileCreateForm($entity);
        $editForm->handleRequest($request);
        $errors = $this->get('app.errorsform')->getErrors($editForm);

        
        if ($editForm->isValid()) {
            $em->flush();
            
            if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                $user = $this->getUser();
            }else{
                $user = $entity->getUser();
            }
            
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);
            
            $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
            $request->getSession()->replace(array('userInfo' => $entity));
            
            return $this->redirect($this->generateUrl('physicians_edit_profile', array('id' => $user->getId())));
        }

        return array(
            'entity' => $entity, 'form' => $editForm->createView(), 'tag' => "registro",
        );
    }

    /**
     * Creates a new Patients entity.
     *
     * @Route("/edit/availability/{id}", name="physicians_update_availability")
     * @Method("PUT")
     * @Template("AppBundle:Physicians:editAvailability.html.twig")
     * @Security("has_role('ROLE_PHYSICIANS') or has_role('ROLE_ADMIN')")  
     */
    public function updateAvailabilityAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array("user" => $user));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Physicians entity.');
        }

        $editForm = $this->updateAvailabilityCreateForm($entity);
        $editForm->handleRequest($request);

        $errors = $this->get('app.errorsform')->getErrors($editForm);

        if (1) {//$editForm->isValid()) {
            $entity->setDatetimeAvailable(json_encode($request->request->get("availability")));
            $em->flush();
            //return $this->redirect($this->generateUrl('physicians_edit_availability', array('id' => $id)));
            return array(
                'entity' => $entity,
                'form' => $editForm->createView(),
                'tag' => "",
                'physician' => $entity,
                'availabilitys' => (null !== $entity->getdatetimeAvailable() ) ? json_decode($entity->getdatetimeAvailable(), true) : array(),
            );
        }

        return array(
            'entity' => $entity, 'form' => $editForm->createView(), 'tag' => "registro", 'physician' => $entity,
            'availabilitys' => (null !== $entity->getdatetimeAvailable() ) ? json_decode($entity->getdatetimeAvailable(), true) : array(),
        );
    }

    /**
     * Creates a form to actualizar a physicians entity.
     *
     * @param Patients $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function updateprofileCreateForm(Physicians $entity) {

        $form = $this->createForm(new PhysiciansEditType(), $entity, array(
            'action' => $this->generateUrl('physicians_update_profile', array('id' => $entity->getUser()->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Creates a form to actualizar a physicians entity.
     *
     * @param Patients $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function updateAvailabilityCreateForm(Physicians $entity) {

        $form = $this->createForm(new PhysiciansEditAvailabilityType(), $entity, array(
            'action' => $this->generateUrl('physicians_update_availability', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Displays a form to actualizar a new physicians entity 
     *
     * @Route("/editar/{id}", name="physicians_edit_profile")
     * @Method("GET")
     * @Template("AppBundle:Physicians:update_profile.html.twig")
     * @Security("has_role('ROLE_PHYSICIANS') or has_role('ROLE_ADMIN')")  
     */
    public function editprofileAction($id) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array("user" => $user));

        if (!$entity) {
            //throw $this->createNotFoundException('Unable to find Physicians entity.');
            if (false === $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
                throw $this->createNotFoundException('Unable to find Physicians entity.');
            }else{
                $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array("user" => $id));
                if (!$entity) throw $this->createNotFoundException('Unable to find Physicians entity.');
            }
        }

        $editForm = $this->updateprofileCreateForm($entity);


        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'tag' => ""
        );
    }

    /**
     * Displays a form to actualizar a new physicians entity 
     *
     * @Route("/edit/availability/{id}", name="physicians_edit_availability")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PHYSICIANS') or has_role('ROLE_ADMIN')")  
     * 
     */
    public function editAvailabilityAction($id) {

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array("user" => $user));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Physicians entity.');
        }

        $editForm = $this->updateAvailabilityCreateForm($entity);

        $user = $this->getUser();
        $entityP = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));


        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'tag' => "",
            'physician' => $entityP,
            'availabilitys' => ($entity->getdatetimeAvailable() != '') ? json_decode($entity->getdatetimeAvailable(), true) : array(),
        );
    }

    /**
     * Finds and displays a Physicians entity.
     * @Route("/resultregister/{name}", name="physicians_resultregister")
     * @Template("AppBundle:Physicians:resultregister.html.twig")     
     */
    public function resultregisterAction($name) {

        return array(
            'name' => $name,
            'tag' => "registro",
        );
    }

    /**
     * @return JsonResponse
     */
    public function verifyAction($email, $username) {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');


        $userE = $userManager->findUserByEmail($email);
        $userN = $userManager->findUserByUsername($username);

        if (count($userE) > 0)
            $email = true;
        if (count($userN) > 0)
            $username = true;

        $data = array(
            'email' => $email,
            'username' => $username
        );
        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     */
    public function getdataAction($id) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Physicians')->find($id);


        $data = array();

        if (!$entity) {
            return new JsonResponse($data);
        }

        //CARGO LAS FECHAS NO DISPONIBLES DE LOS MEDICOS 

        $dateTimeZonePhy = new \DateTimeZone($entity->getTimezone());
        $now = new \DateTime("now", $dateTimeZonePhy);
        $now->setTime(0, 0, 0);
        $now->modify('+2 day');

        $entityCal = $em->getRepository('AppBundle:Calendar')->findAllByDatetimeConsultation($entity->getId(), $now->format('Y-m-d H:i:s'));

        $dataCal = array();

        $us = $this->getUser();
        $entityPat = false;
        if ($us) {
            $entityPat = $em->getRepository('AppBundle:Patients')->findOneBy(array("user" => $us->getId()));
        }

        //$dateTimeZonePhy = new \DateTimeZone($entity->getTimezone());
        $dateTimeZonePat = new \DateTimeZone($entityPat->getTimezone());

        $dateTimePhy = new \DateTime("now", $dateTimeZonePhy);
        $dateTimePat = new \DateTime("now", $dateTimeZonePat);
        $diffgmt = (($dateTimeZonePat->getOffset($dateTimePat) / 60) - ($dateTimeZonePhy->getOffset($dateTimePhy) / 60));

        //REUBICA LA FECHAS OCUPADAS DEL MEDICO SEGUN EL TIMEZONE DEL PACIENTE
        for ($i = 0; $i < count($entityCal); $i++) {
            $entityCal[$i]->getDatetimeConsultation()->modify((($diffgmt > 0) ? '+' : '-') . $diffgmt . ' minutes');
            $dataCal[] = $entityCal[$i]->getDatetimeConsultation()->format('Y-m-d H:i');
        }

        $nowPat = new \DateTime("now", $dateTimeZonePhy);
        $nowPat->setTime(7, 0, 0); // 7 hora inicio de atencion
        $nowPat->modify((($diffgmt > 0) ? '+' : '-') . abs($diffgmt) . ' minutes');


        //CALCULA LAS HORA DISPONIBLES SEGUN EL TIMEZONE ENTRE PACIENTE Y MEDICO         
        /* $options = array();
          $minIni = (int) $nowPat->format('H');
          $numOp = 22 - $minIni; // 22 hora finalizacion de atencion
          if (($numOp) > 0) {
          for ($i = $minIni; $i <= 22; $i++) {
          $min = $nowPat->format('i');
          $options[] = (strlen($i) == 1 ? '0' . $i : $i) . ':' . $min;
          }
          } */

        $dayHoursAvailables = $this->getDayHoursAvailable($entity->getDatetimeAvailable());

        //CALCULA LA PROXIMA FECHA DISPONIBLE SEGUN LA FECHA DEL MEDICO Y L DEL PACIENTE
        $now = new \DateTime("now", $dateTimeZonePhy);
        $now->setTime(0, 0, 0);
        $now->modify('+2 day');
        $now->modify(((($dateTimeZonePat->getOffset($dateTimePat) / 60) > 0) ? '+' : '-') . ($dateTimeZonePat->getOffset($dateTimePat) / 60) . ' minutes');

        $data = (object) array("name" => $entity->getUser()->getName(), "lastname" => $entity->getUser()->getLastName(), "jobtitle" => $entity->getJobtitle(), "abms" => $entity->getAbms(), "specialty_id" => $entity->getSpecialty()->getId(), "specialty" => $entity->getSpecialty()->getName(), "subspecialty" => $entity->getSubspecialty(), "languages" => $entity->getLanguages(), "photo" => $this->container->get('templating.helper.assets')->getUrl("uploads/documents/" . $entity->getPhoto()), "cal" => implode(',', $dataCal), "timezone" => $entity->getTimezone(), "difftz" => ($entityPat !== false ? $this->getDiffTz($entity->getTimezone(), $entityPat->getTimezone()) : ''), 'horas' => $dayHoursAvailables, 'nowPh' => $now->format("Y-m-d"));

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     */
    public function getdataInfoAction($id) {

        $em = $this->getDoctrine()->getManager();
        //$entity = $em->getRepository('AppBundle:Physicians')->find($id);
//        //$rsm = new ResultSetMappingBuilder($em);
//        //$rsm->addRootEntityFromClassMetadata('AppBundle\Entity\Physicians', 'f');
//        $rsm = new ResultSetMapping();
//
//        $query = $em->createNativeQuery("SELECT * FROM physicians  ",$rsm);
//        //$query->setParameter('id', $id);
//        $entitiesSets = $query->getResult();


        $stmt = $this->getDoctrine()->getEntityManager()
                ->getConnection()
                ->prepare('select p.*, u.name, u.last_name from physicians p left join fos_user u on u.id=p.user_id where p.id= :id');
        $params['id'] = $id;
        $stmt->execute($params);
        $result = $stmt->fetchAll();

        return new JsonResponse($result);

        $data = array();

        if (!$entity) {
            return new JsonResponse($data);
        }

        //CARGO LAS FECHAS NO DISPONIBLES DE LOS MEDICOS 

        $dateTimeZonePhy = new \DateTimeZone($entity->getTimezone());
        $now = new \DateTime("now", $dateTimeZonePhy);
        $now->setTime(0, 0, 0);
        $now->modify('+2 day');

        $entityCal = $em->getRepository('AppBundle:Calendar')->findAllByDatetimeConsultation($entity->getId(), $now->format('Y-m-d H:i:s'));

        $dataCal = array();

        $us = $this->getUser();
        $entityPat = false;
        if ($us) {
            $entityPat = $em->getRepository('AppBundle:Patients')->findOneBy(array("user" => $us->getId()));
        }

        //$dateTimeZonePhy = new \DateTimeZone($entity->getTimezone());
        $dateTimeZonePat = new \DateTimeZone($entityPat->getTimezone());

        $dateTimePhy = new \DateTime("now", $dateTimeZonePhy);
        $dateTimePat = new \DateTime("now", $dateTimeZonePat);
        $diffgmt = (($dateTimeZonePat->getOffset($dateTimePat) / 60) - ($dateTimeZonePhy->getOffset($dateTimePhy) / 60));

        //REUBICA LA FECHAS OCUPADAS DEL MEDICO SEGUN EL TIMEZONE DEL PACIENTE
        for ($i = 0; $i < count($entityCal); $i++) {
            $entityCal[$i]->getDatetimeConsultation()->modify((($diffgmt > 0) ? '+' : '-') . $diffgmt . ' minutes');
            $dataCal[] = $entityCal[$i]->getDatetimeConsultation()->format('Y-m-d H:i');
        }

        $nowPat = new \DateTime("now", $dateTimeZonePhy);
        $nowPat->setTime(7, 0, 0); // 7 hora inicio de atencion
        $nowPat->modify((($diffgmt > 0) ? '+' : '-') . abs($diffgmt) . ' minutes');


        //CALCULA LAS HORA DISPONIBLES SEGUN EL TIMEZONE ENTRE PACIENTE Y MEDICO         
        /* $options = array();
          $minIni = (int) $nowPat->format('H');
          $numOp = 22 - $minIni; // 22 hora finalizacion de atencion
          if (($numOp) > 0) {
          for ($i = $minIni; $i <= 22; $i++) {
          $min = $nowPat->format('i');
          $options[] = (strlen($i) == 1 ? '0' . $i : $i) . ':' . $min;
          }
          } */

        $dayHoursAvailables = $this->getDayHoursAvailable($entity->getDatetimeAvailable());

        //CALCULA LA PROXIMA FECHA DISPONIBLE SEGUN LA FECHA DEL MEDICO Y L DEL PACIENTE
        $now = new \DateTime("now", $dateTimeZonePhy);
        $now->setTime(0, 0, 0);
        $now->modify('+2 day');
        $now->modify(((($dateTimeZonePat->getOffset($dateTimePat) / 60) > 0) ? '+' : '-') . ($dateTimeZonePat->getOffset($dateTimePat) / 60) . ' minutes');

        $data = (object) array("name" => $entity->getUser()->getName(), "lastname" => $entity->getUser()->getLastName(), "jobtitle" => $entity->getJobtitle(), "abms" => $entity->getAbms(), "specialty_id" => $entity->getSpecialty()->getId(), "specialty" => $entity->getSpecialty()->getName(), "subspecialty" => $entity->getSubspecialty(), "languages" => $entity->getLanguages(), "photo" => $this->container->get('templating.helper.assets')->getUrl("uploads/documents/" . $entity->getPhoto()), "cal" => implode(',', $dataCal), "timezone" => $entity->getTimezone(), "difftz" => ($entityPat !== false ? $this->getDiffTz($entity->getTimezone(), $entityPat->getTimezone()) : ''), 'horas' => $dayHoursAvailables, 'nowPh' => $now->format("Y-m-d"));

        return new JsonResponse($data);
    }

    /**
     * Displays a form to create a new Physicians entity.
     *
     * @Route("/calendar/list", name="physicians_calendar")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PHYSICIANS') or has_role('ROLE_ADMIN')")  
     */
    public function calendarAction() {
        $user = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        //$entity = new Physicians();
        $entity = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));

        $now = new \DateTime("now");
        $entityCal = $em->getRepository('AppBundle:Calendar')->findAllByDatetimeConsultation($entity->getId(), $now->format('Y-m-d') . " 00:00:00");

        $availableDays = json_decode($entity->getDatetimeAvailable(), true);


        $weekdays = array("D" => array(), "L" => array(), "M" => array(), "X" => array(), "J" => array(), "V" => array(), "S" => array());

        for ($i = 0; $i < count($availableDays); $i++):
            $dayElemt = str_split($availableDays[$i]);
            $weekdays[$dayElemt[0]][] = substr($availableDays[$i], 1);

        endfor;


        $weekdays1 = array();
        foreach ($weekdays as $value) :
            $weekdays1[] = $value;
        endforeach;
//dump($weekdays1);
//        die();
        return array(
            'entity' => $entity,
            'physician' => $entity,
            'entitiesCal' => $entityCal,
            'availableDays' => json_encode($weekdays1),
        );
    }

    /**
     * @return JsonResponse
     */
    public function getListAction($sp = 'all') {

        $em = $this->getDoctrine()->getManager();

        if ($sp === 'all') {
            $data = $em->getRepository('AppBundle:Physicians')->findAllByEnabled();
        } else {
            $data = $em->getRepository('AppBundle:Physicians')->findAllBySpecialty($sp);
        }

        if (!$data) {
            return new JsonResponse(array());
        }

        return new JsonResponse($data);
    }

    /**
     * @return JsonResponse
     */
    public function disableDateAction($date) {

        $em = $this->getDoctrine()->getManager();

        $entityCal = new Calendar();
        $dateC = new \DateTime($date);
        $entityCal->setDatetimePatient($dateC);
        $entityCal->setDatetimeConsultation($dateC);
        $entityCal->setStatus(1);
        $user = $this->getUser();
        if ($user === null) {
            throw $this->createNotFoundException('Unable to find user.');
        }
        $entityPhy = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        if ($entityPhy === null) {
            throw $this->createNotFoundException('Unable to find user physician.');
        }

        $entityCal->setPhysician($entityPhy);
        $em->persist($entityCal);
        $em->flush();

        return new JsonResponse($entityCal->getId());
    }

    /**
     * @return JsonResponse
     */
    public function enableDateAction($date) {

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if ($user === null) {
            return new JsonResponse(array(-2));
        }
        $entityPhy = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        if ($entityPhy === null) {
            return new JsonResponse(array(-1));
        }

        $entityCal = $em->getRepository('AppBundle:Calendar')->findOneBy(array('physician' => $entityPhy->getId(), 'datetimeConsultation' => new \DateTime($date)));

        if (!$entityCal) {
            return new JsonResponse(array(0));
        }

        $em->remove($entityCal);
        $em->flush();

        return new JsonResponse(array(1));
    }

    public function getDiffTz($tzph, $tzpt) {

        $dateTimeZonePhy = new \DateTimeZone($tzph);
        $dateTimeZonePat = new \DateTimeZone($tzpt);

        $dateTimePhy = new \DateTime("now", $dateTimeZonePhy);
        $dateTimePat = new \DateTime("now", $dateTimeZonePat);


        $timeOffset = ($dateTimeZonePat->getOffset($dateTimePat) / 3600) - ($dateTimeZonePhy->getOffset($dateTimePhy) / 3600);

        return $timeOffset;
    }

    public function getDayHoursAvailable($datetimeAvailable) {
        $availableDays = json_decode($datetimeAvailable, true);

        $weekdays = array("D" => array(), "L" => array(), "M" => array(), "X" => array(), "J" => array(), "V" => array(), "S" => array());

        for ($i = 0; $i < count($availableDays); $i++):
            $dayElemt = str_split($availableDays[$i]);
            $weekdays[$dayElemt[0]][] = substr($availableDays[$i], 1);

        endfor;

        $weekdays1 = array();
        foreach ($weekdays as $value) :
            $weekdays1[] = $value;
        endforeach;

        return $weekdays1;
    }

    /**
     * Get all values from specific key in a multidimensional array
     *
     * @param $key string
     * @param $arr array
     * @return null|string|array
     */
    function array_value_recursive($key, array $arr) {
        $val = array();
        array_walk_recursive($arr, function($v, $k) use($key, &$val) {
            if ($k == $key)
                array_push($val, $v);
        });
        return count($val) > 1 ? $val : array_pop($val);
    }

}
