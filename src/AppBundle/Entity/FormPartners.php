<?php

namespace AppBundle\Entity;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * FormPartners
 */
class FormPartners {

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $nameContact;

    /**
     * @var string
     */
    private $typeCompany;

    /**
     * @var string
     */
    private $address;

    /**
     * @var string
     */
    private $phoneNumbers;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $hearAboutUs;

    /**
     * @var string
     */
    private $message;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var integer
     */
    private $id;

    /**
     * @Recaptcha\IsTrue
     */
    public $recaptcha;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return FormPartners
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
     * Set nameContact
     *
     * @param string $nameContact
     *
     * @return FormPartners
     */
    public function setNameContact($nameContact) {
        $this->nameContact = $nameContact;

        return $this;
    }

    /**
     * Get nameContact
     *
     * @return string
     */
    public function getNameContact() {
        return $this->nameContact;
    }

    /**
     * Set typeCompany
     *
     * @param string $typeCompany
     *
     * @return FormPartners
     */
    public function setTypeCompany($typeCompany) {
        $this->typeCompany = $typeCompany;

        return $this;
    }

    /**
     * Get typeCompany
     *
     * @return string
     */
    public function getTypeCompany() {
        return $this->typeCompany;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return FormPartners
     */
    public function setAddress($address) {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress() {
        return $this->address;
    }

    /**
     * Set phoneNumbers
     *
     * @param string $phoneNumbers
     *
     * @return FormPartners
     */
    public function setPhoneNumbers($phoneNumbers) {
        $this->phoneNumbers = $phoneNumbers;

        return $this;
    }

    /**
     * Get phoneNumbers
     *
     * @return string
     */
    public function getPhoneNumbers() {
        return $this->phoneNumbers;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return FormPartners
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
     * @return FormPartners
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
     * Set message
     *
     * @param string $message
     *
     * @return FormPartners
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
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return FormPartners
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
     * Set ip
     *
     * @param string $ip
     *
     * @return FormPartners
     */
    public function setIp($ip) {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp() {
        return $this->ip;
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
