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
		
		$associatedCustomerRoles = $order->getUserId()->getRoles();
		if (in_array('ROLE_CLIENT', $associatedCustomerRoles)) {
			/**
			 * Repositories.
			 */
			$clientAddressRepo = $em->getRepository('NetFlexUserBundle:Address');
			
			/**
			 * Get client's pickup and billing addresses.
			 */
			$clientPickupAndBillingAddresses = $clientAddressRepo->findClientPreferredPickupAndBillingAddresses($order->getUserId()->getId());
			if (0 < $clientPickupAndBillingAddresses[0]['billingAddressCount']) {
				$clientBillingAddressCount = $clientPickupAndBillingAddresses[0]['billingAddressCount'];
				unset($clientPickupAndBillingAddresses[0]);
				list($clientBillingAddresses, $clientPickupAddresses) = array_chunk($clientPickupAndBillingAddresses, $clientBillingAddressCount);
			}
		}
		
		/**
		 * Create the check deliverability form.
		 */
		$deliveryModeTimeline = $order->getDeliveryChargeId()->getDeliveryModeTimelineId();
		$checkDeliverabilityForm = $this->createForm(CheckDeliverabilityType::class, $deliveryModeTimeline);
		
		$clientOtherPickupAddresses = $clientOtherBillingAddresses = [];
		if (isset($clientPickupAddresses) && ! empty($clientPickupAddresses)) {
			/**
			 * Generate client's other pickup addresses list.
			 */
			for ($i = 0; $i < count($clientPickupAddresses); $i++) {
				$clientOtherPickupAddresses[$clientPickupAddresses[$i][0]->getAddressLine1() . '; ' . $clientPickupAddresses[$i][0]->getCountryId()->getName() . '; ' . $clientPickupAddresses[$i][0]->getStateId()->getName() . '; ' . $clientPickupAddresses[$i][0]->getCityId()->getName() . '; ' . $clientPickupAddresses[$i][0]->getZipCode()] = $clientPickupAddresses[$i][0]->getId();
			}
		}
		if (isset($clientBillingAddresses) && ! empty($clientBillingAddresses)) {
			/**
			 * Generate client's other billing addresses list.
			 */
			for ($i = 0; $i < count($clientBillingAddresses); $i++) {
				$clientOtherBillingAddresses[$clientBillingAddresses[$i][0]->getAddressLine1() . '; ' . $clientBillingAddresses[$i][0]->getCountryId()->getName() . '; ' . $clientBillingAddresses[$i][0]->getStateId()->getName() . '; ' . $clientBillingAddresses[$i][0]->getCityId()->getName() . '; ' . $clientBillingAddresses[$i][0]->getZipCode()] = $clientBillingAddresses[$i][0]->getId();
			}
		}
		
		/**
		 * Create the order form.
		 */
		$orderForm = $this->createForm(OrderForClientFromDashboardType::class, $order, [
			'clientOtherPickupAddresses' => $clientOtherPickupAddresses,
			'clientOtherBillingAddresses' => $clientOtherBillingAddresses,
		]);
		
		$orderForm->handleRequest($request);
		
		if ($orderForm->isSubmitted()) {
			/**
			 * Populate mandatory pickup address fields on absence.
			 */
			if (! $order->getOrderAddress()->getPickupFirstName()) {
				$order->getOrderAddress()->setPickupFirstName($order->getUserId()->getFirstName());
			}
			if (! $order->getOrderAddress()->getPickupMidName()) {
				$order->getOrderAddress()->getPickupMidName($order->getUserId()->getMidName());
			}
			if (! $order->getOrderAddress()->getPickupLastName()) {
				$order->getOrderAddress()->setPickupLastName($order->getUserId()->getLastName());
			}
			if (! $order->getOrderAddress()->getPickupAddressLine1()) {
				$order->getOrderAddress()->setPickupAddressLine1('Not Given');
			}
			if (! $order->getOrderAddress()->getPickupContactNumber()) {
				$contact = $em->getRepository('NetFlexUserBundle:Contact')->findUserContact($order->getUserId()->getId());
				if (! $contact) {
					$order->getOrderAddress()->setPickupContactNumber('Not Found');
				} else {
					$order->getOrderAddress()->setPickupContactNumber($contact['contactNumber']);
				}
			}
			if (! $order->getOrderAddress()->getPickupZipCode()) {
				$order->getOrderAddress()->setPickupZipCode('Not Given');
			}
			
			/**
			 * Validate.
			 */
			$errors = $this->get('validator')->validate($orderForm);
			if (0 < count($errors)) {
				$errorMessages = [];
				foreach($errors as $error) {
					$field = substr($error->getPropertyPath(), (strrpos($error->getPropertyPath(), '.') + 1));
					$field = preg_replace_callback('/[A-Z0-9]/', function($matches) {
						return '-' . strtolower($matches[0]);
					}, $field);
					$errorMessages[$field] = $error->getMessage();
				}
				return $this->json(['status' => 'validationErrors', 'errorMessages' => $errorMessages]);
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
			$awbNumber = $order->getAwbNumber();
			
			return $this->json(['status' => 'success', 'orderId' => $orderId, 'awbNumber' => $awbNumber]);
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
			'order' => $order,
			'userId' => $order->getUserId()->getId(),
			'checkDeliverabilityForm' => $checkDeliverabilityForm->createView(),
			'orderForm' => $orderForm->createView(),
		]);
	}
	
	/**
	 * Renders the view order page.
	 *
	 * @Route("/dashboard/order/view/{id}", name="view_order")
	 * @Method({"GET"})
	 *
	 * @param OrderTransaction     $order   An OrderTransaction instance
	 * @param Request              $request A request instance
	 *
	 * @return Response
	 */
	public function renderViewOrderPageAction(OrderTransaction $order, Request $request)
	{
		$orderDetails = $this->getDoctrine()->getManager()->getRepository('NetFlexOrderBundle:OrderTransaction')->findOrderDetailsForView($order->getId());
		
		$referrer = urldecode($request->query->get('ref'));
		
		$breadCrumbs = [
			[
				'title' => 'Dashboard Home',
				'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
			],
			[
				'title' => 'Order List',
				'link' => $referrer,
			],
			[
				'title' => 'View Order',
				'link' => $this->generateUrl('view_order', ['id' => $order->getId()], UrlGeneratorInterface::ABSOLUTE_URL),
			],
		];
		
		return $this->render('NetFlexOrderBundle:Order:view.html.twig', [
			'pageTitle' => 'View Order Details',
			'breadCrumbs' => $breadCrumbs,
			'pageHeader' => '<h1>View  <small>order details </small></h1>',
			'listHeader' => 'View Order Details',
			'referrer' => $referrer,
			'orderDetails' => $orderDetails,
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
	public function deleteOrderAction(Request $request)
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
	
	/**
	 * Toggles payment status for an order.
	 *
	 * @Route("/dashboard/order/toggle-payment-status", name="toggle_order_payment_status")
	 * @Method({"GET"})
	 *
	 * @param Request $request A request instance
	 *
	 * @return RedirectResponse
	 */
	public function toggleOrderPaymentStatusAction(Request $request)
	{
		$orderId = $request->query->get('id');
		$referrer = $request->query->get('ref');
		
		if (false === strpos($orderId, '-')) {
			if (false === $this->toggleOrderPaymentStatus($orderId)) {
				$this->addFlash('error', 'Payment status couldn\'t be changed');
			} else {
				$this->addFlash('success', 'Payment status has been changed successfully');
			}
		} else {
			$orderIds = explode('-', $orderId);
			$paymentStatusToggleResults = [];
			foreach ($orderIds as $orderId) {
				if (false === $this->toggleOrderPaymentStatus($orderId)) {
					$paymentStatusToggleResults[] = $orderId;
				}
			}
			if ($paymentStatusToggleResults) {
				if (count($orderIds) === count($paymentStatusToggleResults)) {
					$this->addFlash('error', 'Payment statuses couldn\'t be changed');
				} else {
					$this->addFlash('warning', 'Payment statuses for order IDs: ' . implode(', ', $paymentStatusToggleResults) . ' couldn\'t be changed');
				}
			} else {
				$this->addFlash('success', 'Payment statuses have been changed successfully');
			}
		}
		
		return $this->redirect($referrer);
	}
	
	protected function toggleOrderPaymentStatus($orderId)
	{
		$em = $this->getDoctrine()->getManager();
		
		$order = $em->getRepository('NetFlexOrderBundle:OrderTransaction')->findOneById($orderId);
		if (! $order) {
			return false;
		}
		
		$currentPaymentStatus = $order->getPaymentStatus();
		$newPaymentStatus = (0 == $currentPaymentStatus) ? 1 : 0;
		$order->setPaymentStatus($newPaymentStatus);
		
		try {
			$em->persist($order);
			$em->flush();
		} catch (\Exception $ex) {
			return false;
		}
	}
}
