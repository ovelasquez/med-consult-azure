<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Messages
 *
 * @ORM\Table(name="messages")
 * @ORM\Entity
 */
class Messages
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
     * @ORM\ManyToOne(targetEntity="\AppUserBundle\Entity\User",cascade={"persist"})
     * @ORM\JoinColumn(name="from_msg", referencedColumnName="id")
     * 
     */

     private $frommsg;

    /**
     * @ORM\ManyToOne(targetEntity="\AppUserBundle\Entity\User",cascade={"persist"})
     * @ORM\JoinColumn(name="to_msg", referencedColumnName="id")
     * 
     */
    protected $tomsg;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text", length=200, nullable=false)
     * @Assert\Length(
     *      min = "2",
     *      max = "200",
     *      minMessage = "por lo menos debe tener {{ limit }} caracteres de largo",
     *      maxMessage = "no puede tener más de {{ limit }} caracteres de largo"
     * )
     */
    private $message;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="create_date", type="datetime", nullable=false)
     */
    private $createdate;

    /**
     * @ORM\ManyToOne(targetEntity="Consultations")
     * @ORM\JoinColumn(name="consultation", referencedColumnName="id")
     *
     */
    private $consultation;

    /**
     * Set frommsg
     *
     * @param string $frommsg
     *
     * @return Messages
     */
    public function setFrommsg($frommsg)
    {
        $this->frommsg = $frommsg;

        return $this;
    }

    /**
     * Get frommsg
     *
     * @return string
     */
    public function getFrommsg()
    {
        return $this->frommsg;
    }

    /**
     * Set tomsg
     *
     * @param string $tomsg
     *
     * @return Messages
     */
    public function setTomsg($tomsg)
    {
        $this->tomsg = $tomsg;

        return $this;
    }

    /**
     * Get tomsg
     *
     * @return string
     */
    public function getTomsg()
    {
        return $this->tomsg;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Messages
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set createdate
     *
     * @param \DateTime $createdate
     *
     * @return Messages
     */
    public function setCreateDate($createdate)
    {
        $this->createdate = $createdate;

        return $this;
    }

    /**
     * Get createdate
     *
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createdate;
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
     * Set consultation
     *
     * @param \AppBundle\Entity\Consultations $consultation
     *
     * @return Messages
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
