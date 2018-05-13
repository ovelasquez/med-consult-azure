<?php
// src/AppUserBundle/Entity/User.php

namespace AppUserBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * 
 */
class User extends BaseUser

{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     * @Assert\NotBlank(message="Please enter your name.", groups={"Registration", "Profile"})
     * @Assert\Length(
     *     min=3,
     *     max=255,
     *     minMessage="The name is too short.",
     *     maxMessage="The name is too long.",
     *     groups={"Registration", "Profile"}
     * )
     */
    private $name;
    
    /** 
     * @ORM\Column(name="last_name", type="string", length=255, nullable=false) 
     */
    protected $lastName;
    
    /**
     * @ORM\ManyToMany(targetEntity="AppUserBundle\Entity\Group")
     * @ORM\JoinTable(name="fos_user_user_group",
     *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
     * )
     */
    protected $groups;
    
    /** 
     * @ORM\Column(name="facebook_id", type="string", length=255, nullable=true) 
     */
    protected $facebook_id;
    
    /** 
     * @ORM\Column(name="facebook_access_token", type="string", length=255, nullable=true) 
     */
    protected $facebook_access_token;
    
    
    
    /**
     * Set name
     *
     * @param string $name
     *
     * @return Contents
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
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Contents
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }
    
    
    /**
     * Set facebook_id
     *
     * @param string $facebook_id
     *
     * @return Contents
     */
    public function setFacebookId($facebook_id)
    {
        $this->facebook_id = $facebook_id;

        return $this;
    }

    /**
     * Get facebook_id
     *
     * @return string
     */
    public function getFacebookId()
    {
        return $this->facebook_id;
    }
    
    /**
     * Set facebook_access_token
     *
     * @param string $facebook_access_token
     *
     * @return Contents
     */
    public function setFacebookAccessToken($facebook_access_token)
    {
        $this->facebook_access_token = $facebook_access_token;

        return $this;
    }

    /**
     * Get facebook_access_token
     *
     * @return string
     */
    public function getFacebookAccessToken()
    {
        return $this->facebook_access_token;
    }
    
    
    
    public function setRoles(array $roles)
    {
        $this->roles = array();

        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }
    
    public function getRoles()
    {
        return $this->roles;
    }
    
    
    /**
     * {@inheritdoc}
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return $this->salt;
    }

    public function __construct()
    {        
        parent::__construct();
        parent::setSalt(base_convert(sha1(uniqid(mt_rand(), true)), 16, 36));
        
        
    }
    
    public function __toString()
    {
        return (string) $this->getName();
    }
}