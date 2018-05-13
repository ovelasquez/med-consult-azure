<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppUserBundle\EventListener;

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

class EmailConfirmationListener implements EventSubscriberInterface {

    private $mailer;
    private $tokenGenerator;
    private $router;
    private $session;
    private $userManager;
    private $em;

    public function __construct(UserManagerInterface $userManager, MailerInterface $mailer, TokenGeneratorInterface $tokenGenerator, UrlGeneratorInterface $router, SessionInterface $session, EntityManager $em) {
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->router = $router;
        $this->session = $session;
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

        if ($form['_5'] === base64_encode('guess')):
            $user->setRoles(array("ROLE_GUESS"));            
            $shared = $this->em->getRepository('AppBundle:PatientsShareMedicalHistory')->findOneBy(array("token" => $form['_2']));
            if ($shared !== null) :
                $shared->setEmail($form['email']);
                $this->em->flush();
            endif;
        endif;

        //$user->setEnabled(false);
        $this->userManager->updateUser($user);

        $roles = $user->getRoles();

        if (is_array($roles) && (in_array('ROLE_PATIENT', $roles) || in_array('ROLE_GUESS', $roles) )):
            $user->setEnabled(false);
            $this->userManager->updateUser($user);
            
            if (null === $user->getConfirmationToken()) {
                $user->setConfirmationToken($this->tokenGenerator->generateToken());
            }

            $this->mailer->sendConfirmationEmailMessage($user);

            $this->session->set('fos_user_send_confirmation_email/email', $user->getEmail());

            $url = $this->router->generate('fos_user_registration_check_email');
            $event->setResponse(new RedirectResponse($url));

        else:
            $url = $this->router->generate('homepage');
            $response = $event->setResponse(new RedirectResponse($url));
        endif;
    }

}
