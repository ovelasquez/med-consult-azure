<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedicalFormsViews
 *
 * @ORM\Table(name="medical_forms_views")
 * 
 */
class MedicalFormsViews {

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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="form_name", type="string", nullable=true)
     */
    private $formName;

    /**
     * @var \AppBundle\Entity\MedicalForms
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MedicalForms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medical_form_id", referencedColumnName="id")
     * })
     */
    private $medicalForm;

    /**
     * @var string
     *
     */
    private $fieldsets;

    /**
     * @var string
     *
     */
    private $fields;
    
    /**
     * @var string
     *
     */
    private $required;

    /**
     * @var \AppBundle\Entity\Specialties
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Specialties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="specialtie_id", referencedColumnName="id")
     * })
     */
    private $specialty;


    /**
     * @var \AppBundle\Entity\Physicians
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Physicians")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="physician_id", referencedColumnName="id")
     * })
     */
    private $physician;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return MedicalFormsViews
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set formName
     *
     * @param string $formName
     *
     * @return MedicalFormsViews
     */
    public function setFormName($formName)
    {
        $this->formName = $formName;

        return $this;
    }

    /**
     * Get formName
     *
     * @return string
     */
    public function getFormName()
    {
        return $this->formName;
    }
    
    /**
     * Set required
     *
     * @param string $required
     *
     * @return MedicalFormsViews
     */
    public function setRequired($required) {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return string
     */
    public function getRequired() {
        return $this->required;
    }
    
    /**
     * Set fields
     *
     * @param string $fields
     *
     * @return MedicalFormsViews
     */
    public function setFields($fields) {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Get fields
     *
     * @return string
     */
    public function getFields() {
        return $this->fields;
    }
    
    
    /**
     * Set fieldsets
     *
     * @param string $fieldsets
     *
     * @return MedicalFormsViews
     */
    public function setFieldsets($fieldsets) {
        $this->fieldsets = $fieldsets;

        return $this;
    }

    /**
     * Get fieldsets
     *
     * @return string
     */
    public function getFieldsets() {
        return $this->fieldsets;
    }
        
    /**
     * Set specialty
     *
     * @param \AppBundle\Entity\Specialties $specialty
     *
     * @return MedicalFormsViews
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
     * Set physician
     *
     * @param \AppBundle\Entity\Physicians $physician
     *
     * @return MedicalFormsViews
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
     * Set medicalForm
     *
     * @param \AppBundle\Entity\MedicalForms $medicalForm
     *
     * @return MedicalFormsViews
     */
    public function setMedicalForm(\AppBundle\Entity\MedicalForms $medicalForm = null) {
        $this->medicalForm = $medicalForm;

        return $this;
    }

    /**
     * Get medicalForm
     *
     * @return \AppBundle\Entity\MedicalForms
     */
    public function getMedicalForm() {
        return $this->medicalForm;
    }

}