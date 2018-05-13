<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\MedicalForms;
use AppBundle\Entity\MedicalFormsViews;
use AppBundle\Entity\MedicalFormsFieldsets;
use AppBundle\Form\MedicalFormsViewsType;
use AppBundle\Form\MedicalFormsViewsUpdateType;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * MedicalFormsViews controller.
 *
 * @Route("/medicalformsviews")
 */
class MedicalFormsViewsController extends Controller {

    /**
     * Lists all MedicalFormsViews entities.
     *
     * @Route("/", name="medicalformsviews")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:MedicalFormsViews')->findAll(null, array("specialty" => "ASC", "id" => "DESC"));

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Lists all MedicalFormsViews entities.
     *
     * @Route("/med", name="medicalformsviewsmed")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function indexMedAction() {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        
        $entities = $em->getRepository('AppBundle:MedicalFormsViews')->findOneBy(array('physician' => $physician));
        
        return array(
            'entities' => $entities,
        );
    }

    
    /**
     * Displays a form to edit an existing MedicalFormsViews entity.
     *
     * @Route("/{id}/edit", name="medical_forms_views_edit")
     * @Method("GET")
     * @Template("AppBundle:MedicalFormsViews:createView.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalFormsViews')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalFormsViews entity.');
        }

        $editForm = $this->createEditForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        $entities = $this->getFieldsFormsAdm($entity->getMedicalForm());
        
        $entity->setFields("'".str_replace(',', "','", $entity->getFields())."'");
        $entity->setFieldsets("'".str_replace(',', "','", $entity->getFieldsets())."'");
        $entity->setRequired("'".str_replace(',', "','", $entity->getRequired())."'");

        return array(
            'entity' => $entity,
            'entityf' => $entity->getMedicalForm(),
            'entities' => $entities,
            'form' => $editForm->createView(),
        );
//        return array(
//            'entity'      => $entity,
//            'edit_form'   => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
//        );
    }

    /**
    * Creates a form to edit a MedicalFormsViews entity.
    *
    * @param MedicalFormsViews $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(MedicalFormsViews $entity)
    {
        $form = $this->createForm(new MedicalFormsViewsType(), $entity, array(
            'action' => $this->generateUrl('medical_forms_views_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    /**
     * Edits an existing MedicalFormsViews entity.
     *
     * @Route("/{id}", name="medical_forms_views_update")
     * @Method("PUT")
     * @Template("AppBundle:MedicalFormsViews:edit.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $entity = $em->getRepository('AppBundle:MedicalFormsViews')->find($id);

        if (!$entity) {  throw $this->createNotFoundException('Unable to find MedicalFormsViews entity.');  }
        
        $form = $this->createEditForm($entity);
        $form->handleRequest($request);
   
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if (is_array($request->request->get("fields"))): $entity->setFields(implode(',',$request->request->get("fields")));
            else:  $entity->setFields($request->request->get("fields")); endif;       
            if (is_array($request->request->get("fieldsets"))): $entity->setFieldsets(implode(',',$request->request->get("fieldsets")));
            else:  $entity->setFieldsets($request->request->get("fieldsets")); endif;               
            
            if (is_array($request->request->get("reque"))): $entity->setRequired(implode(',',$request->request->get("reque")));
            else:  $entity->setRequired($request->request->get("reque")); endif; 
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('medical_forms_views_edit', array('id' => $entity->getId())));
        }

        $entityf = $em->getRepository('AppBundle:MedicalForms')->find($entity->getMedicalForm()->getId());
        $entities = $this->getFieldsFormsAdm($entityf);

        return array(
            'entity' => $entity,'entityf' => $entityf, 'entities' => $entities, 'form' => $form->createView(),
        );
    }
    
    
    
    
    /**
     * Displays a form to edit an existing MedicalFormsViews entity.
     *
     * @Route("/edit/med", name="medical_forms_views_edit_med")
     * @Method("GET")
     * @Template("AppBundle:MedicalFormsViews:createViewMed.html.twig")
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function editMedAction()
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        $entity = $em->getRepository('AppBundle:MedicalFormsViews')->findOneBy(array('physician' => $physician));
          
        //$entity = $em->getRepository('AppBundle:MedicalFormsViews')->find($id);

        if (!$entity) {
            $entity = new MedicalFormsViews();
            $entity->setName('Especialista: '.$physician->getUser()->getName().' '.$physician->getUser()->getLastName());
            $entity->setFormName('informaci_n_general_view');
            $entity->setFieldsets('14,10,15,11,17,27,29,30,31,32,33,34,35,36,18,19,22,37,23,47,24,49,25,50,28,51');
            $entity->setFields('raza_u_origen__tnico,pa_s_de_origen,peso,__ltimo_peso_,unidades_de_masa_de_,talla,__ltima_talla___esta,unidades_de_tallas,__ltima_presi_n_arte,__ltimo_pulso_o_frec,motivo_por_el_que_de,archivos_m_dicos_rel,hipertensi_n_arteria,_alta_presi_n_de_san,describa_de_hiperten,coronaria,_enfermedad_de_las_a,describa_de_enfermed,enf_coronarias_info_,_infarto_o_ataque_al,describa_de_infarto_,_le_han_colocado_ste,describa_stents_o_he,_le_han_hecho_cirug_,describa__bypass_,card_aca,_insuficiencia_card_,describa_de_insufici,insuf_cardiaca_infor,__si_conoce_su_fracc,reporte_de_ecocardio,vascular,_enfermedad_vascular,describa_de__enferme,enf_vascular_informa,le_han_colocado_sten,describa_de_le_han_c,problemas_del_ritmo_,_problemas_del_ritmo,describa_de_problema,prob_rit_card_inform,_toma_anticoagulante,describa_de_toma_ant,_otros_,bronquitis,_bronquitis_cr_nica_,describa_bronquitis,asma,_asma_,describa__asma,apnea_de_sue_o,_apnea_de_sue_o_,describa_apnea_de_su,apnea_de_sue_o_infor,__usa_m_quina_de_cpa,describa_de_usa_m_qu,accidentes_cerebrova,_accidentes_cerebrov,describa__acv__derra,convulsiones_o_epile,_convulsiones_o_epil,describa_convulsione,esclerosis_m_ltiple,_esclerosis_m_ltiple,describa_esclerosis_,otros_neurol_gico,_otros_neurologico,describa_neurologico,colesterol_o_triglic,_colesterol_o_trigli,colest_triglic_infor,__por_favor_anexe_su,exa_anexo_col_tri,obesidad,_obesidad_,obesidad_informacion,__describa_aproximad,trata_esp_quiri_obes,tiroides,_problemas_de_tiroid,describa_tiroides,diabetes,_diabetes_,diabetes_informaci_n,_est__controlada_,describa_diabetes_co,diabetes_tipo,_otros_azucar,_ha_sido_diagnostica,diag_positivo_inform,por_favor_describa_q,cu_ndo_fue_diagnosti,complet__trata_cance,_tuvo_cirug_a_cancer,_se_ha_hecho_alguna_,colonoscopia_informa,_por_favor_describa_,se_encontr__alguna_l,se_ha_hecho_tomograf,tomograf___informaci,por_favor_describa_c,tomogrf_se_encontr,_ha_tenido_problemas,describa_ha_tenido_p,_otros_cancer2,describa_otros_cance,es_mujer,campos_de_es_mujer,edad_de_primera_mens,_cu_l_fue_su__ltima_,_cuanto_d_as_le_dura,_es_regular__,_usa_alg_n_m_todo_an,metodo_anticonceptiv,_que_tipo_de_m_todo_,_por_cuanto_tiempo__,_tuvo_menopausia_nat,_tiene_usted_su__ter,describa_cuando_se_l,_tiene_usted_sus_ova,describa_cirug_a,_se_ha_hecho_alg_n_p,por_favor_describapa,se_ha_hecho_mamograf,por_favor_describama,_ha_tomado_hormonas_,_por_cu_nto_tiempo__,_cuando_fue_la__ltim,est__embarazada_,_otros_embarazos,es_hombre,grupo_es_hombre,_sufre_de_disfunci_n,ha_tomado_medic__inf,_toma_o_ha_tomado_me,respuesta_satisfacto,_ha_sido_la_respuest,describa_ha_sido_la_,prostata,_se_ha_hecho_alg_n_e,describa_se_ha_hecho,_problemas_de_pr_sta,describa_prostata,_otros_hombre,_gastritis_o_reflujo,describa_gastritis_o,gastritis,_ulceras__,describa_ulceras__,_enfermedades_inflam,describir_enfermedad,_enfermedad_cel_aca_,describa_enfermedad_,_enfermedades_del_h_,enfermedad_higado,_asociado_a_consumo_,describa_asociado_a_,_cirrosis_,describa_cirrosis,_h_gado_graso__,describa_h_gado_gras,_hepatitis__,describa_hepatitis,_otros_h__gastrointe,_falla_o_insuficienc,falla_insuf_renal_in,_di_lisis__,dialisis,describa_tipo_,fecha_de_inicio_,_trasplante_renal_,describa_trasplante_,ri_ones,_piedras_en_los_ri_o,describa_piedras_en_,incontinencia_urinar,_incontinencia_urina,describa_incontinenc,otros__,_ha_tenido_alguna_de,hiv_sida,_hiv_sida__,describa_hiv_sida,_otros_infecciones,_ha_recibido_la_vacu,describa_cuando_fue_,ha_recibido_la_vacu_,describa_cuando_grip,listado_de_cirug_as_,tipo_de_cirug_a_u_ho,fecha_aproximada_cir,complicaciones_ciruj,listado_de_medicamen,nombre_comercial_y_g,dosis,cuantas_veces_al_d_a,fecha_de_inicio_apro,medicamentos,_a_medicamentos__,grupo_descrip_de_med,descrip_de_medicamen,medicamento_alerg,reaccion_medi_alerg,_uso_de_tabaco__actu,uso_de_tabaco__actua,_dej__de_fumar___caj,_a_os_que_fum____,_fecha_en_que_dej__e,_tipo_de_tabaco__,fuma_actualmente,_fuma_actualmente__c,fecha_de_inicio,tipo_de_tabaco_,humo_de_segunda_mano,humo_de_segunda_quie,_uso_de_alcohol__act,uso_de_alcohol__actu,_dejo_de_tomar__a_os,_fecha_alcohol,_toma_actualmente__t,_tipo_de_alcohol__,_uso_de_drogas_recre,drogas_recientes,_tipo_,_cantidad_,_con_que_frecuencia_,_realiza_alg_n_tipo_,actividad_f_sica,_tipo_de_actividad__,_duraci_n_en_minutos,_veces_por_semana__,_cuanto_puede_camina,cuanto_puede_cam,_que_lo_detiene__,_esta_empleado_o_rea,_qu__tipo_de_trabajo,alta_presi_n_de_sang,a__alta_presi_n_de_s,describa_alta_presi_,diabetes_familiar,_diabetes_fam,enfermedades_del_cor,_enfermedades__del_c,describa_enfermedade,_apro_a_que_edad_dia,enfermedad_cerebrova,_enfermedad_cerebrov,describa_cerebrovasc,enfermedades_autoinm,_enfermedades_autoin,describa__autoinmune,_c_ncer__,_c_ncer__familiar,describa_cancer_,enfermedades_renales,transplante_renal,describa_transplante,cansancio,_cansancio_,describa_cansancio,fiebre,_fiebre_,describa_fiebre,escalofr_os,_escalofr_os_,describa_escalofr_os,p_rdida_de_peso,_p_rdida_de_peso_,describa_perdida_de_,sudores_nocturnos,_sudores_nocturnos_,describa_sudores_noc,palpitaciones,_palpitaciones__,describa_palpitacion,dolor_de_pecho_revis,_dolor_de_pecho_,describa_dolor_de_pe,ronquido_nocturno,_ronquido_nocturno_,describa_ronquido_no,sue_o_excesivo_duran,_sue_o_excesivo_dura,describa_sue_o_exces,pies_hinchados,_pies_hinchados_,describa_pies_hincha,sed_excesiva,_sed_excesiva_,describa_sed_excesiv,orina_excesiva,_orina_excesiva_,describa_orina_exces,orina_frecuente,tiene_que_levantarse,describa_orina_de_no,_tiene_que_orinar_fr,describa_frecuencia,_siente_que_vacia_la,describa_el_vacio_de,__tiene_incontinenci,describa_incont_orin,_tiene_urgencia_o_va,describa_urgencia_o_,visi_n_borrosa,_visi_n_borrosa_,describa_visi_n_borr,indigesti_n,_indigesti_n_,describa_indigesti_n,diarrea,_diarrea_,describa_diarrea,estre_imiento,_estre_imiento_,describa_estre_imien,dolor_abdominal,_dolor_abdominal_,describa_dolor_abdom,n_useas,_n_useas_,describa_nauseas,v_mito,_v_mito_,describa_v_mito,vomito_con_sangre,_vomito_con_sangre_,describa_vomito_con_,sangre_en_las_heces,_sangre_en_las_heces,describa_sangre_en_l,orina_oscura,_orina_oscura_,describa_orina_oscur,ojos_amarillos,_ojos_amarillos_,describa_ojos_amaril,hemorroides,_hemorroides_,describa_hemorroides,p_rdida_de_apetito,_p_rdida_de_apetito_,describa_p_rdida_de_,ardor_al_orinar,_ardor_al_orinar_,describa_ardor_al_or,sangre_en_la_orina,_sangre_en_la_orina_,describa_san_enorina,hace_moretones_con_f,_hace_moretones_con_,describa_hace_moreto,le_han_dado_transfus,_le_han_dado_transfu,describa_le_han_dado,ansiedad,_ansiedad_,describa_ansiedad,depresi_n,_depresi_n_,describa_depresi_n,problemas_para_dormi,_problemas_para_dorm,describa_problemas_p,problemas_psiqui_tri,problemas_psiqui,describa_probl_psiqu,cambios_de_memoria,_cambios_de_memoria_,describa_cambios_de_,debilidad_en_alg_n_m,_debilidad_en_alg_n_,describa_debilidad_e,sensaci_n_de_hormigu,_sensaci_n_de_hormig,describa_sensaci_n_d,cambios_en_la_visi_n,_cambios_en_la_visi_,describa_cambios_en_,par_lisis,_par_lisis_,describa_par_lisis,temblores,_temblores_,describa_temblores,dolor,solo_dolor,describa_solo_dolor,perdida_de_coordinac,_perdida_de_coordina,describa_perdida__co,se_le_hinchan_las_ar,_se_le_hinchan_las_a,describa_se_le_hinch,dolor_de_articulacio,_dolor_de_articulaci,describa_dolor_de_ar,dolores_musculares,_dolores_musculares_,describa_dolores_mus,calambres,_calambres_,describa_calambres,p_rdida_de_cabello,_p_rdida_de_cabello_,describa_perdida_cab,intolerancia_al_calo,_intolerancia_al_cal,describa_intoleranci,bochornos___calores,_bochornos___calores,describa_bochornos__,salpullido,_salpullido_,describa_salpullido');
            $entity->setSpecialty($physician->getSpecialty());
            $entity->setMedicalForm($em->getRepository('AppBundle:MedicalForms')->find(38));
            $entity->setRequired('motivo_por_el_que_de');
            $entity->setPhysician($physician);           
            $em->persist($entity);
            $em->flush();
            //throw $this->createNotFoundException('Unable to find MedicalFormsViews entity.');
        }

        $editForm = $this->createEditMedForm($entity);
        //$deleteForm = $this->createDeleteForm($id);

        $entities = $this->getFieldsFormsAdm($entity->getMedicalForm());
        
        $entity->setFields("'".str_replace(',', "','", $entity->getFields())."'");
        $entity->setFieldsets("'".str_replace(',', "','", $entity->getFieldsets())."'");
        $entity->setRequired("'".str_replace(',', "','", $entity->getRequired())."'");

        return array(
            'entity' => $entity,
            'entityf' => $entity->getMedicalForm(),
            'entities' => $entities,
            'form' => $editForm->createView(),
            'physician'=>$physician,
        );

    }

    /**
    * Creates a form to edit a MedicalFormsViews entity.
    *
    * @param MedicalFormsViews $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditMedForm(MedicalFormsViews $entity)
    {
        $form = $this->createForm(new MedicalFormsViewsUpdateType(), $entity, array(
            'action' => $this->generateUrl('medical_forms_views_update_med', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));
        
        return $form;
    }
    /**
     * Edits an existing MedicalFormsViews entity.
     *
     * @Route("/{id}/med", name="medical_forms_views_update_med")
     * @Method("PUT")
     * @Template("AppBundle:MedicalFormsViews:createViewMed.html.twig")
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function updateMedAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        
        $entity = $em->getRepository('AppBundle:MedicalFormsViews')->find($id);

        if (!$entity) { 
            throw $this->createNotFoundException('Unable to find MedicalFormsViews entity.');              
        }
        
        $form = $this->createEditMedForm($entity);
        $form->handleRequest($request);
   
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            
            if (is_array($request->request->get("fields"))): $entity->setFields(implode(',',$request->request->get("fields")));
            else:  $entity->setFields($request->request->get("fields")); endif;       
            if (is_array($request->request->get("fieldsets"))): $entity->setFieldsets(implode(',',$request->request->get("fieldsets")));
            else:  $entity->setFieldsets($request->request->get("fieldsets")); endif;               
            
            if (is_array($request->request->get("reque"))): $entity->setRequired(implode(',',$request->request->get("reque")));
            else:  $entity->setRequired($request->request->get("reque")); endif; 
            
            $entity->setPhysician($physician);
            $entity->setName("Especialista: ".$physician->getUser()->getName()." ".$physician->getUser()->getLastName());
            $entity->setSpecialty($physician->getSpecialty());
            
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('medical_forms_views_edit_med'));
        }

        $entityf = $em->getRepository('AppBundle:MedicalForms')->find($entity->getMedicalForm()->getId());
        $entities = $this->getFieldsFormsAdm($entityf);

        return array(
            'entity' => $entity,'entityf' => $entityf, 'entities' => $entities, 'form' => $form->createView(),'physician'=>$physician,
        );
    }
    
    
    
    
    /**
     * Creates a new MedicalFormsView entity.
     *
     * @Route("/save/view", name="medicalformsview_create")
     * @Method("POST")
     * @Template("AppBundle:MedicalFormsViews:createView.html.twig")
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function createAction(Request $request) {
        $entity = new MedicalFormsViews();
        $form = $this->createForm(new MedicalFormsViewsType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_create'), 'method' => 'POST',
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();            
            if (is_array($request->request->get("fields"))): $entity->setFields(implode(',',$request->request->get("fields")));
            else:  $entity->setFieldsets($request->request->get("fields")); endif;       
            if (is_array($request->request->get("fieldsets"))): $entity->setFieldsets(implode(',',$request->request->get("fieldsets")));
            else:  $entity->setFieldsets($request->request->get("fieldsets")); endif;    
            $entity->setFormName($entity->getMedicalForm()->getFormName()."_view");
            $em->persist($entity);
            $em->flush();
            
            $connection = $em->getConnection();
            $name = "_mffd_" . $entity->getFormName();

            try {
                $table = new \Doctrine\DBAL\Schema\Table($name);
                $table->addColumn('id', 'bigint', array('autoincrement' => true));
                $table->addColumn('medical_forms_field_name', 'string', array('length' => 42, 'customSchemaOptions' => array('collation' => 'utf8_general_ci')));
                $table->addColumn('value_data', 'text');
                $table->addColumn('fos_user_id', 'bigint');
                $table->addColumn('consultation_id', 'bigint');
                $table->addColumn('date_creation', 'datetime');
                $table->addColumn('key_enc', 'string', array('length' => 32, 'customSchemaOptions' => array('collation' => 'utf8_general_ci')));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('medical_forms_field_name', 'fos_user_id','consultation_id'));
                foreach ($connection->getDatabasePlatform()->getCreateTableSQL($table) AS $sql) {
                    $connection->executeQuery($sql);
                }
            } catch (\Exception $e) {
                //throw $this->createNotFoundException('Unable to find MedicalForms entity. ' . $e);
            }
            
            return $this->redirect($this->generateUrl('medical_forms_views_edit', array('id' => $entity->getId())));
        }

        $entityf = $entity->getMedicalForm();
        $entities = $this->getFieldsFormsAdm($entityf);

        return array(
            'entity' => $entity, 'entityf' => $entityf, 'entities' => $entities, 'form' => $form->createView(),
        );
    }

    /**
     * Finds and displays a MedicalForms entity.
     *
     * @Route("/{id}/create/view", name="medicalformsview_new")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function createViewAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:MedicalForms')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $entities = $this->getFieldsFormsAdm($entity);

        $entityV = new \AppBundle\Entity\MedicalFormsViews();
        $entityV->setMedicalForm($entity);

        $form = $this->createForm(new MedicalFormsViewsType(), $entityV, array(
            'action' => $this->generateUrl('medicalformsview_create'),
            'method' => 'POST',
        ));



//        echo"<pre>";
//        \Doctrine\Common\Util\Debug::dump($entities);
//        echo"</pre>";
//        exit();


        return array(
            'entity' => $entityV,
            'entityf' => $entity,
            'entities' => $entities,
            'form' => $form->createView(),
        );
    }
    
    
    
    
    /**
     * Creates a new MedicalFormsView entity Med.
     *
     * @Route("/save/view/med", name="medicalformsview_create_med")
     * @Method("POST")
     * @Template("AppBundle:MedicalFormsViews:createViewMed.html.twig")
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function createMedAction(Request $request) {
        $entity = new MedicalFormsViews();
        $em = $this->getDoctrine()->getManager();            
        
        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        
        $form = $this->createForm(new MedicalFormsViewsType(), $entity, array(
            'action' => $this->generateUrl('medicalforms_create'), 'method' => 'POST',
        ));
        $form->handleRequest($request);
        if ($form->isValid()) {
            
            if (is_array($request->request->get("fields"))): $entity->setFields(implode(',',$request->request->get("fields")));
            else:  $entity->setFieldsets($request->request->get("fields")); endif;       
            if (is_array($request->request->get("fieldsets"))): $entity->setFieldsets(implode(',',$request->request->get("fieldsets")));
            else:  $entity->setFieldsets($request->request->get("fieldsets")); endif;    
            $entity->setFormName($entity->getMedicalForm()->getFormName()."_view");
            $entity->setPhysician($physician);
            $entity->setName("Especialista: ".$physician->getUser()->getName()." ".$physician->getUser()->getLastName());
            $entity->setSpecialty($physician->getSpecialty());
            $em->persist($entity);
            $em->flush();
            
            /***ESTO DEBERIA IMPLENETARSE PARA GUARDA LOS DATOS ¿QUÉ ES MÁS COSTOSO?
            $connection = $em->getConnection();
            $name = "_mffd_" . $entity->getFormName()."_".$physician->getId();

            try {
                $table = new \Doctrine\DBAL\Schema\Table($name);
                $table->addColumn('id', 'bigint', array('autoincrement' => true));
                $table->addColumn('medical_forms_field_name', 'string', array('length' => 42, 'customSchemaOptions' => array('collation' => 'utf8_general_ci')));
                $table->addColumn('value_data', 'text');
                $table->addColumn('fos_user_id', 'bigint');
                $table->addColumn('consultation_id', 'bigint');
                $table->addColumn('date_creation', 'datetime');
                $table->addColumn('key_enc', 'string', array('length' => 32, 'customSchemaOptions' => array('collation' => 'utf8_general_ci')));
                $table->setPrimaryKey(array('id'));
                $table->addUniqueIndex(array('medical_forms_field_name', 'fos_user_id','consultation_id'));
                foreach ($connection->getDatabasePlatform()->getCreateTableSQL($table) AS $sql) {
                    $connection->executeQuery($sql);
                }
            } catch (\Exception $e) {
                //throw $this->createNotFoundException('Unable to find MedicalForms entity. ' . $e);
            }
            ***/
            
            return $this->redirect($this->generateUrl('medical_forms_views_update_med', array('id' => $entity->getId())));
        }

        $entityf = $entity->getMedicalForm();
        $entities = $this->getFieldsFormsAdm($entityf);

        return array(
            'entity' => $entity, 'entityf' => $entityf, 'entities' => $entities, 'form' => $form->createView(),'physician'=>$physician,
        );
    }

    /**
     * Finds and displays a MedicalForms entity Med.
     *
     * @Route("/{id}/create/view/med", name="medicalformsview_new_med")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_PHYSICIANS')") 
     */
    public function createViewMedAction($id) {
        $em = $this->getDoctrine()->getManager();

        $user = $this->get('security.context')->getToken()->getUser();
        $physician = $em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user));
        
        
        $entity = $em->getRepository('AppBundle:MedicalForms')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find MedicalForms entity.');
        }

        $entities = $this->getFieldsFormsAdm($entity);

        $entityV = new \AppBundle\Entity\MedicalFormsViews();
        $entityV->setMedicalForm($entity);

        $form = $this->createForm(new MedicalFormsViewsType(), $entityV, array(
            'action' => $this->generateUrl('medicalformsview_create_med'),
            'method' => 'POST',
        ));


        return array(
            'entity' => $entityV,
            'entityf' => $entity,
            'entities' => $entities,
            'form' => $form->createView(),
            'physician'=>$physician,
        );
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
            $query = $em->createNativeQuery(""
                    //. "SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid) F2 ON F2.subgroup=F3.subgroup WHERE F3.medical_forms_fieldset_id=:id ORDER BY oi ASC,  og  ASC, orderid ASC "
                    . "CALL GetFieldsByFieldset(:id)", $rsm);
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
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory)) as $file) {
            $size+=$file->getSize();
        }
        return $size;
    }

    function clDir($files, $dir) {
        if (is_dir($dir)):
            $iterator = new \DirectoryIterator($dir);
            foreach ($iterator as $fileinfo) {
                if ($fileinfo->isFile()) {
                    if ($this->ras($fileinfo->getFilename(), $files) === false):
                        unlink($fileinfo->getPathname());
                    endif;
                }
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

}