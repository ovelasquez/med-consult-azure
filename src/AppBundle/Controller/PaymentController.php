<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Payment controller.
 *
 * @Route("/payment")
 */
class PaymentController extends Controller {

    /**
     * 
     * @Route("/{cons}", name="payment_prepare")
     */
    public function prepareAction($cons) {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Consultations')->find($cons);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }
         
        $gatewayName = 'paypal_express_checkout_and_doctrine_orm';

        $storage = $this->get('payum')->getStorage('AppBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setType($entity->getModalityConsultation()->getTag());
        $payment->setIdp($entity->getId());
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('USD');
        $payment->setTotalAmount($entity->getModalityConsultation()->getPrice()); // 1.23 EUR
        $payment->setDescription($entity->getModalityConsultation()->getName() . ' a través de http://medeconsult.com');
        $payment->setClientId($entity->getPatient()->getUser()->getId());
        $payment->setClientEmail($entity->getPatient()->getUser()->getEmail());

        $storage->update($payment);

        $captureToken = $this->get('payum.security.token_factory')->createCaptureToken(
                $gatewayName, $payment, $this->generateUrl('payment_done',array(),true) // the route to redirect after capture
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * 
     * @Route("/done/action", name="payment_done")
     * @Method("GET")
     * @Template()
     */
    public function doneAction(Request $request) {
        $token = $this->get('payum.security.http_request_verifier')->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // you can invalidate the token. The url could not be requested any more.
        //$this->get('payum.security.http_request_verifier')->invalidate($token);
        // Once you have token you can get the model from the storage directly. 
        //$identity = $token->getDetails();
        //$payment = $payum->getStorage($identity->getClass())->find($identity);
        // or Payum can fetch the model for you while executing a request (Preferred).
        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        // you have order and payment status 
        // so you can do whatever you want for example you can just print status and payment details.

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('AppBundle:Consultations')->find($payment->getIdp());

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Consultations entity.');
        }
        //var_dump($status->getValue()); die();
        if ($status->getValue()==="captured"){
            if ($entity->getModalityConsultation()->getTag() === 'inlive') {
                $entity->setStatus(0);
            } else {
                $entity->setStatus(1);
            }            
            
            $em->flush();
        }else{            
            $em->remove($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('consultations_list_patient'));
        }
        
//        echo"<pre>";
//        \Doctrine\Common\Util\Debug::dump($payment->getIdp());
//        echo"</pre>";
//        exit();
        
//        return new JsonResponse(array(
//            'status' => $status->getValue(),
//            'payment' => array(
//                'total_amount' => $payment->getTotalAmount(),
//                'currency_code' => $payment->getCurrencyCode(),
//                'details' => $payment->getDetails(),
//            ),
//        ));
        return array(
            'entity' => $entity,
            'patient' => $entity->getPatient(),
            'status' => $status->getValue(),
            'payment' => array(
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ),
        );
    }

    

}
