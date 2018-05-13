<?php

namespace AppUserBundle\Security\Resetting\Handler;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use \AppBundle\Entity\Patients;
use \AppBundle\Entity\Physicians;
use Doctrine\ORM\EntityManager;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class PasswordResettingHandler implements EventSubscriberInterface {

    private $router;
    protected $security;
    protected $em;

    public function __construct(UrlGeneratorInterface $router, SecurityContext $security, EntityManager $em) {
        $this->router = $router;
        $this->security = $security;
        $this->em = $em;
    }

    public static function getSubscribedEvents() {
        return [
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onPasswordResettingSuccess',
        ];
    }

    public function onPasswordResettingSuccess(FormEvent $event) {

        $url = $this->router->generate('homepage');
        $response = $event->setResponse(new RedirectResponse($url));        
        
        // retrieve user and session id
        $user = $event->getForm()->getData();
        $roles = $user->getRoles();

        //exit("<pre>".\Doctrine\Common\Util\Debug::dump(is_array($roles) && in_array('ROLE_PATIENT',$roles))."</pre>");

        if (is_array($roles) && in_array('ROLE_ADMIN',$roles)) {
            $response = new RedirectResponse($this->router->generate('admin_page'));
        } elseif (is_array($roles) && in_array('ROLE_PATIENT',$roles)) {
            $pat = new Patients();
            $pat = $this->em->getRepository('AppBundle:Patients')->findOneBy(array('user' => $user->getId()));

            if ($pat === null) {
                $response = new RedirectResponse($this->router->generate('patients_edit_front_fb', array("id" => $user->getId())));
            } else {
                $response = new RedirectResponse($this->router->generate('patient_view_front'));
            }
        } elseif (is_array($roles) && in_array('ROLE_PHYSICIANS',$roles)) {
            $phy = new Physicians();
            $phy = $this->em->getRepository('AppBundle:Physicians')->findOneBy(array('user' => $user->getId()));

            if ($phy !== null) {                
                $response = new RedirectResponse($this->router->generate('physicians_view_front', array("id" => $user->getId())));
            }
        }

        $event->setResponse($response);
    }

}
