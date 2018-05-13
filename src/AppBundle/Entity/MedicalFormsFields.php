<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use AppBundle\Utils\BaseFields;
use AppBundle\Utils\EncrypterFields;
use AppBundle\Utils\UserAct;

/**
 * MedicalFormsFields
 *
 * @ORM\Table(name="medical_forms_fields", indexes={@ORM\Index(name="medical_forms_fieldset_id", columns={"medical_forms_fieldset_id"})})
 * @ORM\Entity
 * @UniqueEntity("name",message="La pregunta ya esta en uso.")
 */
class MedicalFormsFields {

    private $userAct;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20, nullable=false,unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="field", type="string", length=255, nullable=false)
     */
    private $field;

    /**
     * @var string
     *
     * @ORM\Column(name="label", type="string", length=255, nullable=false)
     */
    private $label;

    /**
     * @var string
     *
     * @ORM\Column(name="data", type="text", nullable=true)
     */
    private $data;

    /**
     * @var string
     *
     * @ORM\Column(name="config", type="text", nullable=true)
     */
    private $config;
    private $configjson;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \AppBundle\Entity\MedicalFormsFieldsets
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\MedicalFormsFieldsets")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="medical_forms_fieldset_id", referencedColumnName="id")
     * })
     */
    private $medicalFormsFieldset;

    /**
     * @var integer
     *
     * @ORM\Column(name="subgroup", type="bigint", nullable=true)
     */
    private $subgroup;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordeid", type="integer", nullable=false)
     */
    private $orderid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean", nullable=false)
     */
    private $required;

    /**
     * @var boolean
     *
     * @ORM\Column(name="showlabel", type="boolean", nullable=false)
     */
    private $showlabel;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", nullable=true)
     */
    private $description;
    
    /**
     * @var \AppBundle\Entity\Physicians
     *
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Physicians")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="physician_id", referencedColumnName="id")
     * })
     */
    private $physician;

    /**
     * @var string
     *
     * @ORM\Column(name="help", type="string", nullable=true)
     */
    private $help;
    private $group;
    private $input;

    /**
     * @var string
     * 
     * @ORM\Column(name="value_temp", type="string", nullable=true)
     */
    private $valueTemp;

    /**
     * @var string
     * 
     * @ORM\Column(name="key_enc", type="string", nullable=true)
     */
    private $keyEnc;

    /**
     * @var string
     */
    private $valueHtml;

    /**
     * @var integer
     */
    private $numCol;

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MedicalForms
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
     * Get name
     *
     * @return string
     */
    public function getInputName() {
        $car = '';
        if (null !== ($this->getSubgroup())):
            $config = $this->getSubgroup()->getConfigjson();
            if (isset($config->cardinality) && $config->cardinality !== 1):
                $car = '[]';
            endif;
        elseif (null !== ($this->getConfigjson())):
            $config = $this->getConfigjson();
            if (isset($config->cardinality) && $config->cardinality !== 1):
                $car = '[]';
            endif;
        endif;

        if ($this->field == "file"):
            return $this->getName() . $car;
        else:
            return $this->getMedicalFormsFieldset()->getMedicalForm()->getFormName() . '[' . $this->getName() . ']' . $car;
        endif;
    }

    /**
     * Set field
     *
     * @param string $field
     *
     * @return MedicalFormsFields
     */
    public function setField($field) {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     *
     * @return string
     */
    public function getField() {
        return $this->field;
    }

    /**
     * Set label
     *
     * @param string $label
     *
     * @return MedicalFormsFields
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * Set data
     *
     * @param string $data
     *
     * @return MedicalFormsFields
     */
    public function setData($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data
     *
     * @return string
     */
    public function getData() {
        return $this->data;
    }

    /**
     * Set config
     *
     * @param string $config
     *
     * @return MedicalFormsFields
     */
    public function setConfig($config) {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return string
     */
    public function getConfig() {
        return $this->config;
    }

    /**
     * Get configjson
     *
     * @return string
     */
    public function getConfigjson() {
        $this->configjson = json_decode($this->config);

        return $this->configjson;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set medicalFormsFieldset
     *
     * @param \AppBundle\Entity\MedicalFormsFieldsets $medicalFormsFieldset
     *
     * @return MedicalFormsFields
     */
    public function setMedicalFormsFieldset(\AppBundle\Entity\MedicalFormsFieldsets $medicalFormsFieldset = null) {
        $this->medicalFormsFieldset = $medicalFormsFieldset;

        return $this;
    }

    /**
     * Get medicalFormsFieldset
     *
     * @return \AppBundle\Entity\MedicalFormsFieldsets
     */
    public function getMedicalFormsFieldset() {
        return $this->medicalFormsFieldset;
    }

    /**
     * Set group
     *
     * @param \AppBundle\Entity\MedicalFormsFields $group
     *
     * @return MedicalFormsFields
     */
    public function setGroup(\AppBundle\Entity\MedicalFormsFields $group = null) {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \AppBundle\Entity\MedicalFormsFields
     */
    public function getGroup() {
        return $this->group;
    }

    /**
     * Set subgroup
     *
     * @param \AppBundle\Entity\MedicalFormsFields $subgroup
     *
     * @return MedicalFormsFields
     */
    public function setSubgroup(\AppBundle\Entity\MedicalFormsFields $subgroup = null) {
        $this->subgroup = $subgroup;

        return $this;
    }

    /**
     * Get subgroup
     *
     * @return \AppBundle\Entity\MedicalFormsFields
     */
    public function getSubgroup() {
        return $this->subgroup;
    }

    /**
     * Set orderid
     *
     * @param integer $orderid
     *
     * @return MedicalFormsFields
     */
    public function setOrderid($orderid) {
        $this->orderid = $orderid;

        return $this;
    }

    /**
     * Get orderid
     *
     * @return integer
     */
    public function getOrderid() {
        return $this->orderid;
    }

    /**
     * Set required
     *
     * @param boolean $required
     *
     * @return MedicalFormsFields
     */
    public function setRequired($required) {
        $this->required = $required;

        return $this;
    }

    /**
     * Get required
     *
     * @return boolean
     */
    public function getRequired() {
        return $this->required;
    }

    /**
     * Set showlabel
     *
     * @param boolean $showlabel
     *
     * @return MedicalFormsFields
     */
    public function setShowlabel($showlabel) {
        $this->showlabel = $showlabel;

        return $this;
    }

    /**
     * Get showlabel
     *
     * @return boolean
     */
    public function getShowlabel() {
        return $this->showlabel;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MedicalFormsFields
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Set help
     *
     * @param string $help
     *
     * @return MedicalFormsFields
     */
    public function setHelp($help) {
        $this->help = $help;

        return $this;
    }

    /**
     * Get help
     *
     * @return string
     */
    public function getHelp() {
        return $this->help;
    }

    /**
     * Get input
     *
     * @return string
     */
    public function getInput() {
        $this->input = '';
        $baseF = new BaseFields();
        $class_methods = get_class_methods($baseF);

        if (array_search($this->getField() . "InputField", $class_methods) !== false):
            //\Doctrine\Common\Util\Debug::dump($this);die();            
            $this->input = call_user_func(array($baseF, $this->getField() . "InputField"), $this);
        endif;
        return $this->input;
    }

    /**
     * Set valueTemp
     *
     * @param string $valueTemp
     *
     * @return MedicalForms
     */
    public function setValueTemp($valueTemp) {
        $this->valueTemp = $valueTemp;

        return $this;
    }

    /**
     * Get valueTemp
     *
     * @return string
     */
    public function getValueTemp() {
        $security = new EncrypterFields();
        $publicKey = $this->keyEnc;
        //var_dump($this->valueTemp);
        if (strlen($this->valueTemp) > 1):
            return $security->decrypt($this->valueTemp, $publicKey); //EncrypterFields::decrypt($this->valueTemp);//$this->valueTemp;
        else:
            return $this->valueTemp; //EncrypterFields::decrypt($this->valueTemp);//$this->valueTemp;
        endif;
    }

    /**
     * Set keyEnc
     *
     * @param string $keyEnc
     *
     * @return MedicalForms
     */
    public function setKeyEnc($keyEnc) {
        $this->keyEnc = $keyEnc;
        return $this;
    }

    /**
     * Get keyEnc
     *
     * @return string
     */
    public function getKeyEnc() {
        return $this->keyEnc;
    }

    /**
     * Get input
     *
     * @return string
     */
    public function getValueHtml() {
        $value = $this->getValueTemp();
        $values = array();
        if (isset($value) && !empty($value)):
            $values = explode("|", $value);
        else:
            $this->valueHtml = '';
            return $this->valueHtml;
        endif;

        $baseF = new BaseFields();
        if ($this->data !== null):
            $data = $baseF->getData($this->data);

            $this->valueHtml = '';
            foreach ($data as $val) :
                if (in_array($val[0], $values)):
                    $this->valueHtml.=$this->getHtml($val);
                endif;

            endforeach;

            return $this->valueHtml;
        endif;

        $this->valueHtml = '';
        foreach ($values as $val) :
            $this->valueHtml.=$this->getHtml($val);
        endforeach;

        return $this->valueHtml;
    }
    
    /**
     * Set physician
     *
     * @param \AppBundle\Entity\Physicians $physician
     *
     * @return MedicalFormsFields
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
     * Get number col
     *
     * @return integer
     */
    public function getNumCol() {
        if (isset($this->data)):$baseF = new BaseFields();
            $this->numCol = count($baseF->getData($this->data));
            return $this->numCol;
        else:
            return 0;
        endif;
    }

    private function getHtml($val) {
        $html = '';
        if (!is_array($val)):
            $val = array($val);
        endif;

        if ($this->field == 'file'):
            $html = '<a href="#"  target="_blank" class="files-pat" rel="uploads/documents/">' . (isset($val[1]) ? $val[1] : $val[0]) . '</a>';
            $nameFile = '';
            $fileA = array();
            $nameWExt = '';
            if (is_file(__DIR__ . '/../../../' . $val[0])):
                $fileA = file(__DIR__ . '/../../../' . $val[0]);
                $nameFile = $fileA[0];
                $nameWExt = explode('/', $val[0]);
                $nameWExt = $nameWExt[count($nameWExt) - 1];
                $nameWExt = explode('.', $nameWExt);
                $nameWExt = $nameWExt[count($nameWExt) - 2];
                $html = '<p><a class="medicalFormFile" class="files-pat" href="#" target="_blank" data-f1="' . $this->getMedicalFormsFieldset()->getMedicalForm()->getFormName() . '" data-f2="' . $nameWExt . '" >' . $nameFile . '</a></p>';
            else:
                $html = '<p>Archivo inexistente</p>';
            endif;
            
        else:
            $html = '<p>' . (isset($val[1]) ? $val[1] : $val[0]) . '</p>';
        endif;
        return $html;
    }

    public function getUserAct() {
        return $this->userAct;
    }

//    public function __construct(UserAct $userAct=null) {        
//        $this->userAct=$userAct->getUserAct();
//        
//    }
}