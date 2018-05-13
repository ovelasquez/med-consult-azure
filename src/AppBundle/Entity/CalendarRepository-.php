<?php

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;
use AppBundle\Entity\Calendar;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CalendarRepository
 *
 * @author Mariana
 */
class CalendarRepository extends EntityRepository {

    //put your code here

    public function findAllByDatetimeConsultation($ph,$dat) {
        
        $query = $this->getEntityManager()
                ->createQuery("SELECT  c FROM AppBundle:Calendar c LEFT JOIN c.consultation cs  WHERE (cs.physician=:ph or c.physician=:ph) AND  c.datetimeConsultation >= (:dat)  ORDER BY c.datetimeConsultation ASC");
        $query->setParameter('ph', $ph);
        $query->setParameter('dat', $dat);
        $entities = $query->getResult();

        return $entities;
    }

}
