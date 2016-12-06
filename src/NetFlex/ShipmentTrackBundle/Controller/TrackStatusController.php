<?php

namespace NetFlex\ShipmentTrackBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use NetFlex\ShipmentTrackBundle\Entity\TrackStatus;
use NetFlex\ShipmentTrackBundle\Form\TrackStatusType;

/**
 * Trackstatus controller.
 *
 * @Route("dashboard/shipment-tracking")
 */
class TrackStatusController extends Controller
{
    /**
     * Lists all trackStatus entities.
     *
     * @Route("/list", name="dashboard_shipment_track_status_list")
     * @Method("GET")
     */
    public function dashboardShipmentTrackStatusListAction()
    {
        $em = $this->getDoctrine()->getManager();

        $trackStatuses = $em->getRepository('NetFlexShipmentTrackBundle:TrackStatus')->findAll();
	
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Shipment Track Status List',
			    'link' => $this->generateUrl('dashboard_shipment_track_status_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
	    ];

        return $this->render('NetFlexShipmentTrackBundle:TrackStatus:list.html.twig', [
	        'pageTitle' => 'Shipment Track Status List',
	        'breadCrumbs' => $breadCrumbs,
	        'pageHeader' => '<h1>Shipment<small> track status list </small></h1>',
	        'listHeader' => 'Shipment Track Status List',
	        'trackStatuses' => $trackStatuses,
        ]);
    }

    /**
     * Displays a form to edit an existing trackStatus entity.
     *
     * @Route("/{id}/edit", name="dashboard_shipment_track_status_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, TrackStatus $trackStatus)
    {
        $editForm = $this->createForm(TrackStatusType::class, $trackStatus);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
			
	        $this->addFlash('success', 'Track status has been updated');
	        
            return $this->redirectToRoute('dashboard_shipment_track_status_edit', [
            	'id' => $trackStatus->getId(),
            ]);
        }
	
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Shipment Track Status List',
			    'link' => $this->generateUrl('dashboard_shipment_track_status_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
		        'title' => 'Edit Shipment Track Status',
			    'link' => $this->generateUrl('dashboard_shipment_track_status_edit', ['id' => $trackStatus->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
	    ];
        
        return $this->render('NetFlexShipmentTrackBundle:TrackStatus:edit.html.twig', [
	        'pageTitle' => 'Edit Shipment Track Status',
	        'breadCrumbs' => $breadCrumbs,
	        'pageHeader' => '<h1>Edit<small> shipment track status </small></h1>',
	        'listHeader' => 'Edit Shipment Track Status',
	        'trackStatus' => $trackStatus,
	        'editForm' => $editForm->createView(),
        ]);
    }
}
