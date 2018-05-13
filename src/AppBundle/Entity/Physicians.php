<?php
// src/AppBundle/Entity/Pacientes.php

namespace AppBundle\Entity;

//use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use AppUserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints as Recaptcha;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Entity\PhysiciansRepository")
 * @ORM\Table(name="physicians")
 * @ORM\HasLifecycleCallbacks()
 */
class Physicians
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
     * @ORM\Column(name="jobtitle", type="string", length=255, nullable=false)
     */
    private $jobtitle;
    
    /**
     * @var string
     *
     * @ORM\Column(name="education", type="string", length=255, nullable=false)
     */
    private $education;
    
    /**
     * @var string
     *
     * @ORM\Column(name="abms", type="string", length=255, nullable=false)
     */
    private $abms;
    
    /**
     * @var string
     *
     * @ORM\Column(name="university", type="string", length=255, nullable=false)
     */
    private $university;
    
    /**
     * @var \AppBundle\Entity\Specialties
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Specialties")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="specialty", referencedColumnName="id")
     * })
     */
    private $specialty;
    
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
     * @ORM\Column(name="subspecialty", type="string", length=255, nullable=true)
     */
    private $subspecialty;
    
    /**
     * @var string
     *
     * @ORM\Column(name="research", type="string", length=255, nullable=true)
     */
    private $research;
    
    /**
     * @var string
     *
     * @ORM\Column(name="languages", type="string", length=255, nullable=false)
     */
    private $languages;
        
    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=false)
     */
    private $photo;
    
    /**
     * @var string
     *
     * @ORM\Column(name="timezone", type="string", length=255, nullable=true)
     */
    private $timezone;
    
    
    /**
     * @var string
     *
     * @ORM\Column(name="date_time_available", type="string",nullable=true)
     */
    private $datetimeAvailable;
    
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="volunteering_vzla", type="boolean", nullable=true)
     */
    private $volunteeringVzla;
    
    /**
     * @var string
     *
     * @ORM\Column(name="biography", type="text", length=1500, nullable=true)
     */
    private $biography;
    
    /**
     * @var string
     *
     * @ORM\Column(name="linkedin_profile", type="string",nullable=true)
     */
    private $linkedinProfile;
    
    /**
     * @var string
     *
     * @ORM\Column(name="web_site", type="string",nullable=true)
     */
    private $webSite;
    
    
    
    
     /**
     * @Recaptcha\IsTrue
     */
    public $recaptcha;
    
    private $file;
    
    private $oldImage;
    private $prePhysician;
    
    /**
     * Set education
     *
     * @param string $education
     *
     * @return Physicians
     */
    public function setEducation($education)
    {
        $this->education = $education;

        return $this;
    }

    /**
     * Get education
     *
     * @return string
     */
    public function getEducation()
    {
        return $this->education;
    }
    
    /**
     * Set jobtitle
     *
     * @param string $jobtitle
     *
     * @return Physicians
     */
    public function setJobtitle($jobtitle)
    {
        $this->jobtitle = $jobtitle;

        return $this;
    }

    /**
     * Get jobtitle
     *
     * @return string
     */
    public function getJobtitle()
    {
        return $this->jobtitle;
    }
    
    
    /**
     * Set abms
     *
     * @param string $abms
     *
     * @return Physicians
     */
    public function setAbms($abms)
    {
        $this->abms = $abms;

        return $this;
    }

    /**
     * Get abms
     *
     * @return string
     */
    public function getAbms()
    {
        return $this->abms;
    }
    
     /**
     * Set university
     *
     * @param string $university
     *
     * @return Physicians
     */
    public function setUniversity($university)
    {
        $this->university = $university;

        return $this;
    }

    /**
     * Get university
     *
     * @return string
     */
    public function getUniversity()
    {
        return $this->university;
    }
    

    /**
     * Set specialty
     *
     * @param \AppBundle\Entity\Specialties $specialty
     *
     * @return MedicalForms
     */
    public function setSpecialty(\AppBundle\Entity\Specialties $specialty = null)
    {
        $this->specialty = $specialty;

        return $this;
    }

    /**
     * Get specialty
     *
     * @return \AppBundle\Entity\Specialties
     */
    public function getSpecialty()
    {
        return $this->specialty;
    }
    
    /**
     * Set postalcode
     *
     * @param string $postalcode
     *
     * @return Physicians
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
     * @return Physicians
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
     * Set subspecialty
     *
     * @param string $subspecialty
     *
     * @return Physicians
     */
    public function setSubspecialty($subspecialty)
    {
        $this->subspecialty = $subspecialty;

        return $this;
    }

    /**
     * Get subspecialty
     *
     * @return string
     */
    public function getSubspecialty()
    {
        return $this->subspecialty;
    }
       
    
    /**
     * Set user
     *
     * @param \AppUserBundle\Entity\User $user
     * @return Physicians
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
     * Set research
     *
     * @param string $research
     *
     * @return Physicians
     */
    public function setResearch($research)
    {
        $this->research = $research;

        return $this;
    }

    /**
     * Get research
     *
     * @return string
     */
    public function getResearch()
    {
        return $this->research;
    }
    
    /**
     * Set languages
     *
     * @param string $languages
     *
     * @return Physicians
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get languages
     *
     * @return string
     */
    public function getLanguages()
    {
        return $this->languages;
    }
    
    
     /**
     * Set photo
     *
     * @param string $photo
     *
     * @return Physicians
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
     * Set timezone
     *
     * @param string $timezone
     *
     * @return Physicians
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
     * Set datetimeAvailable
     *
     * @param string $datetimeAvailable
     *
     * @return Physicians
     */
    public function setDatetimeAvailable($datetimeAvailable)
    {
        $this->datetimeAvailable = $datetimeAvailable;

        return $this;
    }

    /**
     * Get datetimeAvailable
     *
     * @return string
     */
    public function getDatetimeAvailable()
    {
        return $this->datetimeAvailable;
    }
    
    /**
     * Set prePhysician
     *
     * @param \AppBundle\Entity\PrePhysicians $prePhysician
     *
     * @return Physicians
     */
    public function setPrePhysician(\AppBundle\Entity\PrePhysicians $prePhysician = null)
    {
        $this->prePhysician = $prePhysician;

        return $this;
    }

    /**
     * Get prePhysician
     *
     * @return \AppBundle\Entity\PrePhysicians
     */
    public function getPrePhysician()
    {
        return $this->prePhysician;
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
    
    /**
     * Sets file.
     *
     * @param string $file
     */
    public function setFile($file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
    
    
    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function preUpload()
    {
//        $this->oldImage = null;
//        if (null !== $this->file) {
//            // haz lo que quieras para generar un nombre único
//            $filename = sha1(uniqid(mt_rand(), true));
//            if (is_file($this->getAbsolutePath())){
//                $this->oldImage =  $this->getAbsolutePath();
//            }
//            $this->photo = $filename.'.'.$this->file->guessExtension();
//        }        
        
    }

    /**
     * @ORM\PostPersist
     * @ORM\PostUpdate
     */
    public function upload()
    {
//        if (null === $this->file) {
//            return;
//        }
//
//        // si hay un error al mover el archivo, move() automáticamente
//        // envía una excepción. This will properly prevent
//        // the entity from being persisted to the database on error
//        $this->file->move($this->getUploadRootDir(), $this->photo);
//
//        // check if we have an old image
//        if (isset($this->oldImage)) {
//            // delete the old image
//            unlink($this->oldImage);
//            // clear the temp image path
//            $this->oldImage = null;
//        }
//        $this->file = null;
    }     

    /**
     * @ORM\PostRemove
     */
    public function removeUpload()
    {
        if (is_file($this->getAbsolutePath())){
            unlink($this->getAbsolutePath());      
        }
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
     * Set volunteeringVzla
     *
     * @param boolean $volunteeringVzla
     *
     * @return Physicians
     */
    public function setVolunteeringVzla($volunteeringVzla)
    {
        $this->volunteeringVzla = $volunteeringVzla;

        return $this;
    }

    /**
     * Get volunteeringVzla
     *
     * @return boolean
     */
    public function getVolunteeringVzla()
    {
        return $this->volunteeringVzla;
    }
    
    /**
     * Set biography
     *
     * @param string $biography
     *
     * @return Physicians
     */
    public function setBiography($biography)
    {
        $this->biography = $biography;

        return $this;
    }

    /**
     * Get biography
     *
     * @return string
     */
    public function getBiography()
    {
        return $this->biography;
    }
    
    /**
     * Set linkedinProfile
     *
     * @param string $linkedinProfile
     *
     * @return Physicians
     */
    public function setLinkedinProfile($linkedinProfile)
    {
        $this->linkedinProfile = $linkedinProfile;

        return $this;
    }

    /**
     * Get linkedinProfile
     *
     * @return string
     */
    public function getLinkedinProfile()
    {
        return $this->linkedinProfile;
    }
    
    /**
     * Set webSite
     *
     * @param string $webSite
     *
     * @return Physicians
     */
    public function setWebSite($webSite)
    {
        $this->webSite = $webSite;

        return $this;
    }

    /**
     * Get webSite
     *
     * @return string
     */
    public function getWebSite()
    {
        return $this->webSite;
    }
    
}
