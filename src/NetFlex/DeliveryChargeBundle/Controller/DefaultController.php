<?php

namespace NetFlex\DeliveryChargeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        return $this->render('NetFlexDeliveryChargeBundle:Default:index.html.twig');
    }
}
