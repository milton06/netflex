<?php

namespace NetFlex\DeliveryChargeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class BookingController extends Controller
{
    /**
     * Renders the booking page.
     *
     * @Route("/dashboard/book-a-shipment/{clientId}", name="book_a_shipment_from_dashboard", requirements={"clientId": "\d+"})
     * @Method({"GET"})
     *
     * @param int     $clientId
     * @param Request $request A request instance
     *
     * @return Response
     */
    public function renderBookingPageInDashboardAction($clientId, Request $request)
    {
	    $em = $this->getDoctrine()->getManager();
	
	    /**
	     * Repositories.
	     */
	    $clientRepo = $em->getRepository('NetFlexUserBundle:User');
	    $clientAddressRepo = $em->getRepository('NetFlexUserBundle:Address');
	    
	    if (! $clientRepo->findUserExistence($clientId)) {
		    throw $this->createNotFoundException("No client with ID: $clientId exists");
	    }
	
	    /**
	     * Get client's preferred addresses.
	     */
	    $clientPreferredAddresses = $clientAddressRepo->findClientPreferredAddresses($clientId);
	    
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Client List',
			    'link' => $this->generateUrl('client_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Book A Shipment',
			    'link' => $this->generateUrl('book_a_shipment_from_dashboard', ['clientId' => $clientId], UrlGeneratorInterface::ABSOLUTE_URL)
		    ],
	    ];
	
	    $referrer = urldecode($request->query->get('ref'));
	
	    return $this->render('NetFlexDeliveryChargeBundle:Booking:book_a_shipment_from_dashboard.html.twig', [
		    'pageTitle' => 'Book A Shipment',
		    'breadCrumbs' => $breadCrumbs,
		    'referrer' => $referrer,
		    'pageHeader' => '<h1>Book <small>a shipment</small></h1>',
		    'clientPreferredAddresses' => $clientPreferredAddresses,
	    ]);
    }
}
