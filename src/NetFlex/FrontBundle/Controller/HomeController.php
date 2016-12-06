<?php

namespace NetFlex\FrontBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class HomeController extends Controller
{
    /**
     * Renders NetFlex home page.
     *
     * @Route("/", name="home_page")
     * @Method({"GET"})
     *
     * @param  Request  $request A Request instance
     *
     * @return Response          A Response instance
     */
    public function renderHomePageAction(Request $request)
    {
        return $this->render('NetFlexFrontBundle:Home:home.html.twig', [
        	'pageTitle' => 'Home',
        ]);
    }
	
	/**
	 * Dummy logging out option.
	 *
	 * @Route("/logout", name="dummy_logout")
	 * @Method({"GET"})
	 *
	 * @param  Request  $request A Request instance
	 *
	 * @return Response          A Response instance
	 */
	public function logUserOutAction(Request $request)
	{
		$session = $request->getSession();
		
		if ($session->has('loggedInUsername')) {
			$session->remove('loggedInUsername');
		}
		
		return $this->redirectToRoute('net_flex_client_logout');
	}
}
