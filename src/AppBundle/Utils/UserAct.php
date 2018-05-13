<?php

namespace AppBundle\Utils;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Description of UserAct
 *
 * @author Mariana
 */
class UserAct {
    private $tokenStorage;
 
    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }
    
    public function getUserAct()
    {
        return $this->tokenStorage->getToken()->getUser();        
    }
}
