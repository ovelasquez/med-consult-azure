<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Utils\BaseFields;

/**
 * MedicalFormsFields
 *
 * @ORM\Table(name="patients_share_medical_history", indexes={@ORM\Index(name="patients_id", columns={"patients_id"})})
 * @ORM\Entity
 */
class PatientsShareMedicalHistory {

    
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", nullable=false)
     */
    private $message;
    
    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=10, nullable=true)
     */
    private $token;

    
    /**
     * @var integer
     *
     * @ORM\Column(name="available", type="integer", nullable=false)
     */
    private $available;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateTime", type="datetime", nullable=true)
     */
    private $dateTime;
    
    /**
     * @var \AppBundle\Entity\Patients
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Patients")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     * })
     */
    private $patient;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return PatientsShareMedicalHistory
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return PatientsShareMedicalHistory
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }
    
    /**
     * Set message
     *
     * @param string $message
     *
     * @return PatientsShareMedicalHistory
     */
    public function setMessage($message) {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage() {
        return $this->message;
    }
    
    /**
     * Set available
     *
     * @param integer $available
     *
     * @return PatientsShareMedicalHistory
     */
    public function setAvailable($available) {
        $this->available = $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return integer
     */
    public function getAvailable() {
        return $this->available;
    }
    
    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return PatientsShareMedicalHistory
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }
    
    /**
     * Set token
     *
     * @param string $token
     *
     * @return PatientsShareMedicalHistory
     */
    public function setToken($token) {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken() {
        return $this->token;
    }
    
    /**
     * Set patient
     *
     * @param \AppBundle\Entity\MedicalFormsFieldsets $patient
     *
     * @return PatientsShareMedicalHistory
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    

}
