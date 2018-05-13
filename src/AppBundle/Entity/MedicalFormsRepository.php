<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of MedicalFormsRepository
 *
 * @author Mariana
 */
class MedicalFormsRepository extends EntityRepository {

    //put your code here

    public function findFieldsForms($par) {

        $em =  $this->getEntityManager();
        $entity = $em->getRepository('AppBundle:MedicalForms')->findOneBy($par);
        
        $user = null;
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            $user = $this->get('security.context')->getToken()->getUser();
        }else{
            return array();
        }

        $entities = $em->getRepository('AppBundle:MedicalFormsFieldsets')->findBy(array("medicalForm" => $entity->getId()), array("position" => "ASC"));
        $entitiesAll = array();
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
                $query = $em->createNativeQuery("SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og, FV.value_data as value_temp, FV.key_enc as key_enc FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid ) F2 ON F2.subgroup=F3.subgroup LEFT JOIN _mffd_" . $entity->getFormName() . " FV ON FV.medical_forms_field_name=F3.name AND FV.fos_user_id=:idu  WHERE F3.medical_forms_fieldset_id=:id ORDER BY oi ASC,  og  ASC, orderid ASC ", $rsm);
                $query->setParameter('idu', $user->getId());
            else:
                $query = $em->createNativeQuery("SELECT F3.*,(CASE WHEN oi IS NULL THEN F3.orderid ELSE oi END) as oi ,(CASE F3.id WHEN F2.id THEN 1 ELSE 2 END) AS og FROM medical_forms_fields F3 LEFT JOIN( SELECT F1.subgroup, F1.orderid as oi, F1.id FROM medical_forms_fields F1 WHERE F1.field='group' order by F1.orderid) F2 ON F2.subgroup=F3.subgroup WHERE F3.medical_forms_fieldset_id=:id ORDER BY oi ASC,  og  ASC, orderid ASC ", $rsm);
            endif;
            $query->setParameter('id', $entityFs->getId());
            $entitiesFl = $query->getResult();
            $entityset->fields = $entitiesFl;
            array_push($entitiesAll, $entityset);
        endforeach;
        return $entitiesAll;
    }

}