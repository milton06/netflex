<?php

namespace NetFlex\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class DashboardController extends Controller
{
    /**
     * Render the dashboard home page.
     *
     * @Route("/dashboard/home", name="dashboard")
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
	        'pageHeader' => '<h1>Dashboard <small>home </small></h1>',
        ]);
    }
}
