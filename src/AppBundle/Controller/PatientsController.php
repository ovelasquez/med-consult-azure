<?php

namespace AppBundle\Controller;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Patients;
use AppBundle\Form\PatientsType;
use AppBundle\Form\PatientsActType;
use AppBundle\Form\PatientsEditType;
use AppBundle\Form\PatientsUpdateType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\FormError;
use \AppBundle\Entity\MedicalForms;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\Finder\Exception\AccessDeniedException;

/**
 * Patients controller.
 *
 * @Route("/patients")
 */
class PatientsController extends Controller {

    /**
     * Lists all Patients entities.
     *
     * @Route("/", name="patients")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $twig = $this->container->get('twig');
        $gl = $twig->getGlobals();

        $entities = $em->getRepository('AppBundle:Patients')->findBy(array(), array('id' => 'DESC'), (int) $gl["for_page"]);

        $qb = $em->getRepository('AppBundle:Patients')->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $count = $qb->getQuery()->getSingleScalarResult();

        return array(
            'entities' => $entities,
            'p' => 1,
            'pCount' => ceil($count / (int) $gl["for_page"])
        );
    }

    /**
     * Creates a new Patients entity.
     *
     * @Route("/", name="patients_create")
     * @Method("POST")
     * @Template("AppBundle:Patients:new.html.twig")
     * 
     */
    public function createAction(Request $request) {
        $entity = new Patients();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $entitySplan = $em->getRepository('AppBundle:StoragePlans')->findOneBy(array("tag" => "free"));
            $entity->setStoragePlan($entitySplan);
            $entity->setStored(0);

            $user = $entity->getUser();
            $user->addRole('ROLE_PATIENT');
            $entity->setUser($user);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('patients_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Patients entity.
     *
     * @param Patients $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Patients $entity) {
        $form = $this->createForm(new PatientsType(), $entity, array(
            'action' => $this->generateUrl('patients_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }

    /**
     * Lists all Patients entities.
     *
     * @Route("/pag/{p}", name="patients_pag")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function indexPagAction($p) {
        $em = $this->getDoctrine()->getManager();

        $twig = $this->container->get('twig');
        $gl = $twig->getGlobals();

        $entities = $em->getRepository('AppBundle:Patients')->findBy(array(), array('id' => 'asc'), $gl["for_page"], ((int) $p - 1) * $gl["for_page"]);

        $qb = $em->getRepository('AppBundle:Patients')->createQueryBuilder('a');
        $qb->select('COUNT(a)');
        $count = $qb->getQuery()->getSingleScalarResult();

        return array(
            'entities' => $entities,
            'p' => $p,
            'pCount' => ceil($count / (int) $gl["for_page"])
        );
    }

    /**
     * Displays a form to create a new Patients entity.
     *
     * @Route("/new", name="patients_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function newAction() {
        $entity = new Patients();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a Patients entity.
     *
     * @Route("/{id}", name="patients_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')")      
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Patients')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patients entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Patients entity.
     *
     * @Route("/{id}/edit", name="patients_edit")
     * @Method("GET")
     * @Template()
     *  
     */
//    public function editAction($id) {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('AppBundle:Patients')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find Patients entity.');
//        }
//
//        $editForm = $this->createEditForm($entity);
//        $deleteForm = $this->createDeleteForm($id);
//
//        return array(
//            'entity' => $entity,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        );
//    }

    /**
     * Creates a form to edit a Patients entity.
     *
     * @param Patients $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
//    private function createEditForm(Patients $entity) {
//        $form = $this->createForm(new PatientsType(), $entity, array(
//            'action' => $this->generateUrl('patients_update', array('id' => $entity->getId())),
//            'method' => 'PUT',
//        ));
//
//        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submit btnAdm rojoFuerte')));
//
//        return $form;
//    }

    /**
     * Edits an existing Patients entity.
     *
     * @Route("/{id}", name="patients_update")
     * @Method("PUT")
     * @Template("AppBundle:Patients:edit.html.twig")
     * 
     */
//    public function updateAction(Request $request, $id) {
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('AppBundle:Patients')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find Patients entity.');
//        }
//
//        $deleteForm = $this->createDeleteForm($id);
//        $editForm = $this->createEditForm($entity);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isValid()) {
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('patients_edit', array('id' => $id)));
//        }
//
//        return array(
//            'entity' => $entity,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        );
//    }

    /**
     * Deletes a Patients entity.
     *
     * @Route("/{id}", name="patients_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')")       
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Patients')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Patients entity.');
            }
            $userManager = $this->get('fos_user.user_manager');
            $entity->getUser()->setEnabled(!$entity->getUser()->isEnabled());
            $userManager->updateUser($entity->getUser());
//            $em->remove($entity);
//            $em->flush();
        }

        return $this->redirect($this->generateUrl('patients'));
    }

    /**
     * Creates a form to delete a Patients entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('patients_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Desactivar', 'attr' => array('class' => 'submit btnAdm lila')))
                        ->getForm()
        ;
    }

    /**
     * Creates a new Patients entity.
     *
     * @Route("/registro", name="patients_create_front")
     * @Method("POST")
     * @Template("AppBundle:Patients:new_front.html.twig")
     */
    public function createfrontAction(Request $request) {
        $entity = new Patients();
        $form = $this->createCreateFrontForm($entity);
        $form->handleRequest($request);

        $userExist = false;
        $regCompleted = false;
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        if (isset($request->request->get("appbundle_patients")["user"]["email"]) && null !== $user = $userManager->findUserByEmail($request->request->get("appbundle_patients")["user"]["email"])):
            $userExist = true;
            $form->get('user')->get('email')->addError(new FormError('El correo está en uso'));
        endif;
        if (isset($request->request->get("appbundle_patients")["user"]["username"]) && null !== $user = $userManager->findUserByUsername($request->request->get("appbundle_patients")["user"]["username"])):
            $userExist = true;
            $form->get('user')->get('username')->addError(new FormError('El usuario está en uso'));
        endif;

        $errors = $this->get('app.errorsform')->getErrors($form);

        if ($form->isValid() && !$userExist) {
            $em = $this->getDoctrine()->getManager();
            $entitySplan = $em->getRepository('AppBundle:StoragePlans')->findOneBy(array("tag" => "free"));
            $entity->setStoragePlan($entitySplan);
            $entity->setStored(0);
            $user = $entity->getUser();
//            $user->setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
//            $entity->setUser($user);
            //dump($entity);die();

            $em->persist($entity);
            $em->flush();

            /*             * ** USER INI************************* */
            $user->addRole('ROLE_PATIENT');

//            $user->setEnabled(true);

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
            $user->setEnabled(true);
            $userManager->updateUser($user);

            $regCompleted = true;

            $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

            /*             * ** USER FIN************************* */

            $message = \Swift_Message::newInstance()
                    ->setSubject('¡FELICIDADES! TU REGISTRO EN MEDECONSULT SE HA COMPLETADO')
                    ->setFrom('noreply@medeconsult.com')
                    ->setTo($entity->getUser()->getEmail())
                    ->setBody(
                    $this->renderView(
                            'AppBundle:Patients:email.welcome.html.twig', array('email' => $entity->getUser()->getEmail(), 'username' => $entity->getUser()->getName())
                    ), 'text/html'
            );
            $this->get('mailer')->send($message);


            return $this->redirect($this->generateUrl('patient_view_front'));

            return $this->render('patients/new_front_res.html.twig', array(
                        'name' => $user->getName(),
                        'tag' => 'registro'
            ));
        }

//        $errors = array();
//        foreach ($form->all() as $child) {
//            if (!$child->isValid()) {
//                $errors[] = $child->getErrors()->getChildren()->getMessage();
//                 
//            }
//        }
        $errors_txt = array();
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($errors));
        foreach ($it as $v) {
            $errors_txt[] = $v;
        }
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tag' => "registro",
            'messages' => $errors_txt,
        );
    }

    /**
     * Creates a form to create a Patients entity.
     *
     * @param Patients $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateFrontForm(Patients $entity) {
        $form = $this->createForm(new PatientsType(), $entity, array(
            'action' => $this->generateUrl('patients_create_front'),
            'method' => 'POST',
        ));

        //$form->add('submit', 'submit', array('label' => 'Guardar','attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }

    /**
     * Displays a form to create a new Patients entity.
     *
     * @Route("/registro/pacientes", name="patients_new_front")
     * @Method("GET")
     * @Template("AppBundle:Patients:new_front.html.twig")
     */
    public function newfrontAction() {
        $user = $this->getUser();
        if (null !== $user && $user->hasRole("ROLE_PATIENT")) :
            return $this->redirect($this->generateUrl('patient_view_front'));
        endif;


        $entity = new Patients();
        $form = $this->createCreateFrontForm($entity);

        //$form->get('user')->remove('lastname');

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tag' => "registro",
        );
    }

    /**
     * Finds and displays a Patients entity.
     *
     * @Route("/paciente", name="patients_show_front")
     * @Method("GET")
     * @Template("AppBundle:Patients:show_front.html.twig")      
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")    
     */
    public function showfrontAction() {
        $user = $this->getUser();

        $entitiesAll = array();

        if ($user->getFacebookId() == $user->getEmail()) {
            return $this->redirect($this->generateUrl('patients_edit_front_fb', array("id" => $user->getId())));
        }

        $em = $this->getDoctrine()->getManager();


        $entity = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));
        $entities = $em->getRepository('AppBundle:Consultations')->findAllWithCalendar($entity->getId(), 10);

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

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patients entity.');
        }

        return array(
            'entity' => $entity,
            'entities' => $entitiesAll,
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
     * Creates a new Patients entity.
     *
     * @Route("/actualizar", name="patients_update_front")
     * @Method("POST")
     * @Template("AppBundle:Patients:update_front.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")    
     */
    public function updatefrontAction(Request $request) {
        $entity = new Patients();
        $form = $this->updatefrontCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {

            $userManager = $this->get('fos_user.user_manager');
            $user = $this->getUser();

            if ($user === null || $user->getId() != $request->request->get("appbundle_patients_act")["user"]) {
                throw $this->createNotFoundException('Unable to find user.');
            }

            $user->setEmail($entity->getEmailact());
            $user->setName($entity->getNameact());
            $user->setLastName($entity->getLastnameact());

            $userManager->updateUser($user);

            $em = $this->getDoctrine()->getManager();

            if ($entity->getStoragePlan() === null):
                $entitySplan = $em->getRepository('AppBundle:StoragePlans')->findOneBy(array("tag" => "free"));
                $entity->setStoragePlan($entitySplan);
                $entity->setStored(0);
            endif;

            $entity->setUser($user);

            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('patient_view_front'));
        }

        $errors = array();
        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[] = $child->getErrors()->getChildren()->getMessage();
            }
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tag' => "registro",
            'messages' => $errors,
        );
    }

    /**
     * Creates a form to actualizar a Patients entity.
     *
     * @param Patients $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function updatefrontCreateForm(Patients $entity) {
        $form = $this->createForm(new PatientsActType(), $entity, array(
            'action' => $this->generateUrl('patients_update_front'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to actualizar a new Patients entity via facebook.
     *
     * @Route("/actualizar/{id}", name="patients_edit_front")
     * @Method("GET")
     * @Template("AppBundle:Patients:update_front.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")    
     */
    public function editfrontAction($id) {
        $entity = new Patients();
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserBy(array("id" => $id));

        $em = $this->getDoctrine()->getManager();

        $entityPat = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));

        if ($entityPat !== null) {
            $user = $entityPat->getUser();
            return $this->redirect($this->generateUrl('patient_view_front'));
        }

        if ($user === null) {
            throw $this->createNotFoundException('Unable to find user.');
        }

        $entity->setUser($user);

        $form = $this->updatefrontCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'tag' => "registro"
        );
    }

    /**
     * Creates a new Patients entity.
     *
     * @Route("/editar/{id}", name="patients_update_profile")
     * @Method("PUT")
     * @Template("AppBundle:Patients:update_profile.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")
     */
    public function updateprofileAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();

        //dump($request->getSession()->get('userInfo'));

        $entity = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));
        $username = $entity->getUser()->getUsername();

//        $params = $request->request->all();
//        $params['appbundle_patients_edit']['user']['username'] = $username;
//        $request->request->add($params);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patients entity.');
        }

        if (null !== $entity->getUser()->getFacebookId()):
            $editForm = $this->updateprofilepCreateForm($entity);
        else:
            $editForm = $this->updateprofileCreateForm($entity);
        endif;


//        $editForm->get('user')->remove('username');
//
//        if (null !== $entity->getUser()->getFacebookId()):
//            $editForm->get('user')->remove('plainPassword');
//        endif;

        $editForm->handleRequest($request);
        $errors = $this->get('app.errorsform')->getErrors($editForm);
//
//        dump($editForm);
//        die();
        if ($editForm->isValid()) {

            $em->flush();
            $user = $this->getUser();
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);

//            dump($request->getSession()->remove('userInfo'));
//            dump($request->getSession()->get('userInfo'));


            $entity = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));
            $request->getSession()->replace(array('userInfo' => $entity));
            //dump($request->getSession()->get('userInfo'));
            //die();

            return $this->redirect($this->generateUrl('patient_edit_profile'));
        }



        return array(
            'entity' => $entity, 'form' => $editForm->createView(), 'tag' => "registro", 'errors' => $errors
        );
    }

    /**
     * Creates a form to actualizar a Patients entity sin username.
     *
     * @param Patients $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function updateprofileCreateForm(Patients $entity) {

        $form = $this->createForm(new PatientsEditType(), $entity, array(
            'action' => $this->generateUrl('patients_update_profile', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Creates a form to actualizar a Patients entity sin username .
     *
     * @param Patients $entity The entity
     * @return \Symfony\Component\Form\Form The form
     */
    private function updateprofilepCreateForm(Patients $entity) {

        $form = $this->createForm(new PatientsUpdateType(), $entity, array(
            'action' => $this->generateUrl('patients_update_profile', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        return $form;
    }

    /**
     * Displays a form to actualizar a new Patients entity via facebook.
     *
     * @Route("/editar", name="patients_edit_profile")
     * @Method("GET")
     * @Template("AppBundle:Patients:update_profile.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")
     */
    public function editprofileAction() {

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if ($user->getFacebookId() == $user->getEmail()) {
            return $this->redirect($this->generateUrl('patients_edit_front_fb', array("id" => $user->getId())));
        }

        $entity = $em->getRepository('AppBundle:Patients')->findOneBy(array("user" => $user));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patients entity.');
        }

        if (null !== $entity->getUser()->getFacebookId()):
            $editForm = $this->updateprofilepCreateForm($entity);
        else:
            $editForm = $this->updateprofileCreateForm($entity);
        endif;

//        if (null !== $entity->getUser()->getFacebookId()):
//            $editForm->get('user')->remove('plainPassword');
//        endif;
//        //$editForm->get('user')->remove('password');

        if (null !== $entity->getBirthdate()):
            $entity->setYearsold($this->CalculaEdad($entity->getBirthdate()->format('Y-m-d')));
        endif;

        return array(
            'entity' => $entity,
            'form' => $editForm->createView(),
            'tag' => ""
        );
    }

    /**
     * Displays a form to actualizar a new Patients entity via facebook.
     *
     * @Route("/ver/{form}/{id}", name="patients_view_form")
     * @Method("GET")
     * @Template("AppBundle:Patients:view_form.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")    
     */
    public function viewformAction($form, $id) {

        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();
        $entity = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patients entity.');
        }

        if (null !== $entity->getBirthdate()):
            $entity->setYearsold($this->CalculaEdad($entity->getBirthdate()->format('Y-m-d')));
        endif;

        $entityForm = $em->getRepository('AppBundle:MedicalForms')->findOneBy(array('formName' => $form));

        if (!$entityForm) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $entities = $this->getFieldsForms($entityForm);

        return array(
            'entity' => $entity,
            'tag' => "",
            'entities' => $entities,
            'dir' => md5($id),
            'entityform' => $entityForm,
            'page' => $id
        );
    }

    /**
     * Displays  shared form
     *
     * @Route("/view/shared/{form}/{id}", name="patients_view_shared_form")
     * @Method("GET")
     * @Template("AppBundle:Patients:view_shared_form.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') or has_role('ROLE_GUESS') or has_role('ROLE_PHYSICIANS')")    
     */
    public function viewsharedformAction($form, $id) {

        //$this->denyAccessUnlessGranted('ROLE_PHYSICIANS', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();


        $shared = null;
        $token = null;

        $entity = $em->getRepository('AppBundle:Patients')->find($id);
        if (!$entity) {
            $shared = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $id));
            if (!$shared) {
                throw $this->createNotFoundException('Unable to find Patients Token entity.');
            }
            $token = $id;
            $entity = $shared->getPatient();
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
            $entityPhy = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
            $entityCons = $em->getRepository('AppBundle:Consultations')->findOneBy(
                    array('physician' => $entityPhy, 'patient' => $entity)
            );

            if ($entityCons === null):
                return $this->redirect($this->generateUrl('physicians_show_front', array('id' => $entityPhy->getId())));
            endif;
        endif;

        /* Vista solo para usuarios relacionados por compartir */
        if ($this->get('security.context')->isGranted('ROLE_GUESS')) :
            $user = $this->get('security.context')->getToken()->getUser();
            $entitySh = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(
                    array('patient' => $entity, 'email' => $user->getEmail())
            );

            if ($entitySh === null):
                return $this->redirect($this->generateUrl('patientssharemedicalhistory_guess'));
            endif;
        endif;


        if (null !== $entity->getBirthdate()):
            $entity->setYearsold($this->CalculaEdad($entity->getBirthdate()->format('Y-m-d')));
        endif;

        $entityForm = $em->getRepository('AppBundle:MedicalForms')->findOneBy(array('formName' => $form));

        if (!$entityForm) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $entities = $this->getFieldsForms($entityForm, $entity->getUser());

        return array(
            'entity' => $entity,
            'tag' => "",
            'entities' => $entities,
            'dir' => md5($id),
            'entityform' => $entityForm,
            'token' => $token,
            'page' => $id,
            'patient' => $entity,
        );
    }

    /**
     * Displays a form list.
     *
     * @Route("/list/forms/{id}", name="patients_list_forms")
     * @Method("GET")
     * @Template("AppBundle:Patients:list_forms.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')")  
     */
    public function listformsAction($id) {

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Patients')->findOneBy(array("user" => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Patients entity.');
        }

        if (null !== $entity->getBirthdate()):
            $entity->setYearsold($this->CalculaEdad($entity->getBirthdate()->format('Y-m-d')));
        endif;

        $conn = $em->getConnection();
        $user = $this->get('security.context')->getToken()->getUser();

        $forms = $conn->fetchAll('SELECT medical_forms_id FROM patients_medical_forms WHERE fos_user_id = ?', array($user->getId()));

        $ids = '';
        foreach ($forms as $f) {
            $ids.=",?";
        }

        if ($ids != ''):
            $ids = substr($ids, 1);
        endif;

        $entities = array();


        if ($forms !== false):
            $rsm = new ResultSetMappingBuilder($em);
            $rsm->addRootEntityFromClassMetadata('AppBundle\Entity\MedicalForms', 'f');
            $query = $em->createNativeQuery("SELECT * FROM medical_forms WHERE medical_forms.id in (" . $ids . ") and medical_forms.form_name<>? and medical_forms.form_name<>?", $rsm);
            for ($i = 0; $i < count($forms); $i++) {
                $query->setParameter($i, $forms[$i]['medical_forms_id']);
            }

            $twig = $this->container->get('twig');
            $globals = $twig->getGlobals();

            $query->setParameter(count($forms), $globals['form_hs']);
            $query->setParameter(count($forms) + 1, $globals['form_hm']);

            $entities = $query->getResult();

        endif;


        return array(
            'entity' => $entity,
            'tag' => "",
            'entities' => $entities,
            'dir' => md5($id)
        );
    }

    /**
     * Displays a form medicalHistory
     *
     * @Route("/medical/history/shared/{token}", name="patients_medical_history_shared")
     * @Method("GET")
     * @Template("AppBundle:Patients:medical_history_shared.html.twig")
     *  
     */
    public function medicalHistorySharedAction($token) {

        $isFullyAuthenticated = $this->get('security.context')
                ->isGranted('IS_AUTHENTICATED_FULLY');

        if (!$isFullyAuthenticated) {
            //throw new AccessDeniedException();
            return $this->redirect($this->generateUrl('patientssharemedicalhistory_log_guess', array('token' => $token)));
        }

        $em = $this->getDoctrine()->getManager();

        $shared = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $token));
        $now = new \DateTime("now");

        if (!$shared) {
            throw $this->createNotFoundException('Unable to find Patients Shared entity.');
        }


        if (($shared->getAvailable() > 0 && $now->diff($shared->getDateTime())->days > $shared->getAvailable())) {
            throw $this->createNotFoundException('Token vencido');
        }

        $twig = $this->container->get('twig');
        $globals = $twig->getGlobals();

        return $this->redirect($this->generateUrl('medicalforms_view_shared', array('id' => $globals['form_hs'], 'pat' => $token)));
    }

    public function CalculaEdad($fecha) {
        list($Y, $m, $d) = explode("-", $fecha);
        return( date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y );
    }

    public function getFieldsForms($entity, $user = null) {

        $em = $this->getDoctrine()->getManager();
        //$entity = $em->getRepository('AppBundle:MedicalForms')->findOneBy($par);

        if ($user === null):
            if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
                $user = $this->get('security.context')->getToken()->getUser();
            } else {
                return array();
            }
        endif;


        $entities = $em->getRepository('AppBundle:MedicalFormsFieldsets')->findBy(array("medicalForm" => $entity->getId()), array("position" => "ASC"));
        $entitiesAll = array();
        $entitiesbyPage = array();
        $entityset = (object) array("fieldset" => '', "fields" => '');
        $classColor = array("azulOscuro blancoColor", "celeste", "rojo", "gris", "lila", "celeste", "rojoFuerte", "azulNormal");
        $itc = 0;
        foreach ($entities as $entityFs) :
            $classC = ($entityFs->getType() == "page") ? $classColor[$itc] : "";
            $entityset = (object) array("fieldset" => '', "fields" => '', "classColor" => $classC);
            $itc = ($entityFs->getType() == "page") ? (($itc === count($classColor) - 1) ? 0 : $itc + 1) : $itc;
            $entityset->fieldset = $entityFs;
            $rsm = new ResultSetMappingBuilder($em);
            $rsm->addRootEntityFromClassMetadata('AppBundle\Entity\MedicalFormsFields', 'f');
            if ($user !== NULL):
                $query = $em->createNativeQuery(""
                        //. "SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og, FV.value_data as value_temp, FV.key_enc as key_enc FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid ) F2 ON F2.subgroup=F3.subgroup LEFT JOIN _mffd_" . $entity->getFormName() . " FV ON FV.medical_forms_field_name=F3.name AND FV.fos_user_id=:idu  WHERE F3.medical_forms_fieldset_id=:id ORDER BY oi ASC,  og  ASC, orderid ASC "
                        . "CALL GetFieldsByFieldsetUser(:id,:idu,'" . $entity->getFormName() . "')"
                        . "", $rsm);
                $query->setParameter('idu', $user->getId());
            else:
                $query = $em->createNativeQuery(""
                        //. "SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid) F2 ON F2.subgroup=F3.subgroup WHERE F3.medical_forms_fieldset_id=:id ORDER BY oi ASC,  og  ASC, orderid ASC "
                        . "CALL GetFieldsByFieldset(:id)"
                        . "", $rsm);
            endif;
            $query->setParameter('id', $entityFs->getId());
            $entitiesFl = $query->getResult();
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


        endforeach;

        return $entitiesbyPage;
    }

}
