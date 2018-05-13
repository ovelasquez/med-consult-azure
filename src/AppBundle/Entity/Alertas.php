<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alertas
 *
 * @ORM\Entity
 * @ORM\Table(name="alertas")
 */
class Alertas
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="tipo", type="string", length=255)
     */
    private $tipo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetimetz")
     */
    private $createdate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="desat_date", type="datetimetz", nullable=true)
     */
    private $desatdate;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=255)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity="\AppUserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * 
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Consultations")
     * @ORM\JoinColumn(name="consultation", referencedColumnName="id")
     *
     */
    private $consultation;


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
     * Set tipo
     *
     * @param string $tipo
     *
     * @return Alertas
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;

        return $this;
    }

    /**
     * Get tipo
     *
     * @return string
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * Set createdate
     *
     * @param \DateTime $createdate
     *
     * @return Alertas
     */
    public function setCreatedate($createdate)
    {
        $this->createdate = $createdate;

        return $this;
    }

    /**
     * Get createdate
     *
     * @return \DateTime
     */
    public function getCreatedate()
    {
        return $this->createdate;
    }

    /**
     * Set desatdate
     *
     * @param \DateTime $desatdate
     *
     * @return Alertas
     */
    public function setDesatdate($desatdate)
    {
        $this->desatdate = $desatdate;

        return $this;
    }

    /**
     * Get desatdate
     *
     * @return \DateTime
     */
    public function getDesatdate()
    {
        return $this->desatdate;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return Alertas
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }
    /**
     * @var \DateTime
     */
    private $desactdate;


    /**
     * Set desactdate
     *
     * @param \DateTime $desactdate
     *
     * @return Alertas
     */
    public function setDesactdate($desactdate)
    {
        $this->desactdate = $desactdate;

        return $this;
    }

    /**
     * Get desactdate
     *
     * @return \DateTime
     */
    public function getDesactdate()
    {
        return $this->desactdate;
    }

    /**
     * Set user
     *
     * @param \AppUserBundle\Entity\User $user
     *
     * @return Alertas
     */
    public function setUser(\AppUserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppUserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set consultation
     *
     * @param \AppBundle\Entity\Consultations $consultation
     *
     * @return Alertas
     */
    public function setConsultation(\AppBundle\Entity\Consultations $consultation = null)
    {
        $this->consultation = $consultation;

        return $this;
    }

    /**
     * Get consultation
     *
     * @return \AppBundle\Entity\Consultations
     */
    public function getConsultation()
    {
        return $this->consultation;
    }
}
