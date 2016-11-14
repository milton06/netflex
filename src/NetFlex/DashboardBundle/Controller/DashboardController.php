<?php

namespace NetFlex\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DashboardController extends Controller
{
    /**
     * @Route("/dashboard", name="dashboard")
     * @Method({"GET"})
     */
    public function renderDashboardHomeAction(Request $request)
    {
        $breadCrumbs = [
        	[
        		'title' => 'Dashboard Home',
		        'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
	        ]
        ];
	    
	    return $this->render('NetFlexDashboardBundle:Dashboard:home.html.twig', [
        	'pageTitle' => 'Dashboard Home',
		    'breadCrumbs' => $breadCrumbs,
	        'pageHeader' => 'Dashboard Home',
        ]);
    }
}
