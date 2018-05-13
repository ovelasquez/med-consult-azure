<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Promotion
 *
 * @ORM\Table(name="promotion")
 * @ORM\Entity
 * 
 */

class Promotion
{
    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255, nullable=false)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="usageAmount", type="bigint", nullable=true)
     */
    private $usageAmount;
    

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Promotion
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
        
    /**
     * Set usageAmount
     *
     * @param integer $usageAmount
     *
     * @return Promotion
     */
    public function setUsageAmount($usageAmount) {
        $this->usageAmount = $usageAmount;
        return $this;
    }

    /**
     * Get orderid
     *
     * @return integer
     */
    public function getUsageAmount() {
        return $this->usageAmount;
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
