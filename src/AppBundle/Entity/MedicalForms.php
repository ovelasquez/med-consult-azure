<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Entity\MedicalFormsFieldsets;


/**
 * MedicalForms
 *
 * @ORM\Table(name="medical_forms", indexes={@ORM\Index(name="specialtie_id", columns={"specialtie_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Entity\MedicalFormsRepository")
 * @UniqueEntity("formName",message="El nombre de sistema ya esta en uso.")
 */
class MedicalForms
{
    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="form_name", type="string", length=20, nullable=false,unique=true)
     */
    private $formName;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\Specialties
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Specialties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="specialtie_id", referencedColumnName="id")
     * })
     */
    private $specialtie;
    
    
    private $fieldsets;


    /**
     * Set name
     *
     * @param string $name
     *
     * @return MedicalForms
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
     * @return MedicalForms
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set specialtie
     *
     * @param \AppBundle\Entity\Specialties $specialtie
     *
     * @return MedicalForms
     */
    public function setSpecialtie(\AppBundle\Entity\Specialties $specialtie = null)
    {
        $this->specialtie = $specialtie;

        return $this;
    }

    /**
     * Get specialtie
     *
     * @return \AppBundle\Entity\Specialties
     */
    public function getSpecialtie()
    {
        return $this->specialtie;
    }
    
    /**
     * Set fieldsets
     *
     * @param \AppBundle\Entity\MedicalFormsFieldsets $fieldsets
     *
     * @return MedicalForms
     */
    public function setFieldsets(\AppBundle\Entity\MedicalFormsFieldsets $fieldsets = null)
    {
        $this->fieldsets = $fieldsets;

        return $this;
    }

    /**
     * Get fieldsets
     *
     * @return \AppBundle\Entity\MedicalFormsFieldsets
     */
    public function getFieldsets()
    {
        return $this->fieldsets;
    }
}
