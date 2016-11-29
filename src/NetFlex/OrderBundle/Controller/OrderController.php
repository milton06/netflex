<?php

namespace NetFlex\OrderBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryModeTimeline;
use NetFlex\DeliveryChargeBundle\Form\CheckDeliverabilityType;
use NetFlex\OrderBundle\Entity\Item;
use NetFlex\OrderBundle\Entity\Price;
use NetFlex\OrderBundle\Entity\Address;
use NetFlex\OrderBundle\Entity\OrderTransaction;
use NetFlex\OrderBundle\Form\OrderForClientFromDashboardType;

class OrderController extends Controller
{
    /**
     * Renders the order list page for a client.
     *
     * @Route("/dashboard/client/orders/list/{clientId}/{page}/{sortColumn}/{sortOrder}", name="client_order_list", defaults={"page": 1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"clientId": "\d+", "page": "\d+", "sortColumn": "id|awb|invoice|orderstatus|paymentstatus|orderdate", "sortOrder": "asc|desc"})
     * @Method({"GET", "POST"})
     *
     * @param  int     $clientId
     * @param  Request $request   A request Instance
     *
     * @return Response           A response instance
     */
    public function renderClintOrderListPageAction($clientId, $page, $sortColumn, $sortOrder, Request $request)
    {
	    if (! ($client = $this->getDoctrine()->getManager()->getRepository('NetFlexUserBundle:User')->findOneById($clientId))) {
		    throw $this->createNotFoundException("No client with ID: $clientId exists");
	    }
	    
	    $orderRepo = $this->getDoctrine()->getManager()->getRepository('NetFlexOrderBundle:OrderTransaction');
	
	    $session = $request->getSession();
	    $paginationService = $this->get('pagination_service');
	    
	    $routeParameters = [
	    	'clientId' => $clientId,
		    'page' => $page,
		    'sortColumn' => $sortColumn,
		    'sortOrder' => $sortOrder,
	    ];
	    $routeExtraParameters = $request->query->all();
	    $backReferrer = ($request->query->get('ref')) ? urldecode($request->query->get('ref')) : $this->generateUrl('client_list');
	
	    $orderPaginationParams = $paginationService->getPaginationParameterValue('dashboard.order_list');
	
	    $limit = $orderPaginationParams['record_per_page'];
	    $neighbor = $orderPaginationParams['neighbor'];
	    $offset = $paginationService->getRecordOffset($page, $limit);
	
	    $sortColumn = $this->getSortColumn($sortColumn);
	    $sortOrder = strtoupper($sortOrder);
	
	    $awbNumber = (true === $session->has('awbNumber')) ? $session->get('awbNumber') : '';
	    $invoiceNumber = (true === $session->has('invoiceNumber')) ? $session->get('invoiceNumber') : '';
	    $orderStatus = (true === $session->has('orderStatus')) ? $session->get('orderStatus') : '';
	    $paymentStatus = (true === $session->has('paymentStatus')) ? $session->get('paymentStatus') : '';
	    $fromDate = (true === $session->has('fromDate')) ? $session->get('fromDate') : '';
	    $toDate = (true === $session->has('toDate')) ? $session->get('toDate') : '';
	
	    $searchForm = $this->createFormBuilder()
		    ->setAction($this->generateUrl('client_order_list', ['clientId' => $clientId, 'ref' => $backReferrer], UrlGeneratorInterface::ABSOLUTE_URL))
		    ->setMethod('POST')
		    ->add('awbNumber', TextType::class, [
		    	'data' => $awbNumber,
		    ])
		    ->add('invoiceNumber', TextType::class, [
			    'data' => $invoiceNumber,
		    ])
		    ->add('orderStatus', ChoiceType::class, [
			    'placeholder' => '-All-',
			    'choices' => $this->getParameter('order_statuses'),
			    'data' => $orderStatus,
		    ])
		    ->add('paymentStatus', ChoiceType::class, [
			    'placeholder' => '-All-',
			    'choices' => $this->getParameter('payment_statuses'),
			    'data' => $paymentStatus,
		    ])
		    ->add('fromDate', TextType::class, [
		    	'attr' => [
				    'placeholder' => 'From:',
			    ],
			    'data' => $fromDate,
		    ])
		    ->add('toDate', TextType::class, [
		    	'attr' => [
				    'placeholder' => 'To:',
			    ],
			    'data' => $toDate,
		    ])
		    ->getForm();
	
	    $searchForm->handleRequest($request);
	
	    if ($searchForm->isSubmitted()) {
		    $searchData = $searchForm->getData();
		
		    $session->set('awbNumber', $searchData['awbNumber']);
		    $session->set('invoiceNumber', $searchData['invoiceNumber']);
		    $session->set('orderStatus', $searchData['orderStatus']);
		    $session->set('paymentStatus', $searchData['paymentStatus']);
		    $session->set('fromDate', $searchData['fromDate']);
		    $session->set('toDate', $searchData['toDate']);
		
		    return $this->redirectToRoute('client_order_list', array_merge($routeParameters, $routeExtraParameters));
	    }
	    
	    $fromDateObject = (! $fromDate) ? null : (\DateTime::createFromFormat('d-m-Y H:i:s', "$fromDate 00:00:00"));
	    $toDateObject = (! $toDate) ? null : (\DateTime::createFromFormat('d-m-Y H:i:s', "$toDate 23:59:59"));
	
	    $orderCount = $orderRepo->countOrders($sortColumn, $sortOrder, $awbNumber, $invoiceNumber, $orderStatus, $paymentStatus, $fromDateObject, $toDateObject, $client);
	
	    $totalPageCount = $paginationService->getTotalPageCount($limit, $orderCount);
	
	    $orders = $orderRepo->findOrders($offset, $limit, $sortColumn, $sortOrder, $awbNumber, $invoiceNumber, $orderStatus, $paymentStatus, $fromDateObject, $toDateObject, $client);
	
	    $pageLinks = $paginationService->getPageLinks($page, $limit, $neighbor, $orderCount, $totalPageCount, 'client_order_list', $routeParameters, $routeExtraParameters);
	    
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
			    'title' => 'Client Orders',
			    'link' => $this->generateUrl('client_order_list', ['clientId' => $clientId], UrlGeneratorInterface::ABSOLUTE_URL),
		    ],
	    ];
	    
	    return $this->render('NetFlexOrderBundle:ClientOrder:list.html.twig', [
		    'pageTitle' => 'Client Order List',
		    'breadCrumbs' => $breadCrumbs,
		    'pageHeader' => '<h1>' . ($client->getFirstName() . (($client->getMidName()) ? ' ' . $client->getMidName() : '') . ' ' . $client->getLastName()) . ':  <small>order list </small></h1>',
		    'listHeader' => 'Client Order List',
		    'searchForm' => $searchForm->createView(),
		    'clientId' => $clientId,
		    'orderCount' => $orderCount,
		    'totalPageCount' => $totalPageCount,
		    'orders' => $orders,
		    'pageLinks' => $pageLinks,
		    'backReferrer' => $backReferrer,
		    'referrer' => $this->generateUrl('client_order_list', array_merge($routeParameters, $routeExtraParameters), UrlGeneratorInterface::ABSOLUTE_URL),
		    'allRouteParameters' => array_merge($routeParameters, $routeExtraParameters),
	    ]);
    }
	
	/**
	 * Renders the order list page.
	 *
	 * @Route("/dashboard/orders/list/{page}/{sortColumn}/{sortOrder}", name="order_list", defaults={"page": 1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"page": "\d+", "sortColumn": "id|awb|invoice|name|orderstatus|paymentstatus|orderdate", "sortOrder": "asc|desc"})
	 * @Method({"GET", "POST"})
	 *
	 * @param  Request $request   A request Instance
	 *
	 * @return Response           A response instance
	 */
	public function renderOrderListPageAction($page, $sortColumn, $sortOrder, Request $request)
	{
		$orderRepo = $this->getDoctrine()->getManager()->getRepository('NetFlexOrderBundle:OrderTransaction');
		
		$session = $request->getSession();
		$paginationService = $this->get('pagination_service');
		
		$routeParameters = [
			'page' => $page,
			'sortColumn' => $sortColumn,
			'sortOrder' => $sortOrder,
		];
		$routeExtraParameters = $request->query->all();
		
		$orderPaginationParams = $paginationService->getPaginationParameterValue('dashboard.order_list');
		
		$limit = $orderPaginationParams['record_per_page'];
		$neighbor = $orderPaginationParams['neighbor'];
		$offset = $paginationService->getRecordOffset($page, $limit);
		
		$sortColumn = $this->getSortColumn($sortColumn);
		$sortOrder = strtoupper($sortOrder);
		
		$awbNumber = (true === $session->has('awbNumber')) ? $session->get('awbNumber') : '';
		$invoiceNumber = (true === $session->has('invoiceNumber')) ? $session->get('invoiceNumber') : '';
		$name = (true === $session->has('name')) ? $session->get('name') : '';
		$orderStatus = (true === $session->has('orderStatus')) ? $session->get('orderStatus') : '';
		$paymentStatus = (true === $session->has('paymentStatus')) ? $session->get('paymentStatus') : '';
		$fromDate = (true === $session->has('fromDate')) ? $session->get('fromDate') : '';
		$toDate = (true === $session->has('toDate')) ? $session->get('toDate') : '';
		
		$searchForm = $this->createFormBuilder()
			->setAction($this->generateUrl('order_list', [], UrlGeneratorInterface::ABSOLUTE_URL))
			->setMethod('POST')
			->add('awbNumber', TextType::class, [
				'data' => $awbNumber,
			])
			->add('invoiceNumber', TextType::class, [
				'data' => $invoiceNumber,
			])
			->add('name', TextType::class, [
				'data' => $name,
			])
			->add('orderStatus', ChoiceType::class, [
				'placeholder' => '-All-',
				'choices' => $this->getParameter('order_statuses'),
				'data' => $orderStatus,
			])
			->add('paymentStatus', ChoiceType::class, [
				'placeholder' => '-All-',
				'choices' => $this->getParameter('payment_statuses'),
				'data' => $paymentStatus,
			])
			->add('fromDate', TextType::class, [
				'attr' => [
					'placeholder' => 'From:',
				],
				'data' => $fromDate,
			])
			->add('toDate', TextType::class, [
				'attr' => [
					'placeholder' => 'To:',
				],
				'data' => $toDate,
			])
			->getForm();
		
		$searchForm->handleRequest($request);
		
		if ($searchForm->isSubmitted()) {
			$searchData = $searchForm->getData();
			
			$session->set('awbNumber', $searchData['awbNumber']);
			$session->set('invoiceNumber', $searchData['invoiceNumber']);
			$session->set('name', $searchData['name']);
			$session->set('orderStatus', $searchData['orderStatus']);
			$session->set('paymentStatus', $searchData['paymentStatus']);
			$session->set('fromDate', $searchData['fromDate']);
			$session->set('toDate', $searchData['toDate']);
			
			return $this->redirectToRoute('order_list', array_merge($routeParameters, $routeExtraParameters));
		}
		
		$fromDateObject = (! $fromDate) ? null : (\DateTime::createFromFormat('d-m-Y H:i:s', "$fromDate 00:00:00"));
		$toDateObject = (! $toDate) ? null : (\DateTime::createFromFormat('d-m-Y H:i:s', "$toDate 23:59:59"));
		
		$orderCount = $orderRepo->countOrders($sortColumn, $sortOrder, $awbNumber, $invoiceNumber, $orderStatus, $paymentStatus, $fromDateObject, $toDateObject, null, $name);
		
		$totalPageCount = $paginationService->getTotalPageCount($limit, $orderCount);
		
		$orders = $orderRepo->findOrders($offset, $limit, $sortColumn, $sortOrder, $awbNumber, $invoiceNumber, $orderStatus, $paymentStatus, $fromDateObject, $toDateObject, null, $name);
		
		$pageLinks = $paginationService->getPageLinks($page, $limit, $neighbor, $orderCount, $totalPageCount, 'order_list', $routeParameters, $routeExtraParameters);
		
		$breadCrumbs = [
			[
				'title' => 'Dashboard Home',
				'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Order List',
				'link' => $this->generateUrl('order_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
		];
		
		return $this->render('NetFlexOrderBundle:Order:list.html.twig', [
			'pageTitle' => 'Order List',
			'breadCrumbs' => $breadCrumbs,
			'pageHeader' => '<h1>Order<small> list </small></h1>',
			'listHeader' => 'Order List',
			'searchForm' => $searchForm->createView(),
			'orderCount' => $orderCount,
			'totalPageCount' => $totalPageCount,
			'orders' => $orders,
			'pageLinks' => $pageLinks,
			'referrer' => $this->generateUrl('order_list', array_merge($routeParameters, $routeExtraParameters), UrlGeneratorInterface::ABSOLUTE_URL),
			'allRouteParameters' => array_merge($routeParameters, $routeExtraParameters),
		]);
	}
	
	/**
	 * Renders the edit order page.
	 *
	 * @Route("/dashboard/order/edit/{id}", name="edit_order")
	 * @Method({"GET", "POST"})
	 *
	 * @param OrderTransaction     $order   An OrderTransaction instance
	 * @param Request              $request A request instance
	 *
	 * @return Response
	 */
	public function renderEditOrderPageAction(OrderTransaction $order, Request $request)
	{
		$em = $this->getDoctrine()->getManager();
		
		/**
		 * Create the check deliverability form.
		 */
		$deliveryModeTimeline = $order->getDeliveryChargeId()->getDeliveryModeTimelineId();
		$checkDeliverabilityForm = $this->createForm(CheckDeliverabilityType::class, $deliveryModeTimeline);
		
		/**
		 * Create the order form.
		 */
		$orderForm = $this->createForm(OrderForClientFromDashboardType::class, $order);
		
		$orderForm->handleRequest($request);
		
		if ($orderForm->isSubmitted()) {
			if ((! $order->getOrderAddress()->getPickupAddressLine1()) || (! $order->getOrderAddress()->getBillingAddressLine1())) {
				/**
				 * We need to fetch pickup and billing addresses from user address repo.
				 */
				$clientAddressRepo = $em->getRepository('NetFlexUserBundle:Address');
				$clientPreferredPickupAndBillingAddresses = $clientAddressRepo->findClientPreferredPickupAndBillingAddresses($order->getUserId()->getId());
			}
			
			if (! $order->getOrderAddress()->getPickupAddressLine1()) {
				/**
				 * Populate pickup address fields.
				 */
				$order->getOrderAddress()->setPickupFirstName($order->getUserId()->getFirstName());
				$order->getOrderAddress()->setPickupMidName($order->getUserId()->getMidName());
				$order->getOrderAddress()->setPickupLastName($order->getUserId()->getLastName());
				$order->getOrderAddress()->setPickupAddressLine1($clientPreferredPickupAndBillingAddresses[1]->getAddressLine1());
				$order->getOrderAddress()->setPickupAddressLine2($clientPreferredPickupAndBillingAddresses[1]->getAddressLine2());
				$order->getOrderAddress()->setPickupCountryId($clientPreferredPickupAndBillingAddresses[1]->getCountryId());
				$order->getOrderAddress()->setPickupStateId($clientPreferredPickupAndBillingAddresses[1]->getStateId());
				$order->getOrderAddress()->setPickupCityId($clientPreferredPickupAndBillingAddresses[1]->getCityId());
				$order->getOrderAddress()->setPickupZipCode($clientPreferredPickupAndBillingAddresses[1]->getZipCode());
			}
			
			if (! $order->getOrderAddress()->getBillingAddressLine1()) {
				/**
				 * Populate billing address fields.
				 */
				$order->getOrderAddress()->setBillingFirstName($order->getUserId()->getFirstName());
				$order->getOrderAddress()->setBillingMidName($order->getUserId()->getMidName());
				$order->getOrderAddress()->setBillingLastName($order->getUserId()->getLastName());
				$order->getOrderAddress()->setBillingAddressLine1($clientPreferredPickupAndBillingAddresses[0]->getAddressLine1());
				$order->getOrderAddress()->setBillingAddressLine2($clientPreferredPickupAndBillingAddresses[0]->getAddressLine2());
				$order->getOrderAddress()->setBillingCountryId($clientPreferredPickupAndBillingAddresses[0]->getCountryId());
				$order->getOrderAddress()->setBillingStateId($clientPreferredPickupAndBillingAddresses[0]->getStateId());
				$order->getOrderAddress()->setBillingCityId($clientPreferredPickupAndBillingAddresses[0]->getCityId());
				$order->getOrderAddress()->setBillingZipCode($clientPreferredPickupAndBillingAddresses[0]->getZipCode());
			}
			
			$associatedCustomerRoles = $order->getUserId()->getRoles();
			if (in_array('ROLE_CLIENT', $associatedCustomerRoles)) {
				$order->setOrderStatus(2);
			}
			
			$order->getOrderItem()->setItemBaseWeight($order->getOrderItem()->getItemBaseWeight() - $order->getOrderItem()->getItemAccountableExtraWeight());
			$order->getLastModifiedBy(new \DateTime());
			$order->setLastModifiedBy($this->getUser()->getId());
			
			$em->persist($order);
			
			$em->flush();
			
			$orderId = $order->getId();
			
			return $this->json(['status' => 'success', 'orderId' => $orderId]);
		}
		
		$breadCrumbs = [
			[
				'title' => 'Dashboard Home',
				'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Order List',
				'link' => $this->generateUrl('order_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Edit Order',
				'link' => $this->generateUrl('edit_order', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL)
			],
		];
		
		$referrer = urldecode($request->query->get('ref'));
		
		return $this->render('NetFlexOrderBundle:Order:edit_order.html.twig', [
			'pageTitle' => 'Edit Order',
			'breadCrumbs' => $breadCrumbs,
			'referrer' => $referrer,
			'pageHeader' => '<h1>Edit <small>order</small></h1>',
			'checkDeliverabilityForm' => $checkDeliverabilityForm->createView(),
			'orderForm' => $orderForm->createView(),
		]);
	}
	
	/**
	 * Deletes an order.
	 *
	 * @Route("/dashboard/order/delete", name="delete_order")
	 * @Method({"GET", "DELETE"})
	 *
	 * @param Request $request A request instance
	 *
	 * @return RedirectResponse
	 */
	public function deleteClientAction(Request $request)
	{
		$orderId = $request->query->get('order_id');
		
		$allRouteParameters = $request->query->get('allRouteParameters');
		$pageIndex = (int) $allRouteParameters['page'];
		$selectedRecordCount = (int) $request->query->get('selectedRecordCount');
		$totalRecordOnPage = (int) $request->query->get('totalRecordOnPage');
		
		if (false === is_numeric($orderId)) {
			$orderIds = explode('-', $orderId);
			$undeletedOrderIds = [];
			
			foreach ($orderIds as $thisOrderId) {
				$orderDeletionResult = $this->deleteOrder((int) $thisOrderId);
				
				if (false === $orderDeletionResult['status']) {
					$undeletedOrderIds[] = $thisOrderId;
				}
			}
			
			if (empty($undeletedOrderIds)) {
				$this->addFlash('success', 'All orders were deleted successfully');
			} elseif (count($undeletedOrderIds) < count($orderIds)) {
				$this->addFlash('warning', 'Orders with ID: ' . implode(', ', $undeletedOrderIds) . ' could not be deleted');
			} else {
				$this->addFlash('error', 'Orders could not be deleted');
			}
			
			$allRouteParameters['page'] = $this->adjustPageIndex($pageIndex, ($selectedRecordCount - count($undeletedOrderIds)), $totalRecordOnPage);
		} else {
			$orderDeletionResult = $this->deleteOrder((int) $orderId);
			
			if (false === $orderDeletionResult['status']) {
				$this->addFlash('error', 'Order could not be deleted');
			} else {
				$this->addFlash('success', 'Order was deleted successfully');
			}
			
			$allRouteParameters['page'] = $this->adjustPageIndex($pageIndex, $selectedRecordCount, $totalRecordOnPage);
		}
		
		if (array_key_exists('clientId', $allRouteParameters)) {
			return $this->redirectToRoute('client_order_list', $allRouteParameters);
		} else {
			return $this->redirectToRoute('order_list', $allRouteParameters);
		}
	}
	
	/**
	 * Deletes a single order.
	 *
	 * @param int    $orderId
	 *
	 * @return array
	 */
	protected function deleteOrder($orderId)
	{
		$em = $this->getDoctrine()->getManager();
		$orderRepo = $em->getRepository('NetFlexOrderBundle:OrderTransaction');
		
		$thisOrder = $orderRepo->findOneById($orderId);
		
		$thisOrder->setOrderStatus(0);
		$thisOrder->setLastModifiedOn(new \DateTime());
		$thisOrder->setLastModifiedBy($this->getUser()->getId());
		
		$em->flush();
		
		return ['status' => true];
	}
	
	/**
	 * Adjusts redirection page index.
	 *
	 * @param int $pageIndex
	 * @param int $selectedRecordCount
	 * @param int $totalRecordOnPage
	 *
	 * @return int
	 */
	protected function adjustPageIndex($pageIndex, $selectedRecordCount, $totalRecordOnPage)
	{
		if (0 === $selectedRecordCount) {
			return $pageIndex;
		}
		
		if ($selectedRecordCount === $totalRecordOnPage) {
			$pageIndex = (1 === $pageIndex) ? 1 : ($pageIndex - 1);
		}
		
		return $pageIndex;
	}
	
	/**
	 * Gets the current sort column
	 *
	 * @param  string $sortColumn
	 *
	 * @return string
	 */
	protected function getSortColumn($sortColumn)
	{
		switch ($sortColumn) {
			case 'awb':
				$sortColumn = 'O.awbNumber';
				
				break;
			
			case 'invoice':
				$sortColumn = 'O.invoiceNumber';
				
				break;
			
			case 'name':
				$sortColumn = 'U.firstName';
				
				break;
			
			
			case 'orderstatus':
				$sortColumn = 'O.orderStatus';
				
				break;
			
			case 'paymentstatus':
				$sortColumn = 'O.paymentStatus';
				
				break;
			
			case 'orderdate':
				$sortColumn = 'O.createdOn';
				
				break;
			
			case 'id':
			default:
				$sortColumn = 'O.id';
				
				break;
		}
		
		return $sortColumn;
	}
	
	/**
	 * @Route("/dashboard/client/exit-from-order-search-mode", name="exit_from_client_order_search_mode")
	 *
	 * @param  Request $request
	 *
	 * @return RedirectResponse
	 */
	public function exitFromClientOrderSearchModeAction(Request $request)
	{
		$session = $request->getSession();
		$referrer = $request->query->get('ref');
		
		$session->remove('awbNumber');
		$session->remove('invoiceNumber');
		$session->remove('orderStatus');
		$session->remove('paymentStatus');
		$session->remove('fromDate');
		$session->remove('toDate');
		
		return $this->redirect($referrer);
	}
	
	/**
	 * @Route("/dashboard/order/exit-from-order-search-mode", name="exit_from_order_search_mode")
	 *
	 * @param  Request $request
	 *
	 * @return RedirectResponse
	 */
	public function exitFromOrderSearchModeAction(Request $request)
	{
		$session = $request->getSession();
		$referrer = $request->query->get('ref');
		
		$session->remove('awbNumber');
		$session->remove('invoiceNumber');
		$session->remove('name');
		$session->remove('orderStatus');
		$session->remove('paymentStatus');
		$session->remove('fromDate');
		$session->remove('toDate');
		
		return $this->redirect($referrer);
	}
}
