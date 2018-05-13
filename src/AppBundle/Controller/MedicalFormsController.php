<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\MedicalForms;
use AppBundle\Entity\MedicalFormsFieldsets;
use AppBundle\Form\MedicalFormsType;
use AppBundle\Form\MedicalFormsUpdateType;
use AppBundle\Form\MedicalFormsViewsType;
use AppBundle\Form\MedicalFormsFillType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use AppBundle\Utils\EncrypterFields;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * MedicalForms controller.
 *
 * @Route("/medicalforms")
 */
class MedicalFormsController extends Controller {

    /**
     * Lists all MedicalForms entities.
     *
     * @Route("/", name="medicalforms")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:MedicalForms')->findAll(null, array("specialtie" => "ASC", "id" => "DESC"));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all MedicalForms entities for specialties.
     *
     * @Route("/list/{id}", name="medicalforms_list")
     * @Method("GET")
     * @Template("AppBundle:MedicalForms:index.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function listAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Specialties')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Specialties entity.');
        }

        $entities = $em->getRepository('AppBundle:MedicalForms')->findBy(array('specialtie' => $id), array("specialtie" => "ASC", "id" => "DESC"));

        return array(
            'entityE' => $entity,
            'entities' => $entities,
        );
    }

    /**
     * Creates a new MedicalForms entity.
     *
     * @Route("/", name="medicalforms_create")
     * @Method("POST")
     * @Template("AppBundle:MedicalForms:new.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function createAction(Request $request) {

        $req = $request->request;
        $data = $req->get("appbundle_medicalforms");

        if (isset($data['form_name']) && !empty($data['form_name'])) {
            $data['form_name'] = $this->get('app.stringprocessing')->cleanUp($data['form_name']);
        } else {
            $data['form_name'] = $this->get('app.stringprocessing')->cleanUp($data['name']);
        }
        $req->set("appbundle_medicalforms", $data);

        $entity = new MedicalForms();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);


        if ($form->isValid()) {
            //echo $entity->getFormName();
            //die();
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $entitySet = new MedicalFormsFieldsets();
            $entitySet->setLabel($data['fieldsets']['label']);
            $entitySet->setType('page');
            $entitySet->setMedicalForm($entity);
            $entitySet->setPosition(0);
            $em->persist($entitySet);
            $em->flush();

            $entitySet->setPage($entitySet);
            $em->flush();

            $connection = $em->getConnection();
            $name = "_mffd_" . $entity->getFormName();

            try {
                $table = new \Doctrine\DBAL\Schema\Table($name);
                $table->addColumn('id', 'bigint', array('autoincrement' => true));
                $table->addColumn('medical_forms_field_name', 'string', array('length' => 42, 'customSchemaOptions' => array('collation' => 'utf8_general_ci')));
                $table->addColumn('value_data', 'text');
                $table->addColumn('fos_user_id', 'bigint');
                $table->addColumn('key_enc', 'string', array('length' => 32, 'customSchemaOptions' => array('collation' => 'utf8_general_ci')));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('medical_forms_field_name', 'fos_user_id'));
                foreach ($connection->getDatabasePlatform()->getCreateTableSQL($table) AS $sql) {
                    $connection->executeQuery($sql);
                }
            } catch (\Exception $e) {
                throw $this->createNotFoundException('Unable to find MedicalForms entity.' . $e);
            }

            if (isset($req->get("appbundle_medicalforms")['submit-add'])):

                return $this->redirect($this->generateUrl('medicalformsfields_new_f', array('idf' => $entity->getId(), 'ids' => $entitySet->getId())));

            else:

                return $this->redirect($this->generateUrl('medicalforms_show', array('id' => $entity->getId())));
            endif;
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Creates a form to create a MedicalForms entity.
     *
     * @param MedicalForms $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(MedicalForms $entity) {
        $form = $this->createForm(new MedicalFormsType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submit btnAdm confgTxt rojoFuerte')));
        $form->add('submit-add', 'submit', array('label' => 'Guardar y añadir campos', 'attr' => array('class' => 'submit btnAdm confgTxt rojoFuerte')));

        return $form;
    }

    /**
     * Displays a form to create a new MedicalForms entity.
     *
     * @Route("/new", name="medicalforms_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function newAction() {
        $entity = new MedicalForms();
        $form = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a MedicalForms entity.
     *
     * @Route("/{id}", name="medicalforms_show")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalForms')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $rsm = new ResultSetMappingBuilder($em);
        $rsm->addRootEntityFromClassMetadata('AppBundle\Entity\MedicalFormsFieldsets', 'f');
        $query = $em->createNativeQuery("SELECT F3.*, (CASE WHEN oi IS NULL THEN F3.position ELSE oi END) as oi FROM medical_forms_fieldsets F3 LEFT JOIN( SELECT F1.page, F1.position as oi, F1.id FROM medical_forms_fieldsets F1 WHERE F1.type='page' order by F1.position ) F2 ON F2.page=F3.page WHERE F3.medical_form_id=:id ORDER BY oi ASC, position ASC ", $rsm);
        $query->setParameter('id', $entity->getId());
        $entitiesSets = $query->getResult();

        //$entitiesSets = $em->getRepository('AppBundle:MedicalFormsFieldsets')->findBy(array("medicalForm" => $entity->getId()), array("position" => 'ASC'));

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity' => $entity,
            'delete_form' => $deleteForm->createView(),
            'entitiesSets' => $entitiesSets,
        );
    }

    /**
     * Displays a form to edit an existing MedicalForms entity.
     *
     * @Route("/{id}/edit", name="medicalforms_edit")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalForms')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
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
     * Creates a form to edit a MedicalForms entity.
     *
     * @param MedicalForms $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(MedicalForms $entity) {
        $form = $this->createForm(new MedicalFormsUpdateType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update', 'attr' => array('class' => 'submit btnAdm rojoFuerte')));

        return $form;
    }

    /**
     * Edits an existing MedicalForms entity.
     *
     * @Route("/{id}", name="medicalforms_update")
     * @Method("PUT")
     * @Template("AppBundle:MedicalForms:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalForms')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('medicalforms_edit', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a MedicalForms entity.
     *
     * @Route("/{id}", name="medicalforms_delete")
     * @Method("DELETE")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function deleteAction(Request $request, $id) {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:MedicalForms')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find MedicalForms entity.');
            }

            $name = "_mffd_" . $entity->getFormName();

            $em->remove($entity);
            $em->flush();

            $connection = $em->getConnection();


            try {
                $schema = new \Doctrine\DBAL\Schema\Schema();
                $dTable = $schema->createTable($name);
                $gdPlatform = $connection->getDatabasePlatform();
                $queries = $schema->toDropSql($gdPlatform); //création requetes "DELETE TABLE.....
                $statement = $connection->prepare($queries[0]);
                $statement->execute();
            } catch (\Exception $e) {
                
            }
        }

        return $this->redirect($this->generateUrl('medicalforms'));
    }

    /**
     * Creates a form to delete a MedicalForms entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('medicalforms_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete', 'attr' => array('class' => 'submit btnAdm rojoFuerte')))
                        ->getForm()
        ;
    }

    /**
     * Creates a new MedicalForms entity.
     *
     * @Route("/fill/{id}/save", name="medicalforms_save_fill")
     * @Method("POST")
     * @Template("AppBundle:MedicalForms:fill.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function saveFillAction(Request $request, $id) {


        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $req = $request->request;
        $files = $request->files;
        $filesKeys = $files->keys();


        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalForms')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $form = $this->createForm(new MedicalFormsFillType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_save_fill', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submitSiguiente rojoFuerte')));

        $data = $req->get($entity->getFormName());
        $user = $this->get('security.context')->getToken()->getUser();
        $patient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));

        if (!$patient) {
            throw $this->createNotFoundException('Unable to find patient entity.');
        }


        $idU = $user->getId();
        $conn = $em->getConnection();

        $error = "0";

        if (is_array($data) || $files):

            /*             * ***************************Procesar archivos******************************** */
            $contfile = 0;
            if (is_array($data) === null):
                $data = array();
            endif;

            $tamUp = 0;
            if (($files)):
                foreach ($files as $file) {
                    if (is_array($file)):
                        foreach ($file as $uploadedFile) {
                            if ($uploadedFile !== NULL && $this->validateFile($uploadedFile)) :
                                $tamUp = $tamUp + $uploadedFile->getClientSize();
                            endif;
                        }
                    endif;
                }
            endif;



            if (!is_dir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU))):
                mkdir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU), 0755);
            endif;

            $userStored = $this->dirSize(__DIR__ . '/../../../web/uploads/documents/' . md5($idU));


            if (($patient->getStoragePlan() === null) || ($userStored + $tamUp) < $patient->getStoragePlan()->getSpace()):
                $tamUp = 0;
                if (($files)):
                    foreach ($files as $file) {

                        if (!isset($data[$filesKeys[$contfile]])):
                            $data[$filesKeys[$contfile]] = array();
                        endif;


                        if (is_array($file)):
                            foreach ($file as $uploadedFile) {

                                if ($uploadedFile !== NULL && $this->validateFile($uploadedFile)) {
                                    $name = str_replace('.' . $uploadedFile->guessClientExtension(), '', $uploadedFile->getClientOriginalName());
                                    $name = $name . "." . $uploadedFile->guessExtension(); //sha1(uniqid(mt_rand(), true))

                                    $nameEnc = md5(uniqid(time()));
                                    $contents = file_get_contents($uploadedFile->getRealPath());
                                    $security = new EncrypterFields();
                                    $publicKey = $security->genRandString(32);
                                    $contents = $security->encrypt($contents, $publicKey);
                                    $data[$filesKeys[$contfile]][] = 'web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/' . $nameEnc . '.txt'; //array("name" => $nameEnc . '.txt', "nameOri" => $name, "mimeType" => $uploadedFile->getClientMimeType(), "key" => $publicKey);
                                    $contents = $name . "\n" . $uploadedFile->getClientMimeType() . "\n" . $publicKey . "\n" . $contents;
                                    if (!is_dir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName())):
                                        mkdir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName(), 0755);
                                    endif;
                                    file_put_contents(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/' . $nameEnc . '.txt', $contents);

                                    //$file = $uploadedFile->move(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/', $name);
                                }
                            }
                        elseif ($file):
                            if ($this->validateFile($file)):

                                $name = str_replace('.' . $file->guessClientExtension(), '', $file->getClientOriginalName());
                                $name = $name . "." . $file->guessExtension(); //sha1(uniqid(mt_rand(), true))

                                $nameEnc = md5(uniqid(time()));
                                $contents = file_get_contents($file->getRealPath());
                                $security = new EncrypterFields();
                                $publicKey = $security->genRandString(32);
                                $contents = $security->encrypt($contents, $publicKey);
                                $data[$filesKeys[$contfile]][] = 'web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/' . $nameEnc . '.txt'; //array("name" => $nameEnc . '.txt', "nameOri" => $name, "mimeType" => $uploadedFile->getClientMimeType(), "key" => $publicKey);
                                $contents = $name . "\n" . $file->getClientMimeType() . "\n" . $publicKey . "\n" . $contents;
                                if (!is_dir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName())):
                                    mkdir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName(), 0755);
                                endif;

                                file_put_contents(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/' . $nameEnc . '.txt', $contents);

                            //$file = $uploadedFile->move(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/', $name);
                            endif;
                        endif;


                        $contfile ++; //        
                    }

                endif;


                //$this->clDir($data, __DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/', 'web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/');
                $this->clDirByInp($req->get('filetodel'));

                //$patient->setStored($this->dirSize(__DIR__ . '/../../../web/uploads/documents/' . md5($idU)));
                $em->flush();


            else:
                $error = $userStored;
                return $this->redirect($this->generateUrl('medicalforms_fill', array('id' => $entity->getFormName(), 'ms' => $error)));
            endif;

            //die();

            $keys = array_keys($data);

            /*             * *******************************Insertar registros nuevos o Editar existentes********************************** */
            $res = array();

            $valInsert = "";
            for ($i = 0; $i < count($data); $i++):
                $valInsert .= ',(?,?,?,?)';
            endfor;
            if (strlen($valInsert) > 0): $valInsert = substr($valInsert, 1, strlen($valInsert) - 1);
            endif;

            $query = 'INSERT INTO _mffd_' . $entity->getFormName() . ' (medical_forms_field_name,value_data,fos_user_id, key_enc) VALUES ' . $valInsert . ' ON DUPLICATE KEY UPDATE value_data=VALUES(value_data),key_enc=VALUES(key_enc)';

            $par = array();
            $k = 0;
            foreach ($data as $inp) :
                $par[] = $keys[$k];

                if (is_array($inp)):
                    $inp = array_filter($inp, function($v) {
                        return $v !== '' && $v !== null;
                    });
                    for ($i = 0; $i < count($inp); $i++) :
                        if (is_array($inp[$i])):
                            $inp[$i] = json_encode($inp[$i]);
                        endif;

                    endfor;
                endif;

                $dato = is_array($inp) ? implode("|", $inp) : $inp;
                $security = new EncrypterFields();
                $publicKey = $security->genRandString(32);
                $par[] = $security->encrypt($dato, $publicKey);
                $par[] = $idU;
                $par[] = $publicKey;
                $k++;
            endforeach;

            if (count($par) > 0):
                $res = $conn->executeUpdate($query, $par);
                $pm_f = $conn->fetchArray('SELECT * FROM patients_medical_forms WHERE fos_user_id = ? and medical_forms_id=?', array($idU, $entity->getId()));
                if ($pm_f === false):
                    $conn->executeUpdate('INSERT INTO patients_medical_forms (fos_user_id,medical_forms_id) VALUES (?,?)', array($idU, $entity->getId()));
                endif;
            endif;

        endif;

        //$entitiesAll = $this->getFieldsForms($entity, $em);

        return $this->redirect($this->generateUrl('medicalforms_fill', array('id' => $entity->getFormName(), 'ms' => $error, 'filter' => 0, 'page' => (($req->get('nextpage') !== null) && $req->get('nextpage') != '' ? $req->get('nextpage') : $req->get('page')))));
    }

    /**
     * Fill MedicalForms.
     *
     * @Route("/fill/{id}/{ms}/{filter}/{page}", name="medicalforms_fill")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     * 
     */
    public function fillAction($id, $ms = null, $filter = null, $page = 0) {

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:MedicalForms')->findOneBy(array("formName" => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $form = $this->createForm(new MedicalFormsFillType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_save_fill', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submitSiguiente rojoFuerte')));

        $entitiesAll = $this->getFieldsForms($entity, $em, false, ($filter === '0' ? null : $filter), null, null, null, $page);

        $user = null;
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.context')->getToken()->getUser();
        }

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user entity.');
        }

        if ($this->get('security.context')->isGranted('ROLE_PATIENT')) {
            $patient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));

            if (!$patient) {
                throw $this->createNotFoundException('Unable to find patient entity.');
            }
        } else {
            $patient = null;
        }


        if ($ms > 0):
            $ms = "<p class='cuadro uno errorFile'>El tamaño de los archivos que esta intentado subir (" . $ms . " MB) sobrepasa su espacio disponible: " . (round(($patient->getStoragePlan()->getSpace() - $patient->getStored()) / 1048576, 2)) . " MB, <a href='#' class='closeBtn' onclick='$(this).parent().remove();return false;'>x<a> <a href='#' >actualice su plan de almacenamiento aquí</a></p>";
        else:
            $ms = '';
        endif;


        return array(
            'entity' => $entity,
            'entities' => $entitiesAll,
            'form' => $form->createView(),
            'ms' => $ms,
            'patient' => $patient,
            'page' => $page,
        );
    }
    
    /**
     * FillMed MedicalForms.
     *
     * @Route("/fillmed/{id}/{page}", name="medicalforms_fill_med")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PHYSICIANS') or has_role('ROLE_ADMIN')") 
     * 
     */
    public function fillMedAction($id, $page = 0) {
        
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:MedicalForms')->findOneBy(array("formName" => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $form = $this->createForm(new MedicalFormsFillType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_save_fill', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submitSiguiente rojoFuerte')));

        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));        
        $entityF = $em->getRepository('AppBundle:MedicalFormsViews')->findOneBy(array('physician' => $physician));
        $filter = $entityF->getId();
        
        $entitiesAll = $this->getFieldsForms($entity, $em, true, ($filter === null || $filter === '0' ? null : $filter), null, null, null, $page);

        $user = null;
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.context')->getToken()->getUser();
        }

        if (!$user) {
            throw $this->createNotFoundException('Unable to find user entity.');
        }

        if ($this->get('security.context')->isGranted('ROLE_PATIENT')) {
            $patient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));

            if (!$patient) {
                throw $this->createNotFoundException('Unable to find patient entity.');
            }
        } else {
            $patient = null;
        }

        $ms = 0;
        if ($ms > 0):
            $ms = "<p class='cuadro uno errorFile'>El tamaño de los archivos que esta intentado subir (" . $ms . " MB) sobrepasa su espacio disponible: " . (round(($patient->getStoragePlan()->getSpace() - $patient->getStored()) / 1048576, 2)) . " MB, <a href='#' class='closeBtn' onclick='$(this).parent().remove();return false;'>x<a> <a href='#' >actualice su plan de almacenamiento aquí</a></p>";
        else:
            $ms = '';
        endif;


        return array(
            'entity' => $entity,
            'entities' => $entitiesAll,
            'form' => $form->createView(),
            'ms' => $ms,
            'patient' => $patient,
            'page' => $page,
            'filter' => $filter,
        );
    }

    /**
     * Creates a new MedicalForms entity.
     *
     * @Route("/fill/consultations/{id}/save", name="medicalforms_save_fill_consultations")
     * @Method("POST")
     * @Template("AppBundle:MedicalForms:fillConsultations.html.twig")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function saveFillConsultationsAction(Request $request, $id) {

        if (!$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('fos_user_security_login'));
        }

        $req = $request->request;
        $files = $request->files;
        $filesKeys = $files->keys();


        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalForms')->find($id);
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $form = $this->createForm(new MedicalFormsFillType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_save_fill', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Continuar Consulta', 'attr' => array('class' => 'submitSiguiente rojoFuerte')));

        $data = $req->get($entity->getFormName());


        $idC = $req->get("consultation");
        $consultation = $em->getRepository('AppBundle:Consultations')->findOneBy(array('id' => $idC)); //VERIFICAR ESTATUS        
        if (!$consultation) {
            throw $this->createNotFoundException('Unable to find consultation entity.');
        }

        $user = $this->get('security.context')->getToken()->getUser();
        $patient = $em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user));

        if (!$patient) {
            throw $this->createNotFoundException('Unable to find patient entity.');
        }


        $idU = $user->getId();
        $conn = $em->getConnection();

        $error = "0";

        if (is_array($data) || $files):

            /*             * ***************************Procesar archivos******************************** */
            $contfile = 0;
            if (is_array($data) === null):
                $data = array();
            endif;

            $tamUp = 0;
            if (is_array($files) || is_object($files))
            // esta linea if (($files)): fue cambiada por la linea anterior porque el foreach arroja warning  
               foreach ($files as $file) {
                if (is_array($file) || is_object($file))
               foreach ($file as $uploadedFile) {
                   
                        if ($uploadedFile !== NULL && $this->validateFile($uploadedFile)) :
                            $tamUp = $tamUp + $uploadedFile->getClientSize();
                        endif;
                    }
                }
            //endif;


            if (!is_dir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU))):
                mkdir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU), 0755);
            endif;

            $userStored = $this->dirSize(__DIR__ . '/../../../web/uploads/documents/' . md5($idU));


            if (($patient->getStoragePlan() === null) || ($userStored + $tamUp) < $patient->getStoragePlan()->getSpace()):
                $tamUp = 0;
                if (($files)):
                    foreach ($files as $file) {

                        if (!isset($data[$filesKeys[$contfile]])):
                            $data[$filesKeys[$contfile]] = array();
                        endif;
                        // se adiciono esta linea a cada foreach ..
                        if (is_array($file) || is_object($file))
                        foreach ($file as $uploadedFile) {
                            if ($uploadedFile !== NULL && $this->validateFile($uploadedFile)) {
                                $name = str_replace('.' . $uploadedFile->guessClientExtension(), '', $uploadedFile->getClientOriginalName());
                                $name = $name . "." . $uploadedFile->guessExtension(); //sha1(uniqid(mt_rand(), true))

                                $nameEnc = md5(uniqid(time()));
                                $contents = file_get_contents($uploadedFile->getRealPath());
                                $security = new EncrypterFields();
                                $publicKey = $security->genRandString(32);
                                $contents = $security->encrypt($contents, $publicKey);
                                $data[$filesKeys[$contfile]][] = 'web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/' . $nameEnc . '.txt'; //array("name" => $nameEnc . '.txt', "nameOri" => $name, "mimeType" => $uploadedFile->getClientMimeType(), "key" => $publicKey);
                                $contents = $name . "\n" . $uploadedFile->getClientMimeType() . "\n" . $publicKey . "\n" . $contents;
                                if (!is_dir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName())):
                                    mkdir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName(), 0755);
                                endif;
                                file_put_contents(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/' . $nameEnc . '.txt', $contents);

                                //$data[$filesKeys[$contfile]][] = $name;
                                //$file = $uploadedFile->move(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/', $name);
                            }
                        }

                        $contfile ++; //        
                    }
                elseif ($file):
                    if ($this->validateFile($file)):

                        $name = str_replace('.' . $file->guessClientExtension(), '', $file->getClientOriginalName());
                        $name = $name . "." . $file->guessExtension(); //sha1(uniqid(mt_rand(), true))

                        $nameEnc = md5(uniqid(time()));
                        $contents = file_get_contents($file->getRealPath());
                        $security = new EncrypterFields();
                        $publicKey = $security->genRandString(32);
                        $contents = $security->encrypt($contents, $publicKey);
                        $data[$filesKeys[$contfile]][] = 'web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/' . $nameEnc . '.txt'; //array("name" => $nameEnc . '.txt', "nameOri" => $name, "mimeType" => $uploadedFile->getClientMimeType(), "key" => $publicKey);
                        $contents = $name . "\n" . $file->getClientMimeType() . "\n" . $publicKey . "\n" . $contents;
                        if (!is_dir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName())):
                            mkdir(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName(), 0755);
                        endif;

                        file_put_contents(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/' . $nameEnc . '.txt', $contents);

                    //$file = $uploadedFile->move(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/', $name);
                    endif;
                endif;


                //$this->clDir($data, __DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/', 'web/uploads/documents/' . md5($idU) . '/' . $entity->getFormName() . '/');
                $this->clDirByInp($req->get('filetodel'));

                //$patient->setStored($this->dirSize(__DIR__ . '/../../../web/uploads/documents/' . md5($idU)));
                $em->flush();


            else:
                $error = $userStored;
                return $this->redirect($this->generateUrl('medicalforms_fill_consultations', array('id' => $entity->getFormName(), 'idc' => $idC, 'filter' => $req->get('filter'), 'page' => (($req->get('nextpage') !== null) && $req->get('nextpage') != '' ? $req->get('nextpage') : $req->get('page')))));
            endif;

            $keys = array_keys($data);

            /*             * *******************************Insertar registros nuevos o Editar existentes********************************** */
            $res = array();

            $valInsert = "";
            $valInsertI = "";
            for ($i = 0; $i < count($data); $i++):
                $valInsert .= ',(?,?,?,?,?,?)';
                $valInsertI .= ',(?,?,?,?)';
            endfor;
            if (strlen($valInsert) > 0): $valInsert = substr($valInsert, 1, strlen($valInsert) - 1);
            endif;
            if (strlen($valInsertI) > 0): $valInsertI = substr($valInsertI, 1, strlen($valInsertI) - 1);
            endif;

            $query = 'INSERT INTO _mffd_' . $entity->getFormName() . '_view (medical_forms_field_name,value_data,fos_user_id,consultation_id,date_creation,key_enc) VALUES ' . $valInsert . ' ON DUPLICATE KEY UPDATE value_data=VALUES(value_data),key_enc=VALUES(key_enc)';
            $queryI = 'INSERT INTO _mffd_' . $entity->getFormName() . ' (medical_forms_field_name,value_data,fos_user_id,key_enc) VALUES ' . $valInsertI . ' ON DUPLICATE KEY UPDATE value_data=VALUES(value_data),key_enc=VALUES(key_enc)';

            $dNow = new \DateTime("now");
            $strNow = $dNow->format("Y-m-d H:i:s");

            $par = array();
            $parI = array();
            $k = 0;
            foreach ($data as $inp) :
                $par[] = $keys[$k];
                $parI[] = $keys[$k];

                if (is_array($inp)):
                    $inp = array_filter($inp, function($v) {
                        return $v !== '' && $v !== null;
                    });
                endif;

                $dato = is_array($inp) ? implode("|", $inp) : $inp;
                $security = new EncrypterFields();
                $publicKey = $security->genRandString(32);
                $par[] = $security->encrypt($dato, $publicKey);
                //$par[] = EncrypterFields::encrypt($dato);//$dato;
                $publicKeyI = $security->genRandString(32);
                $parI[] = $security->encrypt($dato, $publicKeyI);
                //$parI[] = EncrypterFields::encrypt($dato);//$dato;
                $par[] = $idU;
                $parI[] = $idU;
                $parI[] = $publicKeyI;
                $par[] = $consultation->getId();
                $par[] = $strNow;
                $par[] = $publicKey;
                $k++;
            endforeach;

            if (count($par) > 0):
                $res = $conn->executeUpdate($query, $par);
                $res = $conn->executeUpdate($queryI, $parI);
                $pm_f = $conn->fetchArray('SELECT * FROM patients_medical_forms WHERE fos_user_id = ? and medical_forms_id=?', array($idU, $entity->getId()));
                if ($pm_f === false):
                    $conn->executeUpdate('INSERT INTO patients_medical_forms (fos_user_id,medical_forms_id) VALUES (?,?)', array($idU, $entity->getId()));
                endif;
            endif;

        endif;

        //$entitiesAll = $this->getFieldsForms($entity, $em);
        //var_dump($req->get('nextpage'));die();
        if (($req->get('nextpage') !== null) && $req->get('nextpage') === '-1'):
            return $this->redirect($this->generateUrl('consultations_pay', array('idc' => $consultation->getId())));
        else:
            return $this->redirect($this->generateUrl('medicalforms_fill_consultations', array('id' => $entity->getFormName(), 'idc' => $idC, 'filter' => $req->get('filter'), 'page' => (($req->get('nextpage') !== null) && $req->get('nextpage') != '' ? $req->get('nextpage') : $req->get('page')))));
        endif;
    }

    /**
     * Fill MedicalForms.
     *
     * @Route("/fillconsultations/{id}/{idc}/{filter}/{page}", name="medicalforms_fill_consultations")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function fillConsultationsAction($id, $idc, $filter = null, $page = 0) {



        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:MedicalForms')->findOneBy(array("formName" => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $form = $this->createForm(new MedicalFormsFillType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_save_fill_consultations', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Continuar Consulta', 'attr' => array('class' => 'submitSiguiente rojoFuerte')));

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

        $consultation = $em->getRepository('AppBundle:Consultations')->findOneBy(array('id' => $idc));
        //HACER UPDATE PARA CAMBIAR ESTATUS H DATOS CARGADOS
        if (!$consultation) {
            throw $this->createNotFoundException('Unable to find consultation entity.');
        }
        
        //FILTRAR POR EL FORMULARIO DEL MEDICO DE LA CONSULTA        
        $physician = $consultation->getPhysician();        
        $entityF = $em->getRepository('AppBundle:MedicalFormsViews')->findOneBy(array('physician' => $physician));
        
        if ($entityF):
            $filterMed = $entityF->getId();
            else:
                return $this->redirect($this->generateUrl('consultations_new_type', array('type' => $consultation->getModalityConsultation()->getTag())));
            
        endif;
        
        
        $entitiesAll = $this->getFieldsForms($entity, $em, false, $filterMed, null, $consultation->getId(), 'new', $page);

        return array(
            'entity' => $entity,
            'entities' => $entitiesAll,
            'form' => $form->createView(),
            'patient' => $patient,
            'consultation' => $consultation,
            'filter' => $filter,
            'page' => $page,
        );
    }

    /**
     * Fill MedicalForms.
     *
     * @Route("/view/{id}/{page}", name="medicalforms_view")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN')") 
     */
    public function viewAction($id, $page = 0) {


        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:MedicalForms')->findOneBy(array("formName" => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $form = $this->createForm(new MedicalFormsFillType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_save_fill', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Guardar', 'attr' => array('class' => 'submitSiguiente rojoFuerte')));

        $entitiesAll = $this->getFieldsForms($entity, $em, false, null, null, null, null, $page);

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

//        echo"<pre>";
//        \Doctrine\Common\Util\Debug::dump($entitiesAll,4);
//        echo"</pre>";
//        exit();

        return array(
            'entity' => $entity,
            'entities' => $entitiesAll,
            'form' => $form->createView(),
            'patient' => $patient,
            'page' => $page,
        );
    }

    /**
     * Fill MedicalForms.
     *
     * @Route("/view/shared/{id}/{pat}/{page}", name="medicalforms_view_shared")
     * @Method("GET")
     * @Template()
     * 
     */
    public function viewSharedAction($id, $pat, $page = 0) {

        //$this->denyAccessUnlessGranted('ROLE_PHYSICIANS', null, 'Unable to access this page!');

        $em = $this->getDoctrine()->getManager();

        $shared = null;
        $token = null;

        $patient = $em->getRepository('AppBundle:Patients')->find($pat);
        if (!$patient) {
            $shared = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $pat));
            if (!$shared) {
                throw $this->createNotFoundException('Unable to find Patients Token entity.');
            }
            $patient = $shared->getPatient();
            $token = $pat;
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
                    array('physician' => $entityPhy, 'patient' => $patient)
            );

            if ($entityCons === null):
                return $this->redirect($this->generateUrl('physicians_show_front', array('id' => $entityPhy->getId())));
            endif;
        endif;

        /* Vista solo para usuarios relacionados por compartir */
        if ($this->get('security.context')->isGranted('ROLE_GUESS')) :
            $user = $this->get('security.context')->getToken()->getUser();
            $entitySh = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(
                    array('patient' => $patient, 'email' => $user->getEmail())
            );

            if ($entitySh === null):
                return $this->redirect($this->generateUrl('patientssharemedicalhistory_guess'));
            endif;
        endif;

        $entity = $em->getRepository('AppBundle:MedicalForms')->findOneBy(array("formName" => $id));

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }



        $entitiesAll = $this->getFieldsForms($entity, $em, false, null, $patient->getUser(), null, null, $page);

        return array(
            'entity' => $entity,
            'entities' => $entitiesAll,
//            'form' => $form->createView(),
            'patient' => $patient,
            'token' => $token,
            'page' => $page,
        );
    }

    /**
     * View file in MedicalForms.
     *
     * @Route("/view/file/{medicalforms}/{filename}/{id}", name="medicalforms_view_file")
     * @Security("has_role('ROLE_PATIENT') or has_role('ROLE_ADMIN') or has_role('ROLE_GUESS') or has_role('ROLE_PHYSICIANS')")
     * 
     */
    public function viewFileAction($medicalforms, $filename, $id) {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->getDoctrine()->getManager();
        $idU = $user->getId();

        $entity = $em->getRepository('AppBundle:Patients')->find($id);
        if ($entity !== null):
            $idU = $entity->getUser()->getId();
        endif;
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
            $entitySh = $em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(
                    array('patient' => $entity, 'email' => $user->getEmail())
            );

            if ($entitySh === null):
                return $this->redirect($this->generateUrl('patientssharemedicalhistory_guess'));
            endif;
        endif;

        if (is_file(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $medicalforms . '/' . $filename . '.txt')):
            $cifrado = file(__DIR__ . '/../../../web/uploads/documents/' . md5($idU) . '/' . $medicalforms . '/' . $filename . '.txt');
            header('Content-type: ' . $cifrado[1]);
            $security = new EncrypterFields();
            $publicKey = trim($cifrado[2]);
            $content = $security->decrypt($cifrado[3], $publicKey);
            echo ($content);
        else:
            return $this->render(
                            "AppBundle:MedicalForms:filenotfinded.html.twig"
            );
        endif;

//        return new StreamedResponse(function () use ($content) {
//            echo $content;
//        });
    }

    public function getFieldsForms($entity, $em, $actu = false, $filter = null, $user = null, $idC = null, $action = null, $page = 0) {


        if ($user === null && $this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.context')->getToken()->getUser();
        }

        ///BUSCAR FILTROS
        $fieldsFilter = array();
        $fieldsetsFilter = array();
        $required = array();
        if ($filter !== null):
            $filters = $em->getRepository('AppBundle:MedicalFormsViews')->find($filter);
            if ($filters):
                if ($filters->getFields() !== ''):
                    $fieldsFilter = explode(',', $filters->getFields());
                endif;
                if ($filters->getFieldsets() !== ''):
                    $fieldsetsFilter = explode(',', $filters->getFieldsets());
                endif;
                if ($filters->getRequired() !== ''):
                    $required = explode(',', $filters->getRequired());
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
                                    . "CALL GetFieldsByFieldsetUserCons(:id,:idu,'" . $nameTableCont . "')"
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


                    $entitiesFl = (($entityFs->getId() === $page || ( $entityFs->getPage()!==null && $entityFs->getPage()->getId() === $page) ) ? $query->getResult() : array()); //$query->getResult();
                    //Ejecutar FILTRO

                    $entitiesFlAux = array();
                    foreach ($entitiesFl as $field) :
                        if (in_array($field->getName(), $fieldsFilter)):
                            $entitiesFlAux[] = $field;
                        endif;
                        if ($filter !== null):
                            if (in_array($field->getName(), $required)):
                                $field->setRequired(1);
                            else:
                                $field->setRequired(0);
                            endif;
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

    public function getFieldsFormsAdm($entity) {

        $em = $this->getDoctrine()->getManager();

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
            $query = $em->createNativeQuery("SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid) F2 ON F2.subgroup=F3.subgroup WHERE F3.medical_forms_fieldset_id=:id ORDER BY oi ASC,  og  ASC, orderid ASC ", $rsm);
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

    function dirSize($directory) {
        $size = 0;
        if (is_dir($directory)):
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
                $size+=$file->getSize();
            }
        endif;

        return $size;
    }

    function clDir($files, $dir, $dirB = null) {

        if (is_dir($dir)):
            $iterator = new \DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    if ($this->ras($dirB . $fileinfo->getFilename(), $files) === false):
                        unlink($fileinfo->getPathname());
                    endif;
                }
            }
        endif;
    }

    function clDirByInp($files) {
        $files = explode("|", $files);
        if (is_array($files)):
            foreach ($files as $file) {
                if (is_file(__DIR__ . '/../../../' . $file)):
                    unlink(__DIR__ . '/../../../' . $file);
                endif;
            }
        endif;
    }

    /**
     * Recursive array search.
     *
     * See http://php.net/manual/en/function.array-search.php#91365
     *
     * @param $needle
     *   The searched value.
     * @param $haystack
     *   The array.
     *
     * @return bool|int|string
     *   Array of keys, containing values or FALSE if not found.
     */
    private function ras($needle, $haystack) {
        $keys = array();
        foreach ($haystack as $key => $value) {
            if ($needle === $value OR ( is_array($value) && $this->ras(
                            $needle, $value
                    ) !== FALSE)
            ) {
                $keys[] = $key;
            }
        }
        if (!empty($keys)) {
            return $keys;
        }

        return FALSE;
    }

    /**
     * Vaidate file.
     *
     * 
     * @param $file
     *   
     * @return bool
     *   tru or FALSE if not found.
     */
    private function validateFile(UploadedFile $file) {

        $mimePer = array('application/pdf', 'application/vnd.ms-excel', 'application/vnd.ms-excel', 'image/bmp', 'image/gif', 'image/jpeg', 'image/jpeg', 'image/jpeg', 'image/tiff', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/plain', 'image/png');

        if (!in_array($file->getMimeType(), $mimePer) || $file->getSize() > 20971520 || !$file->isValid()):
            return false;
        endif;

        return true;
    }

}