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
	 * Renders a NetFlex CMS page.
	 *
	 * @Route("/{cmsPageSlug}", name="cms_page", requirements={"cmsPageSlug": "[a-zA-z0-9_\-]+"})
	 * @Method({"GET"})
	 *
	 * @param  string  $cmsPageSlug
	 * @param  Request $request A Request instance
	 *
	 * @return Response          A Response instance
	 */
	public function renderCMSPageAction($cmsPageSlug, Request $request)
	{
		$cmsPageSlug = trim($cmsPageSlug);
		
		$pageSlugDataMap = [
			'about-us' => [
				'title' => 'About Netflex',
				'pageHeader' => 'About Netflex',
			],
			'career' => [
				'title' => 'Career With Netflex',
				'pageHeader' => 'Career With Netflex',
			],
			'contact' => [
				'title' => 'Contact Netflex',
				'pageHeader' => 'Contact Netflex',
			],
			'customer-enquiry' => [
				'title' => 'Customer Enquiry',
				'pageHeader' => 'Customer Enquiry',
			],
			'client' => [
				'title' => 'Netflex Clientele',
				'pageHeader' => 'Netflex Clientele',
			],
			'franchisee' => [
				'title' => 'Netflex Franchisee',
				'pageHeader' => 'Netflex Franchisee',
			],
		];
		
		if (false === array_key_exists($cmsPageSlug, $pageSlugDataMap)) {
			throw $this->createNotFoundException('Page Not Found');
		}
		
		return $this->render('NetFlexFrontBundle:Home:page.html.twig', [
			'pageTitle' => $pageSlugDataMap[$cmsPageSlug]['title'],
			'pageHeader' => $pageSlugDataMap[$cmsPageSlug]['pageHeader'],
		]);
	}
	
	/**
	 * Dummy logging out option.
	 *
	 * @Route("/client/dummy-logout", name="dummy_logout")
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
