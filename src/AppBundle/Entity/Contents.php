<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Contents
 *
 * @ORM\Table(name="contents")
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * 
 */

class Contents
{
    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="body", type="text", length=65535, nullable=true)
     */
    private $body;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", length=255, nullable=true)
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=20, nullable=true)
     */
    private $tag;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime", nullable=true)
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="changed", type="datetime", nullable=true)
     */
    private $changed;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


     /**
     * @Assert\File(
     * maxSize="1M",
     * mimeTypes={"image/png", "image/jpeg", "image/gif"}
     * )
     */
    private $file;
    
    private $oldImage;
    
    
    /**
     * @var integer
     *
     * @ORM\Column(name="weight", type="bigint", nullable=true)
     */
    private $weight;
    

    /**
     * Set title
     *
     * @param string $title
     *
     * @return Contents
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Contents
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
    
    /**
     * set body
     *
     * @param string $path
     *
     * @return Contents
     */
    public function setProcBody($path)
    {
        $this->body= str_replace('%path%',$path,$this->body);        
        return $this;
    }

    /**
     * Set image
     *
     * @param string $image
     *
     * @return Contents
     */
    public function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set tag
     *
     * @param string $tag
     *
     * @return Contents
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }
    
    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return Contents
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Contents
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set changed
     *
     * @param \DateTime $changed
     *
     * @return Contents
     */
    public function setChanged($changed)
    {
        $this->changed = $changed;

        return $this;
    }

    /**
     * Get changed
     *
     * @return \DateTime
     */
    public function getChanged()
    {
        return $this->changed;
    }

    
    /**
     * Set weight
     *
     * @param integer $weight
     *
     * @return Contents
     */
    public function setWeight($weight) {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get orderid
     *
     * @return integer
     */
    public function getWeight() {
        return $this->weight;
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
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
        // check if we have an old image image
        if (isset($this->image)) {
            // store the old name to delete after the update
            $this->oldImage = $this->image;
            $this->image = null;
        } else {
            $this->image = 'initial';
        }
    }
    
    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload()
    {
        if (null !== $this->getFile()) {
            // do whatever you want to generate a unique name
            $filename = sha1(uniqid(mt_rand(), true));
            $this->image = $filename.'.'.$this->getFile()->guessExtension();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload()
    {
        if (null === $this->getFile()) {
            return;
        }

        // if there is an error when moving the file, an exception will
        // be automatically thrown by move(). This will properly prevent
        // the entity from being persisted to the database on error
        $this->getFile()->move($this->getUploadRootDir(), $this->image);

        // check if we have an old image
        if (isset($this->oldImage)) {
            // delete the old image
            unlink($this->getUploadRootDir().'/'.$this->oldImage);
            // clear the oldImage image image
            $this->oldImage = null;
        }
        $this->file = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeUpload()
    {
        $file = $this->getAbsolutePath();
        if ($file) {
            unlink($file);
        }
    }
        
    /**
     *  get Absolute Path Image 
     * @return type
     */
    public function getAbsolutePath()
    {
        return null === $this->image
            ? null
            : $this->getUploadRootDir().'/'.$this->image;
    }

    /**
     *  get Web Path Image 
     * @return type
     */
    public function getWebPath()
    {
        return null === $this->image
            ? null
            : $this->getUploadDir().'/'.$this->image;
    }
    
    /**
     *  get Upload Root Image 
     * @return type
     */
    protected function getUploadRootDir()
    {
        // la ruta absoluta del directorio donde se deben
        // guardar los archivos cargados
        return __DIR__.'/../../../web/'.$this->getUploadDir();
    }

    /**
     *  get Upload Dir. 
     * @return type
     */
    protected function getUploadDir()
    {
        // se deshace del __DIR__ para no meter la pata
        // al mostrar el documento/imagen cargada en la vista.
        return 'uploads/documents';
    }
    
    
//    
//    /**
//     * Sets file.
//     *
//     * @param UploadedFile $file
//     */
//    public function setFile(UploadedFile $file = null)
//    {
//        $this->file = $file;
//    }
//
//    /**
//     * Get file.
//     *
//     * @return UploadedFile
//     */
//    public function getFile()
//    {
//        return $this->file;
//    }
//    
//    
//    /**
//     * @ORM\PrePersist
//     * @ORM\PreUpdate
//     */
//    public function preUpload()
//    {
//        $this->oldImage = null;
//        if (null !== $this->file) {
//            // haz lo que quieras para generar un nombre único
//            $filename = sha1(uniqid(mt_rand(), true));
//            if (is_file($this->getAbsolutePath())){
//                $this->oldImage =  $this->getAbsolutePath();
//            }
//            $this->image = $filename.'.'.$this->file->guessExtension();
//        }
//        
//        $this->setChanged(new \DateTime("now"));
//    }
//
//    /**
//     * @ORM\PostPersist
//     * @ORM\PostUpdate
//     */
//    public function upload()
//    {
//        if (null === $this->file) {
//            return;
//        }
//
//        // si hay un error al mover el archivo, move() automáticamente
//        // envía una excepción. This will properly prevent
//        // the entity from being persisted to the database on error
//        echo"<pre>";
//        \Doctrine\Common\Util\Debug::dump(getStoragePlan);
//        echo"</pre>";
//        exit();
//        $this->file->move($this->getUploadRootDir(), $this->image);
//
//        // check if we have an old image
//        if (isset($this->oldImage)) {
//            // delete the old image
//            unlink($this->oldImage);
//            // clear the temp image path
//            $this->oldImage = null;
//        }
//        $this->file = null;
//    }     
//
//    /**
//     * @ORM\PostRemove
//     */
//    public function removeUpload()
//    {
//        if (is_file($this->getAbsolutePath())){
//            unlink($this->getAbsolutePath());      
//        }
//    }
//    
//    
//    /**
//     * Set weight
//     *
//     * @param integer $weight
//     *
//     * @return Contents
//     */
//    public function setWeight($weight) {
//        $this->weight = $weight;
//
//        return $this;
//    }
//
//    /**
//     * Get orderid
//     *
//     * @return integer
//     */
//    public function getWeight() {
//        return $this->weight;
//    }
////    
////    public function upload()
////    {
////        // the file property can be empty if the field is not required
////        if (null === $this->getFile()) {
////            return;
////        }
////
////        // aquí usa el nombre de archivo original pero lo debes
////        // sanear al menos para evitar cualquier problema de seguridad
////
////        // move takes the target directory and then the
////        // target filename to move to
////        $this->getFile()->move(
////            $this->getUploadRootDir(),
////            $this->getFile()->getClientOriginalName()
////        );
////
////        
////        // compute a random name and try to guess the extension (more secure)
////        $extension = $this->getFile()->guessExtension();
////        if (!$extension) {
////            // extension cannot be guessed
////            $extension = 'bin';
////        }
////        
////        $nameFile = rand(1, 99999).'.'.$extension;
////        $this->getFile()->move($this->getUploadRootDir(), $nameFile);
////
////        // set the path property to the filename where you've saved the file
////        $this->image = $nameFile; //$this->getFile()->getClientOriginalName();
////
////        
////        // limpia la propiedad «file» ya que no la necesitas más
////        $this->file = null;
////    
////    }
}
