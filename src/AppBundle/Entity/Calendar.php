<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Calendar
 * @ORM\Entity(repositoryClass="AppBundle\Entity\CalendarRepository")
 * @ORM\Table(name="calendar")
 * 
 */
class Calendar {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="datetimeConsultation", type="datetime", nullable=false)
     */
    private $datetimeConsultation;

    /**
     * @var \Date
     *
     * @ORM\Column(name="datetimePatient", type="datetime", nullable=false)
     */
    private $datetimePatient;    
    
    /**
     * @var \AppBundle\Entity\Physicians
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Physicians")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="physician_id", referencedColumnName="id", nullable=false)
     * })
     *
     * @Assert\NotBlank()
     */
    private $physician;
    
    /**
     * @var \AppBundle\Entity\Patients
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Patients")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_id", referencedColumnName="id", nullable=false)
     * })
     * 
     * @Assert\NotBlank()
     */
    private $patient;
    
    /**
     * @var \AppBundle\Entity\Consultations
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Consultations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="consultation_id", referencedColumnName="id", nullable=false)
     * })
     * 
     * @Assert\NotBlank()
     */
    private $consultation;
    
    /**
     * @var string
     *
     */
    private $timezonePhysician;
    
    /**
     * @var string
     *
     */
    private $timezonePatient;
    
    
    /**
     * @var integer
     *
     */
    private $status;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set datetimeConsultation
     *
     * @param \DateTime $datetimeConsultation
     *
     * @return Calendar
     */
    public function setDatetimeConsultation($datetimeConsultation)
    {
        $this->datetimeConsultation = $datetimeConsultation;

        return $this;
    }

    /**
     * Get datetimeConsultation
     *
     * @return \DateTime
     */
    public function getDatetimeConsultation()
    {
        return $this->datetimeConsultation;
    }
    
    /**
     * Set datetimePatient
     *
     * @param \DateTime $datetimePatient
     *
     * @return Calendar
     */
    public function setDatetimePatient($datetimePatient)
    {
        $this->datetimePatient = $datetimePatient;

        return $this;
    }

    /**
     * Get datetimePatient
     *
     * @return \DateTime
     */
    public function getDatetimePatient()
    {
        return $this->datetimePatient;
    }
    
    
    
    /**
     * Set physician
     *
     * @param \AppBundle\Entity\Physicians $physician
     *
     * @return Calendar
     */
    public function setPhysician(\AppBundle\Entity\Physicians $physician = null) {
        $this->physician = $physician;

        return $this;
    }

    /**
     * Get physician
     *
     * @return \AppBundle\Entity\Physicians
     */
    public function getPhysician() {
        return $this->physician;
    }
    
    /**
     * Set patient
     *
     * @param \AppBundle\Entity\Patients $patient
     *
     * @return Calendar
     */
    public function setPatient(\AppBundle\Entity\Patients $patient = null) {
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
     * Set status
     *
     * @param integer $status
     *
     * @return Calendar
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }
    
    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus() {
        return $this->status;
    }
    
    /**
     * Set timezonePhysician
     *
     * @param integer $timezonePhysician
     *
     * @return Calendar
     */
    public function setTimezonePhysician($timezonePhysician) {
        $this->timezonePhysician = $timezonePhysician;

        return $this;
    }
    
    /**
     * Get timezonePhysician
     *
     * @return string
     */
    public function getTimezonePhysician() {
        return $this->timezonePhysician;
    }
    
    
    /**
     * Set timezonePatient
     *
     * @param integer $timezonePatient
     *
     * @return Calendar
     */
    public function setTimezonePatient($timezonePatient) {
        $this->timezonePatient = $timezonePatient;

        return $this;
    }
    
    /**
     * Get timezonePatient
     *
     * @return string
     */
    public function getTimezonePatient() {
        return $this->timezonePatient;
    }
    
    
    /**
     * Set consultation
     *
     * @param \AppBundle\Entity\Consultations $consultation
     *
     * @return Calendar
     */
    public function setConsultation(\AppBundle\Entity\Consultations $consultation = null) {
        $this->consultation = $consultation;

        return $this;
    }

    /**
     * Get physician
     *
     * @return \AppBundle\Entity\Consultations
     */
    public function getConsultation() {
        return $this->consultation;
    }
    

}
