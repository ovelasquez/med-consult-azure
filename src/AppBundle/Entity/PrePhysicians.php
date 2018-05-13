<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * PrePhysicians
 */
class PrePhysicians {

    /**
     * @var string
     */
    private $firtsName;

    /**
     * @var string
     */
    private $middleName;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $abms;

    /**
     * @var string
     */
    private $practiceType;

    /**
     * @var string
     */
    private $postalCode;

    /**
     * @var string
     */
    private $phone;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $hearAboutUs;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var string
     */
    private $confirmationToken;

    /**
     * @var boolean
     */
    private $status;

    /**
     * @var integer
     *
     * @ORM\Column(name="physicians_id", type="integer", nullable=true)
     */
    private $physician;

    /**
     * @var integer
     */
    private $id;

    /**
     * @Recaptcha\IsTrue
     */
    public $recaptcha;

    /**
     * Set firtsName
     *
     * @param string $firtsName
     *
     * @return PrePhysicians
     */
    public function setFirtsName($firtsName) {
        $this->firtsName = $firtsName;

        return $this;
    }

    /**
     * Get firtsName
     *
     * @return string
     */
    public function getFirtsName() {
        return $this->firtsName;
    }

    /**
     * Set middleName
     *
     * @param string $middleName
     *
     * @return PrePhysicians
     */
    public function setMiddleName($middleName) {
        $this->middleName = $middleName;

        return $this;
    }

    /**
     * Get middleName
     *
     * @return string
     */
    public function getMiddleName() {
        return $this->middleName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return PrePhysicians
     */
    public function setLastName($lastName) {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName() {
        return $this->lastName;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getName() {
        return $this->firtsName . ' ' . $this->middleName . '. ' . $this->lastName;
    }

    /**
     * Set abms
     *
     * @param string $abms
     *
     * @return PrePhysicians
     */
    public function setAbms($abms) {
        $this->abms = $abms;

        return $this;
    }

    /**
     * Get abms
     *
     * @return string
     */
    public function getAbms() {
        return $this->abms;
    }

    /**
     * Set practiceType
     *
     * @param string $practiceType
     *
     * @return PrePhysicians
     */
    public function setPracticeType($practiceType) {
        $this->practiceType = $practiceType;

        return $this;
    }

    /**
     * Get practiceType
     *
     * @return string
     */
    public function getPracticeType() {
        return $this->practiceType;
    }

    /**
     * Set postalCode
     *
     * @param string $postalCode
     *
     * @return PrePhysicians
     */
    public function setPostalCode($postalCode) {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * Get postalCode
     *
     * @return string
     */
    public function getPostalCode() {
        return $this->postalCode;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return PrePhysicians
     */
    public function setPhone($phone) {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone() {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return PrePhysicians
     */
    public function setEmail($email) {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set hearAboutUs
     *
     * @param string $hearAboutUs
     *
     * @return PrePhysicians
     */
    public function setHearAboutUs($hearAboutUs) {
        $this->hearAboutUs = $hearAboutUs;

        return $this;
    }

    /**
     * Get hearAboutUs
     *
     * @return string
     */
    public function getHearAboutUs() {
        return $this->hearAboutUs;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return PrePhysicians
     */
    public function setDateTime($dateTime) {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime() {
        return $this->dateTime;
    }

    /**
     * Set confirmationToken
     *
     * @param string $confirmationToken
     *
     * @return PrePhysicians
     */
    public function setConfirmationToken($confirmationToken) {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * Get confirmationToken
     *
     * @return string
     */
    public function getConfirmationToken() {
        return $this->confirmationToken;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return PrePhysicians
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set physician
     *
     * @param \AppBundle\Entity\Physicians $physician
     *
     * @return PrePhysicians
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
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

}
