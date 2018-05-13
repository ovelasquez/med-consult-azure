<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Promotion;
//use AppBundle\Form\ContentsType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * Promotion controller.
 *
 * @Route("/promotion")
 */
class PromotionController extends Controller
{

    /**
     * Lists all Contents entities.
     *
     * @Route("/", name="promotion")
     * @Method("GET")
     * @Template()
     * @Security("has_role('ROLE_ADMIN')") 
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Promotion')->findAll();

        $uses = array();
        
        for ($i = 0; $i < count($entities); $i++) {
            $uses[] = $em->getRepository('AppBundle:PromotionLog')->findby(array("promotion"=>$entities[$i]));
        }
        
        return array(
            'entities' => $entities,
            'uses' => $uses,
        );
    }
    
    
    
    
}
