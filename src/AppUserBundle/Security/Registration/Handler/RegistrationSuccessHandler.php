<?php

namespace AppUserBundle\Security\Registration\Handler;

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Mailer\MailerInterface;
use FOS\UserBundle\Util\TokenGeneratorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use \FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\ORM\EntityManager;

class RegistrationSuccessHandler implements EventSubscriberInterface {

    private $userManager;
    protected $em;

    public function __construct(UserManagerInterface $userManager, EntityManager $em) {
        $this->userManager = $userManager;
        $this->em = $em;
    }

    public static function getSubscribedEvents() {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(FormEvent $event) {
        /** @var $user \FOS\UserBundle\Model\UserInterface */
        $user = $event->getForm()->getData();

        if (method_exists($user, 'getUser') && null !== $user->getUser()):
            $user = $user->getUser();
        endif;

        $form = $event->getRequest()->request->get('fos_user_registration_form');

        if ($form['_5'] === 'guess'):
            $shared = new \AppBundle\Entity\PatientsShareMedicalHistory();
            $user->setRoles(array("ROLE_GUESS"));
            $user->setEnabled(false);
            $shared = $this->em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $form['_2']));
            if ($shared !== null) :
                $shared->setEmail($form['email']);
                $this->em->flush();
            endif;
        endif;

        $this->userManager->updateUser($user);

//        echo"<pre>";
//        \Doctrine\Common\Util\Debug::dump($user);
//        echo"</pre>";
//        exit();
//        if (null === $user->getConfirmationToken()) {
//            $user->setConfirmationToken($this->tokenGenerator->generateToken());
//        }
//        
//        $this->mailer->sendConfirmationEmailMessage($user);
//
//        $this->session->set('fos_user_send_confirmation_email/email', $user->getEmail());
//
//        $url = $this->router->generate('fos_user_registration_check_email');
//        $event->setResponse(new RedirectResponse($url));
    }

}
