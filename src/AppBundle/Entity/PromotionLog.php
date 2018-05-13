<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\Patients;
use AppBundle\Entity\Consultations;
use AppBundle\Entity\Promotion;

/**
 * Promotion
 *
 * @ORM\Table(name="promotion_log")
 * @ORM\Entity
 * 
 */
class PromotionLog {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Patients",cascade={"persist"})
     * @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     * 
     */
    protected $patient;

    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Consultations",cascade={"persist"})
     * @ORM\JoinColumn(name="consultation_id", referencedColumnName="id")
     * 
     */
    protected $consultation;
    
    /**
     * @ORM\ManyToOne(targetEntity="\AppBundle\Entity\Promotion",cascade={"persist"})
     * @ORM\JoinColumn(name="promotion_id", referencedColumnName="id")
     * 
     */
    protected $promotion;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="date_time_creation", type="datetime", nullable=true)
     */
    private $dateTimeCreation; 
        
    /**
     * Set patient
     *
     * @param \AppBundle\Entity\Patients $patient
     * @return PromotionLog
     */
    public function setPatient(Patients  $patient = null) {
        $this->patient = $patient;

        return $this;
    }

    /**
     * Get patient
     *
     * @return \AppBundle\Entity\Patients
     */
    public function getPatient() {
        return $this->patient;
    }
    
    /**
     * Set consultation
     *
     * @param \AppBundle\Entity\Consultations $consultation
     * @return PromotionLog
     */
    public function setConsultation(Consultations $consultation = null) {
        $this->consultation = $consultation;

        return $this;
    }

    /**
     * Get consultation
     *
     * @return \AppBundle\Entity\Consultations
     */
    public function getConsultation() {
        return $this->consultation;
    }
    
    
    /**
     * Set promotion
     *
     * @param \AppBundle\Entity\Promotion $promotion
     * @return PromotionLog
     */
    public function setPromotion(Promotion $promotion = null) {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return \AppBundle\Entity\Promotion
     */
    public function getPromotion() {
        return $this->promotion;
    }
    
    /**
     * Set dateTimeCreation
     *
     * @param \DateTime $dateTimeCreation
     *
     * @return PromotionLog
     */
    public function setDateTimeCreation($dateTimeCreation)
    {
        $this->dateTimeCreation = $dateTimeCreation;

        return $this;
    }

    /**
     * Get dateTimeCreation
     *
     * @return \DateTime
     */
    public function getDateTimeCreation()
    {
        return $this->dateTimeCreation;
    }
        
    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

}
