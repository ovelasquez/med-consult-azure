<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\MedicalFormsFieldsets;
use AppBundle\Form\MedicalFormsFieldsetsType;
use AppBundle\Form\MedicalFormsFieldsetsUpdateType;
use AppBundle\Form\MedicalFormsFieldsetsMedType;
use AppBundle\Form\MedicalFormsFieldsetsMedAjaxType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * MedicalFormsFieldsets controller.
 *
 * @Route("/medicalformsfieldsets")
 */
class MedicalFormsFieldsetsController extends Controller {

    /**
     * Lists all MedicalFormsFieldsets entities.
     *
     * @Route("/", name="medicalformsfieldsets")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:MedicalFormsFieldsets')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all MedicalFormsFieldsets entities of type page.
     * 
     */
    public function pagesAction($medicalFormName) {
        $em = $this->getDoctrine()->getManager();
        $medicalForm = $em->getRepository('AppBundle:MedicalForms')->findBy(array('formName' => $medicalFormName));
        $entities = $em->getRepository('AppBundle:MedicalFormsFieldsets')->findBy(array("medicalForm" => $medicalForm,"type"=>"page"), array("position" => "ASC"));

        return $this->render(
                        'medicalformsfieldsets/pages_list.html.twig', array('entities' => $entities, 'formname' => $medicalFormName)
        );
    }

    /**
     * Creates a new MedicalFormsFieldsets entity.
     *
     * @Route("/", name="medicalformsfieldsets_create")
     * @Method("POST")
     * @Template("AppBundle:MedicalFormsFieldsets:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function createAction(Request $request) {
        $entity = new MedicalFormsFieldsets();
        $data = $request->request->get("appbundle_medicalformsfieldsets");

        $form = $this->createCreateForm($entity, $data["medicalForm"]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $sql = "UPDATE medical_forms_fieldsets SET position = ((SELECT ord FROM (SELECT MAX(position) AS ord FROM medical_forms_fieldsets) AS ord_s) + 1)" . (($entity->getType() === 'page') ? ', page=:id' : '') . "  WHERE id = :id";

            $connection = $em->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValue('id', $entity->getId());
            $statement->execute();

            return $this->redirect($this->generateUrl('medicalformsfieldsets_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'idf' => $entity->getMedicalForm()->getId(),
        );
    }

    /**
     * Creates a form to create a MedicalFormsFieldsets entity.
     *
     * @param MedicalFormsFieldsets $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicalFormsFieldsets $entity, $idf = null) {
        $form = $this->createForm(new MedicalFormsFieldsetsType(), $entity, array(
            'action' => $this->generateUrl('medicalformsfieldsets_create'),
            'method' => 'POST',
            'idf' => $idf,
        ));

        if ($idf !== null):
            $em = $this->getDoctrine()->getManager();
            $entityF = $em->getRepository('AppBundle:MedicalForms')->find($idf);
            if ($entityF):
                $form->get('medicalForm')->setData($entityF);
            else:
                throw $this->createNotFoundException('Unable to find MedicalForms entity.');
            endif;
        else:
            throw $this->createNotFoundException('Unable to find MedicalForms entity, id null.');
        endif;

        $form->add('submit', 'submit', array('label' => 'Crear'));

        return $form;
    }

    /**
     * Displays a form to create a new MedicalFormsFieldsets entity.
     *
     * @Route("/new/{idf}", name="medicalformsfieldsets_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function newAction($idf) {
        $entity = new MedicalFormsFieldsets();
        $form = $this->createCreateForm($entity, $idf);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'idf' => $idf,
        );
    }

       
    /**MED**************/
    
    /**
     * Creates a new MedicalFormsFieldsets entity.
     *
     * @Route("/med", name="medicalformsfieldsets_create_med")
     * @Method("POST")
     * @Template("AppBundle:MedicalFormsFieldsets:newMed.html.twig")
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function createMedAction(Request $request) {
        $entity = new MedicalFormsFieldsets();
        $data = $request->request->get("appbundle_medicalformsfieldsets");

        $form = $this->createCreateMedForm($entity, $data["medicalForm"],$data["page"]);
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        $mfView = $em->getRepository('AppBundle:MedicalFormsViews')->findOneBy(array('physician' => $physician));
          
        if ($form->isValid()) {
            
            $entity->setPhysician($physician);   
            
            if (null !== $entity->getPosition()):
                $pos = $entity->getPosition()->getPosition()+1;
            else:
                $pos=1;
            endif;
            
            $entity->setPosition($pos);   
            $em->persist($entity);
            $em->flush();

            //$sql = "UPDATE medical_forms_fieldsets SET position = ((SELECT ord FROM (SELECT MAX(position) AS ord FROM medical_forms_fieldsets) AS ord_s) + 1)" . (($entity->getType() === 'page') ? ', page=:id' : '') . "  WHERE id = :id";
            
            $sql = "UPDATE medical_forms_fieldsets SET position = (position + 1)  WHERE id <> :id AND type = 'fieldset' AND page = :idp AND  	medical_form_id = :idf AND position >= ".($pos);

            $connection = $em->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValue('id', $entity->getId());
            $statement->bindValue('idp', $entity->getPage()->getId());
            $statement->bindValue('idf', $entity->getMedicalForm()->getId());
            $statement->execute();

            
            
            return $this->redirect($this->generateUrl('medical_forms_views_edit_med', array('id' => $mfView->getId())));
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
            'idf' => $entity->getMedicalForm()->getId(),
            'idfv' => $mfView->getId(),
            'idfp' => $entity->getPage(),
            'messages' => $errors,
        );
    }

    /**
     * Creates a form to create a MedicalFormsFieldsets entity.
     *
     * @param MedicalFormsFieldsets $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateMedForm(MedicalFormsFieldsets $entity, $idf = null,$idfp = null) {
        $form = $this->createForm(new MedicalFormsFieldsetsMedType(), $entity, array(
            'action' => $this->generateUrl('medicalformsfieldsets_create_med'),
            'method' => 'POST',
            'idfp' => $idfp,
            'idf' => $idf,
        ));
        $em = $this->getDoctrine()->getManager();

        if ($idf !== null):
            
            $entityF = $em->getRepository('AppBundle:MedicalForms')->find($idf);
            if ($entityF):
                $form->get('medicalForm')->setData($entityF);
            else:
                throw $this->createNotFoundException('Unable to find MedicalForms entity.');
            endif;
        else:
            throw $this->createNotFoundException('Unable to find MedicalForms entity, id null.');
        endif;
        
        
        $entityFd = $em->getRepository('AppBundle:MedicalFormsFieldsets')->find($idfp);
        if ($entityFd):
            $form->get('page')->setData($entityFd);
        else:
            throw $this->createNotFoundException('Unable to find MedicalFormsFieldsets entity.');
        endif;

        
        return $form;
    }

    /**
     * Displays a form to create a new MedicalFormsFieldsets entity.
     *
     * @Route("/new/med/{idf}/{idfv}/{idfp}", name="medicalformsfieldsets_new_med")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function newMedAction($idf,$idfv,$idfp) {
        $entity = new MedicalFormsFieldsets();
        $form = $this->createCreateMedForm($entity,$idf,$idfp);

        
        
        return array(
            'entity' => $entity,
            'form' => $form->createView(),
            'idf' => $idf,
            'idfv' => $idfv,
            'idfp' => $idfp,
        );
    }
    
    
    /**
     * Creates a new MedicalFormsFieldsets entity.
     *
     * @Route("/med/ajax", name="medicalformsfieldsets_create_med_ajax")
     * @Method("POST")
     * @Template("AppBundle:MedicalFormsFieldsets:newMed.html.twig")
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function createMedAjaxAction(Request $request) {
        $entity = new MedicalFormsFieldsets();
        $data = $request->request->get("appbundle_medicalformsfieldsets");

        $form = $this->createCreateFMedAjaxForm($entity,$data["medicalForm"],$data["page"]);
        //$form = $this->createCreateMedForm($entity, $data["medicalForm"],$data["page"]);
        $form->handleRequest($request);
        
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        $mfView = $em->getRepository('AppBundle:MedicalFormsViews')->findOneBy(array('physician' => $physician));
          
        if ($form->isValid()) {
            
            $entity->setPhysician($physician);   
            
//            dump($entity->getPosition()); die();
            
            $entityFd = $em->getRepository('AppBundle:MedicalFormsFieldsets')->find($entity->getPosition());
            $idPos = "fs_".$entity->getPosition();
            $idPosEnt = $entity->getPosition();
            
            if (null !== $entityFd->getPosition()):
                $pos = $entityFd->getPosition()+1;
            else:
                $pos=1;
            endif;
            
            $entity->setPage($entityFd->getPage());   
            $entity->setPosition($pos);   
            $em->persist($entity);
            $em->flush();

            //$sql = "UPDATE medical_forms_fieldsets SET position = ((SELECT ord FROM (SELECT MAX(position) AS ord FROM medical_forms_fieldsets) AS ord_s) + 1)" . (($entity->getType() === 'page') ? ', page=:id' : '') . "  WHERE id = :id";
            
            $sql = "UPDATE medical_forms_fieldsets SET position = (position + 1)  WHERE id <> :id AND type = 'fieldset' AND page = :idp AND  	medical_form_id = :idf AND position >= ".($pos);

            $connection = $em->getConnection();
            $statement = $connection->prepare($sql);
            $statement->bindValue('id', $entity->getId());
            $statement->bindValue('idp', $entity->getPage()->getId());
            $statement->bindValue('idf', $entity->getMedicalForm()->getId());
            $statement->execute();
            
            
            return new JsonResponse(array('message' => 'success','name' => $entity->getLabel(),'fieldaft' => $idPos,'fieldaftid' => $idPosEnt,'id' => $entity->getId(),'order' => $pos), 200);
            //return $this->redirect($this->generateUrl('medical_forms_views_edit_med', array('id' => $mfView->getId())));
        }
        
//        $errors = array();
//        foreach ($form->all() as $child) {
//            if (!$child->isValid()) {
//                $errors[] = $child->getErrors()->getChildren()->getMessage();
//            }
//        }
      
        $errors_it = iterator_to_array($form->getErrors());
        $errors = array();
        foreach ($errors_it as $child) {
            if ($child->getMessage()) {
                $errors[] = $child->getMessage();
            }
        }
        
//        return array(
//            'entity' => $entity,
//            'form' => $form->createView(),
//            'idf' => $entity->getMedicalForm()->getId(),
//            'idfv' => $mfView->getId(),
//            'idfp' => $entity->getPage(),
//            'messages' => $errors,
//        );
        
        $response = new JsonResponse(
            array(        
                'form' => $this->renderView('medicalformsfieldsets/createAjax.html.twig',
                array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'idf' => $entity->getMedicalForm()->getId(),
                    'idfv' => $mfView->getId(),
                    'idfp' => $entity->getPage(),
        ))), 400);
 
        return $response;
    }
    
    
    /**
     * Creates a form to create a MedicalFormsFields entity.
     *
     * @param MedicalFormsFields $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateFMedAjaxForm(MedicalFormsFieldsets $entity, $idf, $idfs) {
        $form = $this->createForm(new MedicalFormsFieldsetsMedAjaxType(), $entity, array(
            'action' => $this->generateUrl('medicalformsfieldsets_create_med_ajax'),
            'method' => 'POST',
            'idfp' => $idfs,
            'idf' => $idf,
        ));
        $em = $this->getDoctrine()->getManager();

        if ($idf !== null):
            
            $entityF = $em->getRepository('AppBundle:MedicalForms')->find($idf);
            if ($entityF):
                $form->get('medicalForm')->setData($entityF);
            else:
                throw $this->createNotFoundException('Unable to find MedicalForms entity.');
            endif;
        else:
            throw $this->createNotFoundException('Unable to find MedicalForms entity, id null.');
        endif;
        
        
        $entityFd = $em->getRepository('AppBundle:MedicalFormsFieldsets')->find($idfs);
        if ($entityFd):
            $form->get('page')->setData($entityFd);
        else:
            throw $this->createNotFoundException('Unable to find MedicalFormsFieldsets entity.');
        endif;

        
        return $form;
    }
    
    
    /**
     * Displays a form to create a new MedicalFormsFields entity.  
     * @Route("/create/ajax/{idf}/{idfs}/{idfv}/med", name="medicalformsfieldsets_create_ajax_f_med")       
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function createAjaxAction($idf,$idfs,$idfv) {
        $entity = new MedicalFormsFieldsets();
        $form = $this->createCreateFMedAjaxForm($entity,$idf,$idfs);

        $response = new JsonResponse(
            array(        
                'form' => $this->renderView('medicalformsfieldsets/createAjax.html.twig',
                array(
                    'entity' => $entity,
                    'form' => $form->createView(),
                    'idf' => $idf,
                    'idfv' => $idfv,                   
        ))), 200);
 
        return $response;
        

    }
    
    /**
     * @return JsonResponse
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function deleteMedAction($id) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:MedicalFormsFieldsets')->find($id);

        if (!$entity) {
            return new JsonResponse(false);
        }
 
        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
          
        if ($entity->getPhysician() === $physician ) {    
            
//            $ids = $entity->getMedicalFormsFieldset()->getId();            
            $em->remove($entity);
            $em->flush();
            return new JsonResponse(true);
        }
        
        return new JsonResponse(false);
    }
    
    /**MED**************/
    
    
    /**
     * Finds and displays a MedicalFormsFieldsets entity.
     *
     * @Route("/{id}", name="medicalformsfieldsets_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalFormsFieldsets')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalFormsFieldsets entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'idf' => $entity->getMedicalForm()->getId(),
        );
    }

    /**
     * Displays a form to edit an existing MedicalFormsFieldsets entity.
     *
     * @Route("/{id}/edit", name="medicalformsfieldsets_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalFormsFieldsets')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalFormsFieldsets entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'idf' => $entity->getMedicalForm()->getId(),
        );
    }

    /**
     * Creates a form to edit a MedicalFormsFieldsets entity.
     *
     * @param MedicalFormsFieldsets $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MedicalFormsFieldsets $entity) {
        $form = $this->createForm(new MedicalFormsFieldsetsUpdateType(), $entity, array(
            'action' => $this->generateUrl('medicalformsfieldsets_update', array('id' => $entity->getId())),
            'method' => 'PUT',
            'idf' => $entity->getMedicalForm()->getId(),
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing MedicalFormsFieldsets entity.
     *
     * @Route("/{id}", name="medicalformsfieldsets_update")
     * @Method("PUT")
     * @Template("AppBundle:MedicalFormsFieldsets:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalFormsFieldsets')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalFormsFieldsets entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('medicalformsfieldsets_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a MedicalFormsFieldsets entity.
     *
     * @Route("/{id}", name="medicalformsfieldsets_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:MedicalFormsFieldsets')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MedicalFormsFieldsets entity.');
            }

            $idf = $entity->getMedicalForm()->getId();

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('medicalforms_show', array('id' => $idf)));
        //return $this->redirect($this->generateUrl('medicalformsfieldsets'));
    }

    /**
     * Creates a form to delete a MedicalFormsFieldsets entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('medicalformsfieldsets_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}