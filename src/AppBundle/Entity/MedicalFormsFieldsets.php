<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MedicalFormsFieldsets
 *
 * @ORM\Table(name="medical_forms_fieldsets", indexes={@ORM\Index(name="medical_form_id", columns={"medical_form_id"})})
 * @ORM\Entity
 */
class MedicalFormsFieldsets
{
    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @var integer
     *
     * @ORM\Column(name="position", type="integer", nullable=false)
     */
    private $position;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

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
     * @var integer
     *
     * @ORM\Column(name="page", type="bigint", nullable=true)
     */
    private $page;
    
    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="className", type="string", length=20, nullable=true)
     */
    private $className;
    
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
     * Set label
     *
     * @param string $label
     *
     * @return MedicalFormsFieldsets
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return MedicalFormsFieldsets
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
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

    /**
     * Set medicalForm
     *
     * @param \AppBundle\Entity\MedicalForms $medicalForm
     *
     * @return MedicalFormsFieldsets
     */
    public function setMedicalForm(\AppBundle\Entity\MedicalForms $medicalForm = null)
    {
        $this->medicalForm = $medicalForm;

        return $this;
    }

    /**
     * Get medicalForm
     *
     * @return \AppBundle\Entity\MedicalForms
     */
    public function getMedicalForm()
    {
        return $this->medicalForm;
    }
    
    /**
     * Set page
     *
     * @param \AppBundle\Entity\MedicalFormsFieldsets $page
     *
     * @return MedicalFormsFieldsets
     */
    public function setPage(\AppBundle\Entity\MedicalFormsFieldsets $page=null)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Get page
     *
     * @return \AppBundle\Entity\MedicalFormsFieldsets
     */
    public function getPage()
    {
        return $this->page;
    }
    
    /**
     * Set type
     *
     * @param string $type
     *
     * @return MedicalFormsFieldsets
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    
    /**
     * Set className
     *
     * @param string $className
     *
     * @return MedicalFormsFieldsets
     */
    public function setClassName($className)
    {
        $this->className = $className;

        return $this;
    }

    /**
     * Get className
     *
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
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

}