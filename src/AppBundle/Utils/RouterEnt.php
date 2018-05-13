<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
namespace AppBundle\Utils;
/**
 * Description of RouterEnt
 *
 * @author Mariana
 */
class RouterEnt {

    
    private $router;

    public function __construct($router) {
        $this->router = $router;
    }

    public function generateUrl($route, $parameters = array(), $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH) {
        return $this->router->generate($route, $parameters, $referenceType);
    }

}
