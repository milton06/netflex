<?php

namespace NetFlex\FrontBundle\Controller;

use NetFlex\OrderBundle\Entity\Item;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryModeTimeline;
use NetFlex\FrontBundle\Form\Guest\Deliverability;
use NetFlex\OrderBundle\Entity\OrderTransaction;
use NetFlex\FrontBundle\Form\Guest\Order;
use NetFlex\FrontBundle\Form\Guest\CardDetails;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @Route("/guest/book-a-shipment")
 */
class GuestOrderController extends Controller
{
	/**
	 * Renders shipment booking screen for guest users.
	 *
	 * @Route("/", name="guest_book_a_shipment")
	 * @Method({"GET"})
	 *
	 * @param  Request $request
	 *
	 * @return Response
	 */
	public function guestBookAShipmentAction(Request $request)
	{
		/**
		 * Get required services.
		 */
		$bookAShipmentService = $this->get('guest_book_a_shipment');
		
		/**
		 * Get locations.
		 */
		list($countries, $defaultCountry, $states, $defaultState, $cities, $defaultCity) = $bookAShipmentService->getLocationData();
		
		/**
		 * Create the check deliverability form.
		 */
		$checkDeliverabilityForm = $this->createForm(Deliverability::class, new DeliveryModeTimeline(), [
			'countries' => $countries,
			'defaultCountry' => $defaultCountry,
			'states' => $states,
			'defaultState' => $defaultState,
			'cities' => $cities,
			'defaultCity' => $defaultCity,
		]);
		
		/**
		 * Get order item types.
		 */
		list($primaryItemtypes, $defaultPrimaryType, $secondaryItemtypes, $defaultSecondaryItemtype) = $bookAShipmentService->getOrderItemTypes();
		
		/**
		 * Get order item weight and price units.
		 */
		list($weightUnits, $defaultWeightUnit, $currencyUnits, $defaultCurrencyUnit) = $bookAShipmentService->getOrderWeightAndCurrencyUnits();
		
		/**
		 * Create the order form.
		 */
		$orderForm = $this->createForm(Order::class, new OrderTransaction(), [
			'primaryItemtypes' => $primaryItemtypes,
			'defaultPrimaryType' => $defaultPrimaryType,
			'secondaryItemtypes' => $secondaryItemtypes,
			'defaultSecondaryItemtype' => $defaultSecondaryItemtype,
			'weightUnits' => $weightUnits,
			'defaultWeightUnit' => $defaultWeightUnit,
			'currencyUnits' => $currencyUnits,
			'defaultCurrencyUnit' => $defaultCurrencyUnit,
			'riskTypes' => $this->getParameter('order_risk_types'),
			'countries' => $countries,
			'defaultCountry' => $defaultCountry,
			'states' => $states,
			'defaultState' => $defaultState,
			'cities' => $cities,
			'defaultCity' => $defaultCity,
		]);
		
		/**
		 * Render view.
		 */
		return $this->render('NetFlexFrontBundle:Booking:guest_book_a_shipment.html.twig', [
			'pageTitle' => 'Book A Shipment As Guest',
			'checkDeliverabilityForm' => $checkDeliverabilityForm->createView(),
			'orderForm' => $orderForm->createView(),
		]);
	}
	
	/**
	 * Checks shipment deliverability for guest.
	 *
	 * @Route("/check-deliverability", name="guest_book_a_shipment_check_deliverability")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request A request instance
	 *
	 * @return JsonResponse
	 */
	public function guestBookAShipmentCheckDeliverabilityAction(Request $request)
	{
		/**
		 * Get required services.
		 */
		$bookAShipmentService = $this->get('guest_book_a_shipment');
		
		list($deliveryModeId, $sourceCountryId, $sourceStateId, $sourceCityId, $sourceZipCode, $destinationCountryId, $destinationStateId, $destinationCityId, $destinationZipCode, $itemPrimaryType, $itemSecondaryType, $itemBaseWeight, $itemWeightUnit, $itemInvoiceValue, $itemPriceUnit, $riskType, $codChoice) = $bookAShipmentService->getDeliverabilityCheckAndChargeCalculationParameters($request->request->all());
		
		/**
		 * Check order deliverability.
		 */
		$deliverabilityCheckStatus = $bookAShipmentService->checkDeliverability($deliveryModeId, $sourceCountryId, $sourceStateId, $sourceCityId, $sourceZipCode, $destinationCountryId, $destinationStateId, $destinationCityId, $destinationZipCode);
		
		if (false === $deliverabilityCheckStatus['status']) {
			/**
			 * Deliverability not possible.
			 */
			return $this->json($deliverabilityCheckStatus['message']);
		}
		
		$actualDeliveryCharge = $deliverabilityCheckStatus['actualDeliveryCharge'];
		
		/**
		 * Calculate delivery charge parameters.
		 */
		$deliveryParams = $bookAShipmentService->getDeliveryChargeParameters($actualDeliveryCharge, $itemBaseWeight, $itemWeightUnit, $riskType, $codChoice);
		
		return $this->json(['delivery_params' => $deliveryParams]);
	}
	
	/**
	 * Books a shipment for guest.
	 *
	 * @Route("/place-order", name="guest_book_a_shipment_place_order")
	 * @Method({"POST"})
	 *
	 * @param  Request $request A request instance
	 *
	 * @return Response
	 */
	public function guestBookAShipmentPlaceOrderAction(Request $request)
	{
		/**
		 * Get the required services.
		 */
		$em = $this->getDoctrine()->getManager();
		$bookAShipmentService = $this->get('guest_book_a_shipment');
		
		/**
		 * Get locations.
		 */
		list($countries, $defaultCountry, $states, $defaultState, $cities, $defaultCity) = $bookAShipmentService->getLocationData();
		
		/**
		 * Create the check deliverability form.
		 */
		$checkDeliverabilityForm = $this->createForm(Deliverability::class, new DeliveryModeTimeline(), [
			'countries' => $countries,
			'defaultCountry' => $defaultCountry,
			'states' => $states,
			'defaultState' => $defaultState,
			'cities' => $cities,
			'defaultCity' => $defaultCity,
		]);
		
		/**
		 * Get order item types.
		 */
		list($primaryItemtypes, $defaultPrimaryType, $secondaryItemtypes, $defaultSecondaryItemtype) = $bookAShipmentService->getOrderItemTypes();
		
		/**
		 * Get order item weight and price units.
		 */
		list($weightUnits, $defaultWeightUnit, $currencyUnits, $defaultCurrencyUnit) = $bookAShipmentService->getOrderWeightAndCurrencyUnits();
		
		/**
		 * Create the order form.
		 */
		$order = new OrderTransaction();
		$orderForm = $this->createForm(Order::class, $order, [
			'primaryItemtypes' => $primaryItemtypes,
			'defaultPrimaryType' => $defaultPrimaryType,
			'secondaryItemtypes' => $secondaryItemtypes,
			'defaultSecondaryItemtype' => $defaultSecondaryItemtype,
			'weightUnits' => $weightUnits,
			'defaultWeightUnit' => $defaultWeightUnit,
			'currencyUnits' => $currencyUnits,
			'defaultCurrencyUnit' => $defaultCurrencyUnit,
			'riskTypes' => $this->getParameter('order_risk_types'),
			'countries' => $countries,
			'defaultCountry' => $defaultCountry,
			'states' => $states,
			'defaultState' => $defaultState,
			'cities' => $cities,
			'defaultCity' => $defaultCity,
		]);
		
		/**
		 * Populate order entity with submitted data.
		 */
		$orderForm->handleRequest($request);
		
		/**
		 * Validate.
		 */
		$errors = $this->get('validator')->validate($order);
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
        
        /**
         * Is a COD order
         */
        $isACodOrder = (bool) $order->getOrderPrice()->getOrderCodPaymentAddedCharge();
		
		/**
		 * Set additional fields.
		 */
		$currentDateTime = new \DateTime();
		$order->getOrderItem()->setItemUserBaseWeight($order->getOrderItem()->getItemBaseWeight() - $order->getOrderItem()->getItemAccountableExtraWeight());
		$order->getOrderItem()->setItemBaseWeight($order->getOrderItem()->getItemBaseWeight() - $order->getOrderItem()->getItemAccountableExtraWeight());
		$order->getOrderItem()->setItemUserAccountableExtraWeight($order->getOrderItem()->getItemAccountableExtraWeight());
		$order->getOrderPrice()->setOrderUserBaseCharge($order->getOrderPrice()->getOrderBaseCharge());
		$order->getOrderPrice()->setOrderUserExtraWeightLeviedCharge($order->getOrderPrice()->getOrderExtraWeightLeviedCharge());
		$order->setAwbNumber('nfcs-' . time());
		$order->setInvoiceNumber('nfon-' . time());
		
		if ($isACodOrder) {
            $order->setOrderStatus(1);
        } else {
            $order->setOrderStatus(8);
        }
		
		$order->setPaymentStatus(1);
		$order->setCreatedOn($currentDateTime);
		$order->setLastModifiedOn($currentDateTime);
		
		/**
		 * Set the order entity to the associated entities.
		 */
		$order->getOrderItem()->setOrderId($order);
		$order->getOrderPrice()->setOrderId($order);
		$order->getOrderAddress()->setOrderId($order);
		
		$em->persist($order);
		$em->flush();
		
		if ($isACodOrder) {
            /**
             * Shipment has been booked, send mail.
             */
            $mailerService = $this->get('mailer_service');
            list($fromEmail, $fromName, $subject, $message) = $mailerService->getMailTemplateData('SHPMNT_BK_SUCC');
            $message = $this->renderView('NetFlexMailerBundle::mail_layout.html.twig', [
                'mailBody' => $message,
            ]);
            $message = str_replace(['[clientName]', '[awbNumber]', '[invoiceNumber]', '[trackUrl]'], ['Guest Customer', $order->getAwbNumber(), $order->getInvoiceNumber(), $this->generateUrl('client_view_own_order', ['awbNumber' => $order->getAwbNumber()], UrlGeneratorInterface::ABSOLUTE_URL)], $message);
            $message = html_entity_decode($message);
            $mailerService->setMessage($fromEmail, $order->getOrderAddress()->getBillingEmail(), $subject, $message, 1, $fromName, 'Guest Customer');
            $mailerService->sendMail();
            
            $awbNumber = $order->getAwbNumber();
            $request->getSession()->set('awbNumber', $awbNumber);
            
            return $this->json(['status' => 'success', 'paymentType' => 'cod']);
        } else {
            return $this->json(['status' => 'success', 'paymentType' => 'online', 'orderId' => $order->getId()]);
        }
	}
	
	/**
	 * Gets order details for guest.
	 *
	 * @Route("/payment", name="guest_book_a_shipment_payment")
	 * @Method({"POST"})
	 *
	 * @param  Request $request A request instance
	 *
	 * @return Response
	 */
	public function guestBookAShipmentPaymentAction(Request $request)
	{
		/**
		 * Get required services.
		 */
		$em = $this->getDoctrine()->getManager();
		$bookAShipmentService = $this->get('guest_book_a_shipment');
		
		/**
		 * Fetch the current order details.
		 */
		$orderDetails = $em->getRepository('NetFlexOrderBundle:OrderTransaction')->findOneById
	($request->request->get('orderId'));
		//$orderDetails = $em->getRepository('NetFlexOrderBundle:OrderTransaction')->findOneById(1);
		
		/**
		 * Payment mode and debit card type choices.
		 */
		$paymentModes = [
			'CC' => 'Credit Card',
			'DC' => 'Debit Card'
		];
		$dcTypes = [
			'MasterCard Debit Cards (All Banks)' => 'MAST',
			'Other Maestro Cards' => 'MAES',
			'Rupay Debit CardRUPAY' => 'RUPAY',
			'State Bank Maestro Cards' => 'SMAE',
			'Visa Debit Cards (All Banks)' => 'VISA',
		];
		
		/**
		 * PayU embedded form parameters.
		 */
		$url = 'https://test.payu.in/_payment';
		$key = $this->getParameter('payu_merchant_key');
		$txnid = bin2hex(openssl_random_pseudo_bytes(20));
		$amount = ($orderDetails->getOrderPrice()->getOrderBaseCharge() + $orderDetails->getOrderPrice()->getOrderExtraWeightLeviedCharge() + $orderDetails->getOrderPrice()->getOrderFuelSurchargeAddedCharge() + $orderDetails->getOrderPrice()->getOrderServiceTaxAddedCharge() + $orderDetails->getOrderPrice()->getOrderCarrierRiskAddedCharge());
		$amount = (float) round($amount);
		$productinfo = 'NFGS-' . $orderDetails->getOrderItem()->getItemSecondaryTypeId()->getItemTypeName() . '-' . $orderDetails->getOrderItem()->getItemPrimaryTypeId()->getItemTypeName();
		$udf1 = $orderDetails->getAwbNumber();
		$firstname = $orderDetails->getOrderAddress()->getBillingFirstName() . ' ' . $orderDetails->getOrderAddress()->getBillingLastName();
		$email = $orderDetails->getOrderAddress()->getBillingEmail();
		$phone = $orderDetails->getOrderAddress()->getBillingContactNumber();
		$furl = $this->generateUrl('guest_book_a_shipment_payment_failure', [], UrlGeneratorInterface::ABSOLUTE_URL);
		$surl = $this->generateUrl('guest_book_a_shipment_payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL);
		$salt = $this->getParameter('payu_merchant_salt');
		$hashSequence = "$key|$txnid|$amount|$productinfo|$firstname|$email|$udf1||||||||||$salt";
		$HASH = strtolower(hash('sha512', $hashSequence));
		$pg = 'CC';
		$bankcode = 'CC';
		$months = ['JAN' => '1', 'FEB' => '2', 'MAR' => '3', 'APR' => '4', 'MAY' => '5', 'JUN' => '6', 'JUL' => '7', 'AUG' => '8', 'SEP' => '9', 'OCT' => '10', 'NOV' => '11', 'DEC' => '12'];
		$years = $bookAShipmentService->getExpieryYears();
		
		$options = compact('url', 'paymentModes', 'dcTypes', 'key', 'txnid', 'amount', 'productinfo', 'udf1', 'firstname', 'email', 'phone', 'furl', 'surl', 'HASH', 'pg', 'bankcode', 'months', 'years');
		
		$cardDetailsForm = $this->createForm(CardDetails::class, null, $options);
		
		return $this->render('NetFlexFrontBundle:Booking:guest_payment.html.twig', [
			'orderDetails' => $orderDetails,
			'cardDetailsForm' => $cardDetailsForm->createView(),
		]);
	}
	
	/**
	 * Renders payment success page.
	 *
	 * @Route("/payment-success", name="guest_book_a_shipment_payment_success")
	 * @Method({"GET", "POST"})
	 *
	 * @param  Request                   $request A request instance
	 *
	 * @return RedirectResponse|Response
	 */
	public function guestBookAShipmentPaymentSuccessAction(Request $request)
	{
		if ('get' === strtolower($request->server->get('REQUEST_METHOD'))) {
			return $this->redirectToRoute('guest_book_a_shipment');
		} else {
			/**
			 * Get required services.
			 */
			$em = $this->getDoctrine()->getManager();
			
			$awbNumber = $request->request->get('udf1');
			
			/**
			 * Update order and payment statuses.
			 */
			$orderDetails = $em->getRepository('NetFlexOrderBundle:OrderTransaction')->findOneByAwbNumber($awbNumber);
			$orderDetails->setOrderStatus(2);
			$orderDetails->setPaymentStatus(2);
			
			$em->persist($orderDetails);
			
			$em->flush();
            
            /**
             * Shipment has been booked, send mail.
             */
            $mailerService = $this->get('mailer_service');
            list($fromEmail, $fromName, $subject, $message) = $mailerService->getMailTemplateData('SHPMNT_BK_SUCC');
            $message = $this->renderView('NetFlexMailerBundle::mail_layout.html.twig', [
            'mailBody' => $message,
            ]);
            $message = str_replace(['[clientName]', '[awbNumber]', '[invoiceNumber]', '[trackUrl]'], ['Guest Customer', $orderDetails->getAwbNumber(), $orderDetails->getInvoiceNumber(), $this->generateUrl('client_view_own_order', ['awbNumber' => $orderDetails->getAwbNumber()], UrlGeneratorInterface::ABSOLUTE_URL)], $message);
            $message = html_entity_decode($message);
            $mailerService->setMessage($fromEmail, $orderDetails->getOrderAddress()->getBillingEmail(), $subject, $message, 1, $fromName, 'Guest Customer');
            $mailerService->sendMail();
			
			return $this->render('NetFlexFrontBundle:Booking:guest_order_confirmation.html.twig', [
				'pageTitle' => 'Order Confirmation',
				'awbNumber' => $awbNumber,
			]);
		}
	}
    
    /**
     * Renders the booking confirmation page.
     *
     * @Route("/order-confirmation", name="guest_order_confirmation")
     * @Method({"GET"})
     *
     * @param Request $request A request instance
     *
     * @return Response
     */
    public function renderGuestOrderConfirmationPageAction(Request $request)
    {
        $session = $request->getSession();
        
        if (! $session->has('awbNumber')) {
            return $this->redirectToRoute('home_page');
        }
        
        $awbNumber = $session->get('awbNumber');
        $session->remove('awbNumber');
        
        return $this->render('NetFlexFrontBundle:Booking:guest_order_confirmation.html.twig', [
            'pageTitle' => 'Order Confirmation',
            'awbNumber' => $awbNumber,
        ]);
    }
	
	/**
	 * Renders payment failure page.
	 *
	 * @Route("/payment-failure", name="guest_book_a_shipment_payment_failure")
	 * @Method({"GET", "POST"})
	 *
	 * @param  Request $request A request instance
	 *
	 * @return Response
	 */
	public function guestBookAShipmentPaymentFailureAction(Request $request)
	{
		if ('get' === strtolower($request->server->get('REQUEST_METHOD'))) {
			/**
			 * Cannot see this page twice.
			 */
			return $this->redirectToRoute('guest_book_a_shipment');
		} else {
			/**
			 * Get required services.
			 */
			$em = $this->getDoctrine()->getManager();
			
			$awbNumber = $request->request->get('udf1');
			
			/**
			 * Update order and payment statuses.
			 */
			$orderDetails = $em->getRepository('NetFlexOrderBundle:OrderTransaction')->findOneByAwbNumber($awbNumber);
			
			if (! $orderDetails) {
				/**
				 * Cannot see this page twice.
				 */
				return $this->redirectToRoute('guest_book_a_shipment');
			}
			
			$orderAddress = $orderDetails->getOrderAddress();
			$orderPrice = $orderDetails->getOrderPrice();
			$orderItem = $orderDetails->getOrderItem();
			
			$em->remove($orderItem);
			$em->remove($orderPrice);
			$em->remove($orderAddress);
			$em->remove($orderDetails);
			
			$em->flush();
			
			return $this->render('NetFlexFrontBundle:Booking:guest_payment_failure.html.twig', [
				'pageTitle' => 'Guest Payment Failure',
			]);
		}
	}
	
	/**
	 * Gets all the states of a country and cities of the first state.
	 *
	 * @Route("/state-list", name="guest_book_a_shipment_state_list")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request
	 *
	 * @return JsonResponse
	 */
	public function guestBookAShipmentStateListAction(Request $request)
	{
		/**
		 * Get required services.
		 */
		$bookAShipmentService = $this->get('guest_book_a_shipment');
		
		/**
		 * Get state and city list for requested country.
		 */
		list($stateList, $cityList) = $bookAShipmentService->getStateAndCityListByCountryId($request->request->get('countryId'));
		
		return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
	}
	
	/**
	 * Gets all the cities of a state.
	 *
	 * @Route("/city-list", name="guest_book_a_shipment_city_list")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request
	 *
	 * @return JsonResponse
	 */
	public function guestBookAShipmentCityListAction(Request $request)
	{
		/**
		 * Get required services.
		 */
		$bookAShipmentService = $this->get('guest_book_a_shipment');
		
		/**
		 * Get city list for requested state.
		 */
		$cityList = $bookAShipmentService->getCityListByStateId($request->request->get('stateId'));
		
		return $this->json(['cityList' => $cityList]);
	}
	
	/**
	 * Gets secondary item types.
	 *
	 * @Route("/secondary-item-type", name="guest_book_a_shipment_secondary_item_type")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request
	 *
	 * @return JsonResponse
	 */
	public function guestBookAShipmentSecondaryItemTypes(Request $request)
	{
		/**
		 * Get required services.
		 */
		$bookAShipmentService = $this->get('guest_book_a_shipment');
		
		/**
		 * Get secondary item type list for requested primary item type.
		 */
		$secondaryItemTypeList = $bookAShipmentService->getSecondaryItemTypeList($request->request->get('itemPrimaryTypeId'));
		
		return $this->json(['itemRelatedSecondaryTypeList' => $secondaryItemTypeList]);
	}
}
