<?php

namespace NetFlex\ShipmentTrackBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use NetFlex\ShipmentTrackBundle\Entity\OrderShipmentTrackRecord;
use NetFlex\ShipmentTrackBundle\Form\OrderShipmentTrackRecordAddType;
use NetFlex\ShipmentTrackBundle\Form\OrderShipmentTrackRecordEditType;

/**
 * Ordershipmenttrackrecord controller.
 *
 * @Route("dashboard/shipment-tracking/track-order")
 */
class OrderShipmentTrackRecordController extends Controller
{
    /**
     * Lists all orderShipmentTrackRecord entities for an orderTransaction entity.
     *
     * @Route("/{id}", name="dashboard_order_shipment_track_list", requirements={"id": "\d+"})
     * @Method("GET")
     *
     * @param  int     $id
     * @param  Request $request
     *
     * @return Response
     */
    public function renderDashboardOrderShipmentTrackListAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $orderShipmentTrackRecords = $em->getRepository('NetFlexShipmentTrackBundle:OrderShipmentTrackRecord')->findTrackRecordForAnOrder($id);
	
	    $referrer = ($request->query->has('ref')) ? urldecode($request->query->get('ref')) : '';
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Order Track Record',
			    'link' => $this->generateUrl('dashboard_order_shipment_track_list', ['id' => $id, 'ref' => $referrer], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
	    ];
	
	    return $this->render('NetFlexShipmentTrackBundle:OrderShipmentTrackRecord:list.html.twig', [
		    'pageTitle' => 'Order Track Record',
		    'breadCrumbs' => $breadCrumbs,
		    'orderId' => $id,
		    'backReferrer' => $referrer,
		    'referrer' => urlencode($this->generateUrl('dashboard_order_shipment_track_list', ['id' => $id, 'ref' => $referrer], UrlGeneratorInterface::ABSOLUTE_URL)),
		    'pageHeader' => '<h1>Order<small> track record </small></h1>',
		    'listHeader' => 'Order Track Record',
		    'orderShipmentTrackRecords' => $orderShipmentTrackRecords,
	    ]);
    }

    /**
     * Creates a new orderShipmentTrackRecord entity.
     *
     * @Route("/{id}/new", name="dashboard_order_shipment_track_record_new", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param int     $id
     * @param Request $request
     *
     * @return Response
     */
    public function addNewOrderShipmentTrackRecordAction($id, Request $request)
    {
	    $referrer = ($request->query->has('ref')) ? urldecode($request->query->get('ref')) : '';
	    
	    $orderShipmentTrackRecord = new OrderShipmentTrackRecord();
        $form = $this->createForm(OrderShipmentTrackRecordAddType::class, $orderShipmentTrackRecord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
	        
	        $order = $em->getRepository('NetFlexOrderBundle:OrderTransaction')->findOneById($id);
	        if (! $order) {
		        $this->addFlash('error', 'Server error encountered');
		        
		        return $this->redirectToRoute('dashboard_order_shipment_track_record_new', ['id' => $id, 'ref' => $referrer]);
	        }
	
	        $currentDateTime = new \DateTime();
	        
	        $orderShipmentTrackRecord->setOrderId($order);
	        $orderShipmentTrackRecord->setCreatedOn($currentDateTime);
	        $orderShipmentTrackRecord->setCreatedBy($this->getUser());
	        $orderShipmentTrackRecord->setLastModifiedOn($currentDateTime);
	        $orderShipmentTrackRecord->setLastModifiedBy($this->getUser());
	        
            $em->persist($orderShipmentTrackRecord);
            $em->flush($orderShipmentTrackRecord);
	        
	        $id = $orderShipmentTrackRecord->getId();

            return $this->redirectToRoute('dashboard_order_shipment_track_record_edit', ['id' => $id, 'ref' => $referrer]);
        }
        
        $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Order Track Record',
			    'link' => $referrer,
		    ],
	        [
		        'title' => 'Add New Track Status',
		        'link' =>  $this->generateUrl('dashboard_order_shipment_track_record_new', ['id' => $id, 'ref' => $referrer], UrlGeneratorInterface::ABSOLUTE_URL),
	        ],
	    ];
	    
        return $this->render('NetFlexShipmentTrackBundle:OrderShipmentTrackRecord:new.html.twig', [
	        'pageTitle' => 'Add New Track Record',
	        'breadCrumbs' => $breadCrumbs,
	        'referrer' => $referrer,
	        'pageHeader' => '<h1>Add New<small> track record </small></h1>',
	        'listHeader' => 'Add New Track Record',
        	'orderShipmentTrackRecord' => $orderShipmentTrackRecord,
	        'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing orderShipmentTrackRecord entity.
     *
     * @Route("/{id}/edit", name="dashboard_order_shipment_track_record_edit", requirements={"id": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param int                      $id
     * @param Request                  $request
     * @param OrderShipmentTrackRecord $orderShipmentTrackRecord
     *
     * @return Response
     */
    public function editOrderShipmentTrackRecordAction($id, Request $request, OrderShipmentTrackRecord $orderShipmentTrackRecord)
    {
	    $referrer = ($request->query->has('ref')) ? urldecode($request->query->get('ref')) : '';
	    
	    $form = $this->createForm(OrderShipmentTrackRecordEditType::class, $orderShipmentTrackRecord);
	    $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
	        $em = $this->getDoctrine()->getManager();
	        
	        $orderShipmentTrackRecord->setLastModifiedOn(new \DateTime());
	        $orderShipmentTrackRecord->setLastModifiedBy($this->getUser());
	        
	        $em->persist($orderShipmentTrackRecord);
            $em->flush();
	        
	        $this->addFlash('success', 'Order track record updated successfully');

            return $this->redirectToRoute('dashboard_order_shipment_track_record_edit', ['id' => $orderShipmentTrackRecord->getId(), 'ref' => $referrer]);
        }
	
	    $breadCrumbs = [
		    [
			    'title' => 'Dashboard Home',
			    'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
		    [
			    'title' => 'Order Track Record',
			    'link' => $referrer,
		    ],
		    [
			    'title' => 'Edit Track Record',
			    'link' => $this->generateUrl('dashboard_order_shipment_track_record_edit', ['id' => $id, 'ref' => $referrer], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
	    ];
	
	    return $this->render('NetFlexShipmentTrackBundle:OrderShipmentTrackRecord:edit.html.twig', [
		    'pageTitle' => 'Edit Track Record',
		    'breadCrumbs' => $breadCrumbs,
		    'referrer' => $referrer,
		    'pageHeader' => '<h1>Edit<small> track record </small></h1>',
		    'listHeader' => 'Edit Track Record',
		    'orderShipmentTrackRecord' => $orderShipmentTrackRecord,
		    'form' => $form->createView(),
	    ]);
    }

    /**
     * Deletes a orderShipmentTrackRecord entity.
     *
     * @Route("/{id}", name="dashboard_shipment-tracking_track-order_delete")
     * @Method("DELETE")
     */
    /*public function deleteAction(Request $request, OrderShipmentTrackRecord $orderShipmentTrackRecord)
    {
        $form = $this->createDeleteForm($orderShipmentTrackRecord);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($orderShipmentTrackRecord);
            $em->flush($orderShipmentTrackRecord);
        }

        return $this->redirectToRoute('dashboard_shipment-tracking_track-order_index');
    }*/

    /**
     * Creates a form to delete a orderShipmentTrackRecord entity.
     *
     * @param OrderShipmentTrackRecord $orderShipmentTrackRecord The orderShipmentTrackRecord entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    /*private function createDeleteForm(OrderShipmentTrackRecord $orderShipmentTrackRecord)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dashboard_shipment-tracking_track-order_delete', array('id' => $orderShipmentTrackRecord->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }*/
}
