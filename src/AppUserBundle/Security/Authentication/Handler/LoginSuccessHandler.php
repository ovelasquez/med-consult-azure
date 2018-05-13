<?php

namespace AppUserBundle\Security\Authentication\Handler;

use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Router;
use \AppBundle\Entity\Patients;
use Doctrine\ORM\EntityManager;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface {

    protected $router;
    protected $security;
    protected $em;

    public function __construct(Router $router, SecurityContext $security, EntityManager $em) {
        $this->router = $router;
        $this->security = $security;
        $this->em = $em;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {

        $response = null;

        // retrieve user and session id
        $user = $token->getUser();

        if (!empty($request->request->get('_target_path'))):
            $referer = $request->request->get('_target_path');
        else:
            $referer = '';
        endif;

        

        if ($this->security->isGranted('ROLE_ADMIN')) {
            $response = new RedirectResponse($this->router->generate('admin_page'));
        } elseif ($this->security->isGranted('ROLE_PATIENT')) {
            $pat = new Patients();
            $pat = $this->em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user->getId()));
                        
            $request->getSession()->set('userInfo', $pat);
            
            if ($pat === null) {            
                $response = new RedirectResponse($this->router->generate('patients_edit_front_fb', array("id" => $user->getId())));
            } elseif ($referer === '') {            
                $response = new RedirectResponse($this->router->generate('patient_view_front'));
            } else {            
                $response = new RedirectResponse($referer);
            } 
            
        } elseif ($this->security->isGranted('ROLE_PHYSICIANS')) {
            
            $request->getSession()->set('userInfo', $this->em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user->getId())));
            
            if ($referer === '') {
                $response = new RedirectResponse($this->router->generate('physicians_view_front', array("id" => $user->getId())));
            } else {
                $response = new RedirectResponse($referer);
            }
        } elseif ($this->security->isGranted('ROLE_GUESS')) {
            $response = new RedirectResponse($this->router->generate('patientssharemedicalhistory_guess'));
        } else {
            $response = new RedirectResponse($this->router->generate('homepage'));
        }


        return $response;
    }

}
