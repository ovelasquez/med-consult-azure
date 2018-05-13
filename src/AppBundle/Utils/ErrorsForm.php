<?php
use Symfony\Component\Form\Form;

namespace AppBundle\Utils;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ErrorsForm
 *
 * @author Mariana
 */
class ErrorsForm {
    //put your code here
    
    public function getErrors($form) {
        $errors = array();

        foreach ($form->getErrors() as $key => $error) {
            if ($form->isRoot()) {
                $errors['#'][] = $error->getMessage();
            } else {
                $errors[] = $error->getMessage();
            }
        }

        foreach ($form->all() as $child) {
            if (!$child->isValid()) {
                $errors[$child->getName()] = $this->getErrors($child);
            }
        }
        return $errors;
    }
}
