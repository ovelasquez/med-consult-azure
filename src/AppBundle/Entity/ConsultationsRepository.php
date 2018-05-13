<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Consultations;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ConsultationsRepository
 *
 * @author Mariana
 */
class ConsultationsRepository extends EntityRepository {

    //put your code here

    public function findAllWithCalendar($ph,$limit=100) {

        $query = $this->getEntityManager()
                ->createQuery("SELECT  cs, c FROM AppBundle:Consultations cs LEFT JOIN AppBundle:Calendar c WITH cs.id=c.consultation  WHERE cs.patient=:pt and cs.status > -2 ORDER BY cs.creationDate DESC")
                ->setFirstResult(0)
                ->setMaxResults($limit);
        $query->setParameter('pt', $ph);
        $entities = $query->getResult();

        //dump($entities); die();
        
        return $entities;
    }

     public function findAllPhysicianWithCalendar($ph,$limit=100) {

        $query = $this->getEntityManager()
                ->createQuery("SELECT  cs, c FROM AppBundle:Consultations cs LEFT JOIN AppBundle:Calendar c WITH cs.id=c.consultation  WHERE cs.physician=:pt ORDER BY cs.creationDate DESC")
                  ->setFirstResult(0)
                  ->setMaxResults($limit);
        $query->setParameter('pt', $ph);
        $entities = $query->getResult();

        return $entities;
    }


	public function findAllPhysicianCalenFiltro($ph , $filtro) {

        $query = $this->getEntityManager()
                ->createQuery("SELECT  cs, c FROM AppBundle:Consultations cs LEFT JOIN AppBundle:Calendar c WITH cs.id=c.consultation  WHERE cs.physician=:pt and cs.modalityConsultation=:type ORDER BY cs.creationDate DESC");
        $query->setParameter('pt', $ph);
        $query->setParameter('type', $filtro);
        $entities = $query->getResult();

        return $entities;
    }

    public function findAllByPhysicianPending($ph) {

        $query = $this->getEntityManager()
                ->createQuery("SELECT  cs FROM AppBundle:Consultations  cs LEFT JOIN AppBundle:Calendar c WITH cs.id=c.consultation WHERE cs.physician=:ph and (cs.status=1 or cs.status=0 ) and (c.datetimeConsultation>=:n or c.id is null)
                     ORDER BY cs.creationDate DESC ");
                
        $query->setParameter('ph', $ph);
        $now = new \DateTime("now");
        $query->setParameter('n', $now->format("Y-m-d"));
        $entities = $query->getResult();

        return $entities;
    }

    public function findAllByPatientConsice($ph) {

        $query = $this->getEntityManager()
                ->createQuery("SELECT  cs FROM AppBundle:Consultations cs LEFT JOIN AppBundle:ModalityConsultations m WITH cs.modalityConsultation=m.id WHERE cs.patient=:ph and m.tag='consice'");
                //->createQuery("SELECT  cs FROM AppBundle:Consultations cs LEFT JOIN AppBundle:ModalityConsultations m WITH cs.modalityConsultation=m.id WHERE cs.patient=:ph and cs.status=1 and m.tag='consice'");
        $query->setParameter('ph', $ph);
        $entities = $query->getResult();

        return count($entities);
    }

}
