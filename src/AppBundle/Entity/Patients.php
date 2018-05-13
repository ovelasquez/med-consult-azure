<?php
// src/AppBundle/Entity/Pacientes.php

namespace AppBundle\Entity;

//use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use AppUserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;


/**
 * @ORM\Entity
 * @ORM\Table(name="patients")
 */
class Patients
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="\AppUserBundle\Entity\User",cascade={"persist"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * 
     */
    protected $user;
    
    /**
     * @var string
     *
     * @ORM\Column(name="address1", type="string", length=255, nullable=false)
     */
    private $address1;
    
    /**
     * @var string
     *
     * @ORM\Column(name="address2", type="string", length=255, nullable=false)
     */
    private $address2;
    
    /**
     * @var string
     *
     * @ORM\Column(name="locality", type="string", length=255, nullable=false)
     */
    private $locality;
    
    /**
     * @var string
     *
     * @ORM\Column(name="province", type="string", length=255, nullable=false)
     */
    private $province;
    
    /**
     * @var string
     *
     * @ORM\Column(name="country", type="string", length=255, nullable=false)
     * @Assert\Country()
     */
    private $country;
    
    /**
     * @var string
     *
     * @ORM\Column(name="postalcode", type="string", length=255, nullable=false)
     */
    private $postalcode;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255, nullable=false)
     */
    private $phone;
    
    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    private $website;
    
     
    /**
     * @var \Date
     *
     * @ORM\Column(name="birthdate", type="date", nullable=true)
     */
    private $birthdate;
    
    
    /**
     * @var string
     *
     */
    private $emailact;
    
    /**
     * @var string
     *
     */
    private $nameact;
    
    /**
     * @var string
     *
     */
    private $lastnameact;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;
    
    /**
     * @var \StoragePlans
     *
     * @ORM\ManyToOne(targetEntity="StoragePlans")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="storage_plan_id", referencedColumnName="id")
     * })
     */
    private $storagePlan;
    
    /**
     * @var string
     *
     * @ORM\Column(name="gender", type="string", length=10, nullable=true)
     */
    private $gender;
    
    /**
     * @var integer     
     */
    private $yearsold;
    
    /**
     * @var integer     
     */
    private $stored;
    
     /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=255, nullable=true)
     */
    private $timezone;
    
     /**
     * @Recaptcha\IsTrue
     */
    public $recaptcha;
    
    /**
     * Set address1
     *
     * @param string $address1
     *
     * @return Patients
     */
    public function setAddress1($address1)
    {
        $this->address1 = $address1;

        return $this;
    }

    /**
     * Get address1
     *
     * @return string
     */
    public function getAddress1()
    {
        return $this->address1;
    }
    
    /**
     * Set address2
     *
     * @param string $address2
     *
     * @return Patients
     */
    public function setAddress2($address2)
    {
        $this->address2 = $address2;

        return $this;
    }

    /**
     * Get address2
     *
     * @return string
     */
    public function getAddress2()
    {
        return $this->address2;
    }
    
    
    /**
     * Set locality
     *
     * @param string $locality
     *
     * @return Patients
     */
    public function setLocality($locality)
    {
        $this->locality = $locality;

        return $this;
    }

    /**
     * Get locality
     *
     * @return string
     */
    public function getLocality()
    {
        return $this->locality;
    }
    
     /**
     * Set province
     *
     * @param string $province
     *
     * @return Patients
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * Get province
     *
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }
    
    
     /**
     * Set country
     *
     * @param string $country
     *
     * @return Patients
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
    
    /**
     * Set postalcode
     *
     * @param string $postalcode
     *
     * @return Patients
     */
    public function setPostalcode($postalcode)
    {
        $this->postalcode = $postalcode;

        return $this;
    }

    /**
     * Get postalcode
     *
     * @return string
     */
    public function getPostalcode()
    {
        return $this->postalcode;
    }
    
    
    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Patients
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }
    
    
    /**
     * Set website
     *
     * @param string $website
     *
     * @return Patients
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }
    
    /**
     * Set birthdate
     *
     * @param string $birthdate
     *
     * @return Patients
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return string
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }
    
    
    /**
     * Set user
     *
     * @param \AppUserBundle\Entity\User $user
     * @return Patients
     */
    public function setUser(User $user = null)
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
     * Set emailact
     *
     * @param string $emailact
     *
     * @return Patients
     */
    public function setEmailact($emailact)
    {
        $this->emailact = $emailact;

        return $this;
    }

    /**
     * Get emailact
     *
     * @return string
     */
    public function getEmailact()
    {
        return $this->emailact;
    }
    
    /**
     * Set nameact
     *
     * @param string $nameact
     *
     * @return Patients
     */
    public function setNameact($nameact)
    {
        $this->nameact = $nameact;

        return $this;
    }

    /**
     * Get nameact
     *
     * @return string
     */
    public function getNameact()
    {
        return $this->nameact;
    }
    
    /**
     * Set lastnameact
     *
     * @param string $lastnameact
     *
     * @return Patients
     */
    public function setLastnameact($lastnameact)
    {
        $this->lastnameact = $lastnameact;

        return $this;
    }

    /**
     * Get lastnameact
     *
     * @return string
     */
    public function getLastnameact()
    {
        return $this->lastnameact;
    }
    
    
    /**
     * Set storagePlan
     *
     * @param \AppUserBundle\Entity\StoragePlans $storagePlan
     * @return Patients
     */
    public function setStoragePlan(StoragePlans $storagePlan = null)
    {
        $this->storagePlan = $storagePlan;

        return $this;
    }

    /**
     * Get storagePlan
     *
     * @return \AppUserBundle\Entity\StoragePlans
     */
    public function getStoragePlan()
    {
        return $this->storagePlan;
    }
    
    
    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Patients
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }
    
    
    
    /**
     * Set gender
     *
     * @param string $gender
     *
     * @return Patients
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * Get gender
     *
     * @return string
     */
    public function getGender()
    {
        return $this->gender;
    }
    
    
    /**
     * Set yearsold
     *
     * @param integer $yearsold
     *
     * @return Patients
     */
    public function setYearsold($yearsold)
    {
        $this->yearsold = $yearsold;

        return $this;
    }

    /**
     * Get yearsold
     *
     * @return integer
     */
    public function getYearsold()
    {
        $this->yearsold = $this->CalculaEdad($this->getBirthdate()->format('Y-m-d'));
        return $this->yearsold;
    }
    
    
    /**
     * Set stored
     *
     * @param integer $stored
     *
     * @return Patients
     */
    public function setStored($stored)
    {
        $this->stored = $stored;

        return $this;
    }

    /**
     * Get stored
     *
     * @return integer
     */
    public function getStored()
    {
        return $this->stored;
    }
    
    
    /**
     * Set timezone
     *
     * @param string $timezone
     *
     * @return Patients
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
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
    
    public function __construct()
    {
        //parent::__construct();
    }
    
    
    /**
     *  get Absolute Path Image 
     * @return type
     */
    public function getAbsolutePath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadRootDir().'/'.$this->photo;
    }

    /**
     *  get Web Path Image 
     * @return type
     */
    public function getWebPath()
    {
        return null === $this->photo
            ? null
            : $this->getUploadDir().'/'.$this->photo;
    }
    
    /**
     *  get Upload Root Image 
     * @return type
     */
    public function getUploadRootDir()
    {
        // la ruta absoluta del directorio donde se deben
        // guardar los archivos cargados
        $dir =  str_replace('\\', '/', __DIR__) ;        
        return $dir.'/../../../web/'.$this->getUploadDir();
        //return 'C:/Bitnami/wampstack-5.5.27-0/apache2/htdocs/medeconsult/web/'.$this->getUploadDir();
    }

    /**
     *  get Upload Dir. 
     * @return type
     */
    public function getUploadDir()
    {
        // se deshace del __DIR__ para no meter la pata
        // al mostrar el documento/imagen cargada en la vista.
        return 'uploads/documents';
    }
    
    public function CalculaEdad($fecha) {
        list($Y, $m, $d) = explode("-", $fecha);
        return( date("md") < $m . $d ? date("Y") - $Y - 1 : date("Y") - $Y );
    }
}
