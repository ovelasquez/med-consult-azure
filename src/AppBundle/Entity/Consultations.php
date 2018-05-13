<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Consultations
 *@ORM\Entity(repositoryClass="AppBundle\Entity\ConsultationsRepository")
 * @ORM\Table(name="consultations")
 * 
 */
class Consultations {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     * @var string
     *
     * @ORM\Column(name="question", type="text", length=200, nullable=false)
     * 
     * @Assert\NotBlank()
     */
    private $question;
    
    /**
     * @var string
     *
     * @ORM\Column(name="resume", type="text", nullable=true)
     * 
     */
    private $resume;
    

    /**
     * @var \AppBundle\Entity\ModalityConsultations
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ModalityConsultations")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medical_form_id", referencedColumnName="id", nullable=false)
     * })
     *
     * @Assert\NotBlank()
     *  
     */
    private $modalityConsultation;

    
    /**
     * @var \AppBundle\Entity\Specialties
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Specialties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="specialtie_id", referencedColumnName="id", nullable=false)
     * })
     * 
     * @Assert\NotBlank()
     */
    private $specialty;
    
    
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
     * @var integer
     *
     */
    private $status;
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="creation_date", type="datetime", nullable=false)
     */
    private $creationDate;  
    
    /**
     * @var \Date
     *
     * @ORM\Column(name="update_date", type="datetime", nullable=false)
     */
    private $updateDate;  
    
    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text", length=65535, nullable=true)
     */
    private $answer;

    /**
     * @var string
     *
     * @ORM\Column(name="tlf", type="string", length=14, nullable=true)
     */
    private $tlf;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set question
     *
     * @param string $question
     *
     * @return Consultations
     */
    public function setQuestion($question)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }
    
    /**
     * Set resume
     *
     * @param string $resume
     *
     * @return Consultations
     */
    public function setResume($resume)
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * Get resume
     *
     * @return string
     */
    public function getResume()
    {
        return $this->resume;
    }
    
    /**
     * Set specialty
     *
     * @param \AppBundle\Entity\Specialties $specialty
     *
     * @return Consultations
     */
    public function setSpecialty(\AppBundle\Entity\Specialties $specialty = null) {
        $this->specialty = $specialty;

        return $this;
    }

    /**
     * Get specialty
     *
     * @return \AppBundle\Entity\Specialties
     */
    public function getSpecialty() {
        return $this->specialty;
    }

    /**
     * Set modalityConsultation
     *
     * @param \AppBundle\Entity\ModalityConsultations $modalityConsultation
     *
     * @return Consultations
     */
    public function setModalityConsultation(\AppBundle\Entity\ModalityConsultations $modalityConsultation = null) {
        $this->modalityConsultation = $modalityConsultation;

        return $this;
    }

    /**
     * Get modalityConsultation
     *
     * @return \AppBundle\Entity\ModalityConsultations
     */
    public function getModalityConsultation() {
        return $this->modalityConsultation;
    }
    
    /**
     * Set physician
     *
     * @param \AppBundle\Entity\Physicians $physician
     *
     * @return Consultations
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
     * @return Consultations
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
     * @return Consultations
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
     * Set creationDate
     *
     * @param \DateTime $creationDate
     *
     * @return Consultations
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }
    
    /**
     * Set updateDate
     *
     * @param \DateTime $updateDate
     *
     * @return Consultations
     */
    public function setUpdateDate($updateDate)
    {
        $this->updateDate = $updateDate;

        return $this;
    }

    /**
     * Get updateDate
     *
     * @return \DateTime
     */
    public function getUpdateDate()
    {
        return $this->updateDate;
    }
    
    /**
     * Set answer
     *
     * @param string $answer
     *
     * @return Contents
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * Set tlf
     *
     * @param string $tlf
     *
     * @return Contents
     */
    public function setTlf($tlf)
    {
        $this->tlf = $tlf;

        return $this;
    }

    /**
     * Get tlf
     * @return string
     */
    public function getTlf()
    {
        return $this->tlf;
    }
    
  

    public function __toString()
    {
        return strval( $this->getId() );
    }
}
