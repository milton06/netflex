<?php

namespace NetFlex\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use NetFlex\PaymentBundle\Entity\PaymentStatus;
use NetFlex\PaymentBundle\Form\PaymentStatusType;

/**
 * Paymentstatus controller.
 *
 * @Route("/")
 */
class PaymentStatusController extends Controller
{
	/**
	 * Lists all paymentStatus entities.
	 *
	 * @Route("/dashboard/payment-status/list", name="dashboard_payment_status_list")
	 * @Method("GET")
	 */
	public function dashboardPaymentStatusListAction()
	{
		$em = $this->getDoctrine()->getManager();
		
		$paymentStatuses = $em->getRepository('NetFlexPaymentBundle:PaymentStatus')->findAll();
		
		$breadCrumbs = [
			[
				'title' => 'Dashboard Home',
				'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Payment Status List',
				'link' => $this->generateUrl('dashboard_payment_status_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
		];
		
		return $this->render('NetFlexPaymentBundle:PaymentStatus:list.html.twig', [
			'pageTitle' => 'Payment Status List',
			'breadCrumbs' => $breadCrumbs,
			'pageHeader' => '<h1>Payment<small> status list </small></h1>',
			'listHeader' => 'Payment Status List',
			'paymentStatuses' => $paymentStatuses,
		]);
	}
	
	/**
	 * Displays a form to edit an existing paymentStatus entity.
	 *
	 * @Route("/dashboard/payment-status/{id}/edit", name="dashboard_payment_status_edit")
	 * @Method({"GET", "POST"})
	 */
	public function editPaymentStatusAction(Request $request, PaymentStatus $paymentStatus)
	{
		$editForm = $this->createForm(PaymentStatusType::class, $paymentStatus);
		$editForm->handleRequest($request);
		
		if ($editForm->isSubmitted() && $editForm->isValid()) {
			$this->getDoctrine()->getManager()->flush();
			
			$this->addFlash('success', 'Payment status has been updated');
			
			return $this->redirectToRoute('dashboard_payment_status_edit', [
				'id' => $paymentStatus->getId(),
			]);
		}
		
		$breadCrumbs = [
			[
				'title' => 'Dashboard Home',
				'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Payment Status List',
				'link' => $this->generateUrl('dashboard_payment_status_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Edit Payment Status',
				'link' => $this->generateUrl('dashboard_payment_status_edit', ['id' => $paymentStatus->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
			],
		];
		
		return $this->render('NetFlexPaymentBundle:PaymentStatus:edit.html.twig', [
			'pageTitle' => 'Edit Payment Status',
			'breadCrumbs' => $breadCrumbs,
			'pageHeader' => '<h1>Edit<small> payment status </small></h1>',
			'listHeader' => 'Edit Payment Status',
			'paymentStatus' => $paymentStatus,
			'editForm' => $editForm->createView(),
		]);
	}
}
