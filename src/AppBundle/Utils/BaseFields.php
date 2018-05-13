<?php

use \AppBundle\Entity\MedicalFormsFields;

namespace AppBundle\Utils;

/**
 * Description of BaseFields
 *
 * @author Mariana
 */
class BaseFields {

    public function gridField($ini = null) {
        $cardinality = $class = "";
        $txtam = "Añadir más";
        if ($ini !== null):
            if (isset($ini->cardinality)):
                $cardinality = $ini->cardinality;
            endif;
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
            if (isset($ini->txtam)):
                $txtam = $ini->txtam;
            endif;
            
        endif;
        $field = array(
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf_class">Clase</label><input type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '<div><label for="field_conf">Número de valores</label>'
            . '<input type="hidden" value="' . $cardinality . '" id="cardinality_val" />'
            . '<select id="field_conf_cardinality" class=" form-control select_cardinality" name="field_conf[cardinality]"><option value="-1">Ilimitado</option><option value="1" selected="selected">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select>'
            . '<div class="help-data">El número máximo de valores que los usuarios pueden entrar en este campo.<br>Sin límite proporcionará un botón "Añadir más" con el que los usuarios podrán añadir tantos valores como deseen.</div></div>'
            . '<div><label for="field_conf_txtam">Texto de añadir más</label><input type="text" value="' . $txtam . '" id="field_conf_txtvm" required="required" name="field_conf[txtam]"></div>'
            . '</div>',
            'showData' => 1,
            'type' => 'grid',
            'helpData' => '<div class="help-data-info">*Ingrese el título de cada columna escribiendo un valor por línea</div>',
        );
        return $field;
    }

    public function gridInputField($field) {
        $grid = '';
        $data = $this->getData($field->getData());
        $arrayColor = array("uno", "dos", "tres");
        $config = $field->getConfigjson();
        //$name = $field->getInputName() . ($config->cardinality !== 1 ? '[]' : '');

        if (is_array($data)):
            if (isset($config->cardinality) && $config->cardinality == -1):
                $grid.='<tr class="tr-a-mas-f"  ><td colspan="' . (count($data) + 1) . '" ><a class="a-mas-f  btn btn-info" href="#" rel="' . $field->getName() . '">' . ((isset($config->txtam)) ? $config->txtam : "<i class='icon-plus4'></i>Añadir más") . '</a></td></tr>';
            endif;

            $grid.='<tr class="groupInputs">';
            $nCol = floor(12 / count($data));

            for ($i = 0; $i < count($data); $i++) :
                $color = ($i < count($arrayColor)) ? $arrayColor[$i] : $arrayColor[count($arrayColor) - 1];
                $grid.= '<td class="cuestEncbTabl ' . $color . ' ' . '" >' . $data[$i][0] . '</td>';
            endfor;

            if (isset($config->cardinality) && $config->cardinality == -1):
                $grid.= '<td class="cuestEncbTabl ' . $arrayColor[count($arrayColor) - 1] . ' tdelim" >&nbsp;</td>';
            endif;

            $grid.='</tr>';
        endif;
        return $grid . ((isset($config->cardinality) && $config->cardinality !== 1 ) ? '<tr class="f-mas-f groupInputs" id="tr_' . $field->getName() . '">' : '<tr>');
    }

    public function selectField($ini = null) {
        $class = $dataMult = "";
        if ($ini !== null):
            if (isset($ini->multiple) && $ini->multiple == 'on'):
                $dataMult = 'checked="checked"';
            endif;
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
        endif;
        $field = array(
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf_m">Múltiple</label><input type="checkbox" ' . $dataMult . ' id="field_conf_m" name="field_conf[multiple]"></div>'
            . '<div><label for="field_conf_class">Clase</label><input type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '</div>',
            'showData' => 1,
            'type' => 'select',
        );
        return $field;
    }

    public function selectInputField($field) {
        $name = $field->getInputName();
        $value = $field->getValueTemp();

        $values = array();
        if (isset($value) && !empty($value)):
            $values = explode("|", $value);
        endif;
        $values =array_map('trim',$values );
        
        $data = $this->getData($field->getData());
        $config = $field->getConfigjson();
        $select = "";
        if (is_array($data)):
            $select.='<select class="form-input form-control" id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . ' ' . ((isset($config->multiple) && $config->multiple == "on") ? 'multiple' : '') . '>';
            foreach ($data as $val) :
                $select.= (count($val) > 1) ? ('<option ' . (in_array(trim($val[0]), $values) ? 'selected="selected"' : '') . ' value="' . trim($val[0]) . '">' . $val[1] . '</option>') : '<option ' . (in_array(trim($val[0]), $values) ? 'selected="selected"' : '') . ' value="' . trim($val[0]) . '">' . $val[0] . '</option>';
            endforeach;
            $select.='</select>';
        endif;

        return (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid") ? '<td class="colorTablaUno borderBlanco">' . $select . "</td>" : $select;
    }

    public function checkField($ini = null) {
        $class = $condx = $dataMult = "";
        if ($ini !== null):
            if (isset($ini->multiple) && $ini->multiple == 'on'):
                $dataMult = 'checked="checked"';
            endif;
            if (isset($ini->condx) && $ini->condx != ''):
                $condx = $ini->condx;
            endif;
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
        endif;
        $field = array(
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf">Múltiple</label><input class=" form-control " type="checkbox" ' . $dataMult . ' id="field_conf" name="field_conf[multiple]"></div>'
            . '<div><label for="field_conf_condx">Condicionado por</label><input class=" form-control " type="text" value="' . $condx . '" id="field_conf_condx" name="field_conf[condx]"></div>'
            . '<div><label for="field_conf_class">Clase</label><input class=" form-control " type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '</div>',
            'showData' => 1,
            'type' => 'check',
        );
        return $field;
    }

    public function checkInputField($field) {
        $name = $field->getInputName();
        $data = $this->getData($field->getData());
        $config = $field->getConfigjson();
        $checks = "";

        $value = $field->getValueTemp();
        $values = array();
        if (isset($value) && !empty($value)):
            $values = explode("|", $value);
        endif;
        
        $values =array_map('trim',$values );
        //var_dump($values);
        $contC= 1;
        if (is_array($data)):
            $checks .= "<div class='input-group groupCheck'>";
            foreach ($data as $val) :
                
                $checks.= (count($val) > 1) ? 
                ('<label class="display-inline-block custom-control custom-' . ((isset($config->multiple) && $config->multiple == "on") ? 'checkbox' : 'radio') . ' ml-1" for="' . $field->getName() .$contC.'">'.
                    '<input class="custom-control-input form-input ' . ((isset($config->condx) && $config->condx!=='' ) ? 'condx' : '') . ' " ' . (in_array(trim($val[0]), $values) ? 'checked="checked"' : '') . '  ' . ($field->getRequired() ? 'required="required"' : '') . ' name="' . $name . '[]" id="' . $field->getName() .$contC. '" value="' . trim($val[0]) . '" type="' . ((isset($config->multiple) && $config->multiple == "on") ? 'checkbox' : 'radio') . '" data-condx="' . (isset($config->condx) ? $config->condx : '') . '">'.
                    '<span class="custom-control-indicator"></span>'.
                    '<span class="custom-control-description ml-0">' . $val[1] . '</span>'.
                    '</label>') : 
                ('<label class="display-inline-block custom-control custom-' . ((isset($config->multiple) && $config->multiple == "on") ? 'checkbox' : 'radio') . ' ml-1" for="' . $field->getName() .$contC.'">'.
                    '<input class="custom-control-input form-input ' . ((isset($config->condx) && $config->condx!=='' ) ? 'condx' : '') . ' " ' . (in_array(trim($val[0]), $values) ? 'checked="checked"' : '') . '  ' . ($field->getRequired() ? 'required="required"' : '') . ' name="' . $name . '[]" id="' . $field->getName() .$contC. '" value="' . trim($val[0]) . '" type="' . ((isset($config->multiple) && $config->multiple == "on") ? 'checkbox' : 'radio') . '" data-condx="' . (isset($config->condx) ? $config->condx : '') . '">'.
                    '<span class="custom-control-indicator"></span>'.
                    '<span class="custom-control-description ml-0">' . $val[0] . '</span>'.
                    '</label>');
                $contC++;
            endforeach;
            $checks .= "</div>";
        endif;

        return (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid") ? '<td class="colorTablaUno borderBlanco">' . $checks . "</td>" : $checks;
    }

    public function textField($ini = null) {
        $class = $condx = $numerico = $placeholder = "";
        if ($ini !== null):
            if (isset($ini->placeholder) && $ini->placeholder != ''):
                $placeholder = $ini->placeholder;
            endif;
            if (isset($ini->condx) && $ini->condx != ''):
                $condx = $ini->condx;
            endif;
            if (isset($ini->numerico) && $ini->numerico == 'on'):
                $numerico = 'checked="checked"';
            endif;
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
        endif;
        $field = array(
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf_ph">Placeholder</label><input type="text" value="' . $placeholder . '" id="field_conf_ph" name="field_conf[placeholder]"></div>'
            . '<div><label for="field_conf_num">Numérico</label><input type="checkbox" ' . $numerico . ' id="field_conf_num" name="field_conf[numerico]"></div>'
            . '<div><label for="field_conf_condx">Condicionado por</label><input type="text" value="' . $condx . '" id="field_conf_condx" name="field_conf[condx]"></div>'
            . '<div><label for="field_conf_class">Clase</label><input type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '</div>',
            'showData' => 0,
            'type' => 'text',
        );
        return $field;
    }

    public function textInputField($field) {
        $name = $field->getInputName();
        $config = $field->getConfigjson();

        $text = '';
        $value = ($field->getValueTemp() !== NULL) ? explode("|", $field->getValueTemp()) : NULL;

        //var_dump($value); die();

        if (isset($value) && !empty($value)):
            for ($i = 0; $i < count($value); $i++):
                if ($value[$i] !== ""):
                    $inp = '<input type="' . ((isset($config->numerico) && $config->numerico == "on") ? 'number' : 'text') . '"  class="form-input form-control ' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . '"  value="' . $value[$i] . '" placeholder="' . (isset($config->placeholder) ? $config->placeholder : '') . '" data-condx="' . (isset($config->condx) ? $config->condx : '') . '" id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . ' />';
                    if (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid"):
                        $text .= '<div class="inp">' . $inp . '</div>';
                    else:
                        $text .=$inp;
                    endif;
                endif;
            endfor;
        endif;

        if ($text === ''):
            $text = '<input type="' . ((isset($config->numerico) && $config->numerico == "on") ? 'number' : 'text') . '"  class="form-input form-control ' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . '"  value="' . $field->getValueTemp() . '" placeholder="' . (isset($config->placeholder) ? $config->placeholder : '') . '" id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . ' data-condx="' . (isset($config->condx) ? $config->condx : '') . '" />';
        endif;

        return (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid") ? '<td class="colorTablaUno borderBlanco">' . $text . "</td>" : $text;
    }

    public function textareaField($ini = null) {

        $class = $condx =  "";
        if ($ini !== null):            
            if (isset($ini->condx) && $ini->condx != ''):
                $condx = $ini->condx;
            endif;  
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
        endif;
        
        $field = array(
            'showData' => 0,
            'type' => 'textarea',
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf_condx">Condicionado por</label><input type="text" value="' . $condx . '" id="field_conf_condx" name="field_conf[condx]"></div>'
            . '<div><label for="field_conf_class">Clase</label><input type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '</div>',
        );
        return $field;
    }

    public function textareaInputField($field) {
        $name = $field->getInputName();
        $config = $field->getConfigjson();

        $text = '';
        $value = ($field->getValueTemp() !== NULL) ? explode("|", $field->getValueTemp()) : NULL;
        if (isset($value) && !empty($value)):
            for ($i = 0; $i < count($value); $i++):
                if ($value[$i] !== ""):
                    $inp = '<textarea rows="5"  class="form-input form-control"  id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . '>' . $value[$i] . '</textarea>';
                    if (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid"):
                        $text .= '<div class="inp">' . $inp . '</div>';
                    else:
                        $text .=$inp;
                    endif;
                endif;
            endfor;
        endif;

        if ($text === ''):
            $text = '<textarea  class="form-input form-control ' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . '"  id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . ' data-condx="' . (isset($config->condx) ? $config->condx : '') . '">' . $field->getValueTemp() . '</textarea>';
        endif;



        return (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid") ? '<td class="colorTablaUno borderBlanco">' . $text . "</td>" : $text;
    }

    public function fileField($ini = null) {
        $class = $condx = $cardinality = "";
        if ($ini !== null):
            if (isset($ini->cardinality)):
                $cardinality = $ini->cardinality;
            endif;
            if (isset($ini->condx) && $ini->condx != ''):
                $condx = $ini->condx;
            endif;  
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
        endif;
        $field = array(
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf">Número de valores</label>'
            . '<input type="hidden" value="' . $cardinality . '" id="cardinality_val" />'
            . '<select id="field_conf_cardinality" class="select_cardinality" name="field_conf[cardinality]"><option value="-1">Ilimitado</option><option value="1" selected="selected">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option></select>'
            . '<div class="help-data">El número máximo de valores que los usuarios pueden entrar en este campo.<br>Sin límite proporcionará un botón "Añadir más" con el que los usuarios podrán añadir tantos valores como deseen.</div></div>'
            . '<div><label for="field_conf_condx">Condicionado por</label><input type="text" value="' . $condx . '" id="field_conf_condx" name="field_conf[condx]"></div>'
            . '<div><label for="field_conf_class">Clase</label><input type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '</div>',
            'showData' => 0,
            'type' => 'file',
        );
        return $field;
    }

    public function fileInputField($field) {

        $file = '';
        $config = $field->getConfigjson();
        $name = $field->getInputName(); //. ($config->cardinality !== 1 ? '[]' : '');

        $value = ($field->getValueTemp() !== NULL) ? explode("|", $field->getValueTemp()) : NULL;

        if (isset($value) && !empty($value)):

            for ($i = 0; $i < count($value); $i++):
                if ($value[$i] !== ""):
                    $nameFile = '';
                    $fileA = array();
                    $nameWExt='';
                    $nameFile='Inexistente';
                    if (is_file(__DIR__ . '/../../../' . $value[$i])):
                        $fileA = file(__DIR__ . '/../../../' . $value[$i]);
                        $nameFile = $fileA[0];
                        $nameWExt = explode('/',$value[$i]); 
                        $nameWExt=$nameWExt[count($nameWExt)-1];
                        $nameWExt = explode('.',$nameWExt);
                        $nameWExt=$nameWExt[count($nameWExt)-2];
                    endif;
                    $inp = '<div class="dataOld"><fieldset class="form-group"><div class="col-xs-10  col-lg-10 "><a class="medicalFormFile" href="#" target="_blank" data-f1="'.$field->getMedicalFormsFieldset()->getMedicalForm()->getFormName().'" data-f2="'.$nameWExt.'" >' . $nameFile . '</a><input type="hidden" data-filetodel="1" name="' . $field->getMedicalFormsFieldset()->getMedicalForm()->getFormName() . '[' . $field->getName() . '][]' . '" value="' . $value[$i] . '" /></div><div  class="col-xs-2 col-lg-2"><a class="elim a-mas-el mr-1" data-input-cha="dataOld' . $i . '" href="#" rel=".dataOld"><i class="icon-cross2"></i></a></div></fieldset></div>';
                    if (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid"):
                        $inp.= '<input type="file" accept="application/pdf,application/vnd.ms-excel,application/vnd.ms-excel,image/bmp,image/gif,image/jpeg,image/jpeg,image/jpeg,image/tiff" class="' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . ' form-input form-control form-file hidden dataOld' . $i . '"  id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . '  data-condx="' . (isset($config->condx) ? $config->condx : '') . '"/>';
                        $file .= '<div class="inp">' . $inp . '</div>';
                    else:
                        $file .=$inp;
                    endif;
                endif;

            endfor;
        endif;
        $fileb = "";

        if ($file === ''):
            $fileb=$file = '<fieldset class="form-group 1"><div class="col-xs-10  col-lg-10 "><label class="custom-file center-block block"><input type="file" accept="application/pdf,application/vnd.ms-excel,application/vnd.ms-excel,image/bmp,image/gif,image/jpeg,image/jpeg,image/jpeg,image/tiff" class="' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . ' form-input form-control form-file custom-file-input"  id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . '  data-condx="' . (isset($config->condx) ? $config->condx : '') . '" /> <span class="custom-file-control"></span></label></div></fieldset>';
        endif;

        if (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid"):
            return '<td class="colorTablaUno borderBlanco">' . $file . '</td>';
        else:
            if ($file === $fileb):
                return $file;
            else:
                return $file . '<fieldset class="form-group 3"><div class="col-xs-10  col-lg-10 "><label class="custom-file center-block block"><input type="file"  accept="application/pdf,application/vnd.ms-excel,application/vnd.ms-excel,image/bmp,image/gif,image/jpeg,image/jpeg,image/jpeg,image/tiff" class="' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . ' form-input form-control form-file custom-file-input"  id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . '  data-condx="' . (isset($config->condx) ? $config->condx : '') . '" /><span class="custom-file-control"></span></label></div></fieldset>';
            endif;
        endif;

        //return (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid") ? '<td class="colorTablaUno borderBlanco FILE">' . $file . '</td>' : $file;
    }

    public function dateField($ini = null) {
        $class = $condx = $placeholder = "";
        if ($ini !== null):
            if (isset($ini->placeholder) && $ini->placeholder != ''):
                $placeholder = $ini->placeholder;
            endif;
            if (isset($ini->condx) && $ini->condx != ''):
                $condx = $ini->condx;
            endif; 
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
        endif;
        $field = array(
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf_ph">Placeholder</label><input type="text" value="' . $placeholder . '" id="field_conf_ph" name="field_conf[placeholder]"></div>'
            . '<div><label for="field_conf_condx">Condicionado por</label><input type="text" value="' . $condx . '" id="field_conf_condx" name="field_conf[condx]"></div>'
            . '<div><label for="field_conf_class">Clase</label><input type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '</div>',
            'showData' => 0,
            'type' => 'date',
        );
        return $field;
    }

    public function dateInputField($field) {
        $name = $field->getInputName();
        $config = $field->getConfigjson();

        $date = '';
        $value = ($field->getValueTemp() !== NULL) ? explode("|", $field->getValueTemp()) : NULL;
        if (isset($value) && !empty($value)):
            for ($i = 0; $i < count($value); $i++):
                if ($value[$i] !== ""):

                    $inp = '<input class="form-input form-control datepicker ' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . '" data-provide="datepicker" data-date-end-date="0d" pattern="\d{1,2}-\d{1,2}-\d{4}" data-date-format="dd-mm-yyyy" value="' . $value[$i] . '" placeholder="' . (isset($config->placeholder) ? $config->placeholder : '') . '" type="text" id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . ' data-condx="' . (isset($config->condx) ? $config->condx : '') . '"/>';
                    if (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid"):
                        $date .= '<div class="inp">' . $inp . '</div>';
                    else:
                        $date .=$inp;
                    endif;
                endif;
            endfor;
        endif;

        if ($date === ''):
            $date = '<input class="form-input form-control datepicker ' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . '" data-provide="datepicker" data-date-end-date="0d" pattern="\d{1,2}-\d{1,2}-\d{4}"  data-date-format="dd-mm-yyyy" value="' . $field->getValueTemp() . '" placeholder="' . (isset($config->placeholder) ? $config->placeholder : '') . '" type="text" id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required="required"' : '') . ' data-condx="' . (isset($config->condx) ? $config->condx : '') . '"/>';
        endif;

        return (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid") ? '<td class="colorTablaUno borderBlanco">' . $date . '</td>' : $date;
    }

    public function countryField($ini = null) {
        $class = $condx ="";
        if ($ini !== null):
            if (isset($ini->condx) && $ini->condx != ''):
                $condx = $ini->condx;
            endif; 
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
        endif;
        
        $field = array(
            'showData' => 0,
            'type' => 'country',
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf_condx">Condicionado por</label><input type="text" value="' . $condx . '" id="field_conf_condx" name="field_conf[condx]"></div>'
            . '<div><label for="field_conf_class">Clase</label><input type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '</div>',
        );
        return $field;
    }

    public function countryInputField($field) {
        $name = $field->getInputName();
        $config = $field->getConfigjson();
        
        $country = '';
        $value = ($field->getValueTemp() !== NULL) ? explode("|", $field->getValueTemp()) : NULL;
        if (isset($value) && !empty($value)):
            for ($i = 0; $i < count($value); $i++):
                if ($value[$i] !== ""):
                    $inp = '<select class="form-input form-control bfh-countries ' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . '" data-country="' . $value[$i] . '" id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required' : '') . ' data-condx="' . (isset($config->condx) ? $config->condx : '') . '"></select>';
                    if (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid"):
                        $country .= '<div class="inp">' . $inp . '</div>';
                    else:
                        $country .=$inp;
                    endif;

                endif;
            endfor;
        else:
            $country = '<select class="form-input form-control bfh-countries ' . ((isset($config->condx) && $config->condx!=='') ? 'condx' : '') . '" data-country="' . $field->getValueTemp() . '" id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required' : '') . ' data-condx="' . (isset($config->condx) ? $config->condx : '') . '"></select>';
        endif;

        if ($country === ''):
            $country = '<select class="form-input form-control bfh-countries" data-country="' . $field->getValueTemp() . '" id="' . $field->getName() . '" name="' . $name . '" ' . ($field->getRequired() ? 'required' : '') . '></select>';
        endif;

        return (null !== $field->getSubgroup() && $field->getSubgroup()->getField() == "grid") ? '<td class="colorTablaUno borderBlanco">' . $country . '</td>' : $country;
    }

    public function groupField($ini = null) {

        $class = $condx = $class = "";
        if ($ini !== null):
            if (isset($ini->class)):
                $class = $ini->class;
            endif;
            if (isset($ini->condx) && $ini->condx != ''):
                $condx = $ini->condx;
            endif;
            
        endif;

        $field = array(
            'showData' => 0,
            'type' => 'group',
            'extra' => '<div class="_conf">'
            . '<div><label for="field_conf_class">Clase</label><input type="text" value="' . $class . '" id="field_conf_class" name="field_conf[class]"></div>'
            . '<div><label for="field_conf_condx">Condicionado por</label><input type="text" value="' . $condx . '" id="field_conf_condx" name="field_conf[condx]"></div>'
            . '</div>',
        );

        return $field;
    }

    public function getData($data) {

        if (!empty($data)):
            $data = explode("\n", $data);
            if (is_array($data)):
                for ($i = 0; $i < count($data); $i++):
                    $data[$i] = explode("|", $data[$i]);
                    $data[$i] =array_map('trim',$data[$i] );
                endfor;
            endif;
        endif;
        
        return $data;
    }

}
