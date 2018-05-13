<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Physicians;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PhysiciansRepository
 *
 * @author Mariana
 */
class PhysiciansRepository extends EntityRepository {

    //put your code here

    public function findAllByEnabled() {

        return $this->getEntityManager()
                ->createQuery('SELECT u.name as name,u.lastName as lastname,p.abms,p.education,p.id,p.jobtitle,p.languages,p.phone,p.photo,p.postalcode,p.research,e.name as specialty,p.subspecialty,p.university,e.id as specialty_id FROM AppBundle:Physicians p LEFT JOIN p.user u LEFT JOIN p.specialty e WHERE u.enabled=1 ORDER BY u.name ASC')
                ->getResult();
    }

    public function findAllBySpecialty($sp) {
        
        $query = $this->getEntityManager()
                ->createQuery('SELECT  u.name as name,u.lastName as lastname,p.abms,p.education,p.id,p.jobtitle,p.languages,p.phone,p.photo,p.postalcode,p.research,e.name as specialty,p.subspecialty,p.university,e.id as specialty_id FROM AppBundle:Physicians p LEFT JOIN p.user u LEFT JOIN p.specialty e WHERE u.enabled=1 AND  p.specialty=:sp ORDER BY u.name ASC');
        $query->setParameter('sp', $sp);
        $entities = $query->getResult();

        return $entities;
    }

}
