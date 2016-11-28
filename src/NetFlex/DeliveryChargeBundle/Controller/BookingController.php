<?php

namespace NetFlex\DeliveryChargeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

class BookingController extends Controller
{
    /**
     * Renders the booking page.
     *
     * @Route("/dashboard/client/book-a-shipment/{clientId}", name="book_a_client_shipment_from_dashboard", requirements={"clientId": "\d+"})
     * @Method({"GET"})
     *
     * @param int     $clientId
     * @param Request $request A request instance
     *
     * @return Response
     */
    public function renderBookingForClientPageInDashboardAction($clientId, Request $request)
    {
	    $em = $this->getDoctrine()->getManager();
	
	    /**
	     * Repositories.
	     */
	    $clientRepo = $em->getRepository('NetFlexUserBundle:User');
	    $clientAddressRepo = $em->getRepository('NetFlexUserBundle:Address');
	    
	    if (! ($client = $clientRepo->findUserExistence($clientId))) {
		    throw $this->createNotFoundException("No client with ID: $clientId exists");
	    }
	
	    /**
	     * Get client's preferred addresses.
	     */
	    $clientPreferredPickupAndBillingAddresses = $clientAddressRepo->findClientPreferredPickupAndBillingAddresses($clientId);
	    
	    $deliveryModeTimeline = new DeliveryModeTimeline();
	
	    /**
	     * Set client's preferred pickup address if any and create the check deliverability form.
	     */
	    if (! empty($clientPreferredPickupAndBillingAddresses) && isset($clientPreferredPickupAndBillingAddresses[1])) {
		    $deliveryModeTimeline->setSourceCountryId($clientPreferredPickupAndBillingAddresses[1]->getCountryId());
		    $deliveryModeTimeline->setSourceStateId($clientPreferredPickupAndBillingAddresses[1]->getStateId());
		    $deliveryModeTimeline->setSourceCityId($clientPreferredPickupAndBillingAddresses[1]->getCityId());
		    $deliveryModeTimeline->setSourceZipCode($clientPreferredPickupAndBillingAddresses[1]->getZipCode());
	    }
	    
	    $checkDeliverabilityForm = $this->createForm(CheckDeliverabilityType::class, $deliveryModeTimeline);
	
	    /**
	     * Set client's preferred addresses to order address fields.
	     */
	    $orderAddress = new Address();
	    
	    if (! empty($clientPreferredPickupAndBillingAddresses) && isset($clientPreferredPickupAndBillingAddresses[1])) {
		    /**
		     * Set client's preferred pickup address
		     */
		    $orderAddress->setPickupFirstName($client->getFirstName());
		    $orderAddress->setPickupMidName($client->getMidName());
		    $orderAddress->setPickupLastName($client->getLastName());
		    $orderAddress->setPickupAddressLine1($clientPreferredPickupAndBillingAddresses[1]->getAddressLine1());
		    $orderAddress->setPickupAddressLine2($clientPreferredPickupAndBillingAddresses[1]->getAddressLine2());
		    $orderAddress->setPickupCountryId($clientPreferredPickupAndBillingAddresses[1]->getCountryId());
		    $orderAddress->setPickupStateId($clientPreferredPickupAndBillingAddresses[1]->getStateId());
		    $orderAddress->setPickupCityId($clientPreferredPickupAndBillingAddresses[1]->getCityId());
		    $orderAddress->setPickupZipCode($clientPreferredPickupAndBillingAddresses[1]->getZipCode());
	    }
	
	    if (! empty($clientPreferredPickupAndBillingAddresses) && isset($clientPreferredPickupAndBillingAddresses[0])) {
		    /**
		     * Set client's preferred billing address
		     */
		    $orderAddress->setBillingFirstName($client->getFirstName());
		    $orderAddress->setBillingMidName($client->getMidName());
		    $orderAddress->setBillingLastName($client->getLastName());
		    $orderAddress->setBillingAddressLine1($clientPreferredPickupAndBillingAddresses[0]->getAddressLine1());
		    $orderAddress->setBillingAddressLine2($clientPreferredPickupAndBillingAddresses[0]->getAddressLine2());
		    $orderAddress->setBillingCountryId($clientPreferredPickupAndBillingAddresses[0]->getCountryId());
		    $orderAddress->setBillingStateId($clientPreferredPickupAndBillingAddresses[0]->getStateId());
		    $orderAddress->setBillingCityId($clientPreferredPickupAndBillingAddresses[0]->getCityId());
		    $orderAddress->setBillingZipCode($clientPreferredPickupAndBillingAddresses[0]->getZipCode());
	    }
	    
	    /**
	     * Create the order form.
	     */
	    $order = new OrderTransaction();
	    $order->setOrderAddress($orderAddress);
	    $orderForm = $this->createForm(OrderForClientFromDashboardType::class, $order);
	    
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
			    'link' => $this->generateUrl('book_a_client_shipment_from_dashboard', ['clientId' => $clientId], UrlGeneratorInterface::ABSOLUTE_URL)
		    ],
	    ];
	
	    $referrer = urldecode($request->query->get('ref'));
	
	    return $this->render('NetFlexDeliveryChargeBundle:ClientBooking:book_a_shipment_from_dashboard.html.twig', [
		    'pageTitle' => 'Book A Shipment',
		    'breadCrumbs' => $breadCrumbs,
		    'referrer' => $referrer,
		    'pageHeader' => '<h1>Book <small>a shipment</small></h1>',
		    'checkDeliverabilityForm' => $checkDeliverabilityForm->createView(),
		    'orderForm' => $orderForm->createView(),
	    ]);
    }
	
	/**
	 * Book a shipment for a client from dashboard.
	 *
	 * @Route("/dashboard/client/place-shipment-order", name="place_shipment_order_for_client_from_dashboard")
	 * @Method({"POST"})
	 *
	 * @param  Request $request A request instance
	 *
	 * @return Response
	 */
    public function placeShipmentOrderForClientFromDashboard(Request $request)
    {
	    /**
	     * Create an empty order entity and form.
	     */
	    $order = new OrderTransaction();
	    
	    $orderForm = $this->createForm(OrderForClientFromDashboardType::class, $order);
	
	    /**
	     * Populate order entity with submitted data.
	     */
	    $orderForm->handleRequest($request);
	
	    /**
	     * Get the entity manager.
	     */
	    $em = $this->getDoctrine()->getManager();
	    
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
	    $order->setOrderStatus(1);
	    $order->setPaymentStatus(0);
	    $order->setCreatedOn($currentDateTime);
	    $order->setCreatedBy($this->getUser()->getId());
	    $order->setLastModifiedOn($currentDateTime);
	    $order->setLastModifiedBy($this->getUser()->getId());
	
	    /**
	     * Set the order entity to the associated entities.
	     */
	    $order->getOrderItem()->setOrderId($order);
	    $order->getOrderPrice()->setOrderId($order);
	    $order->getOrderAddress()->setOrderId($order);
	    
	    $em->persist($order);
	    
	    $em->flush();
	    
	    $orderId = $order->getId();
	    
	    return $this->json(['status' => 'success', 'orderId' => $orderId]);
    }
	
	/**
	 * Checks deliverability.
	 *
	 * @Route("/dashboard/booking/check-deliverability", name="check_deliverability_from_dashboard")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request A request instance
	 *
	 * @return JsonResponse
	 */
    public function checkDeliverabilityAction(Request $request)
    {
	    /**
	     * Selected delivery mode.
	     */
	    $deliveryModeId = $request->request->get('deliveryModeId');
	
	    /**
	     * Selected source params.
	     */
	    $sourceCountryId = $request->request->get('sourceCountryId');
	    $sourceStateId = $request->request->get('sourceStateId');
	    $sourceCityId = $request->request->get('sourceCityId');
	    $sourceZipCode = $request->request->get('sourceZipCode');
	
	    /**
	     * Selected destination params.
	     */
	    $destinationCountryId = $request->request->get('destinationCountryId');
	    $destinationStateId = $request->request->get('destinationStateId');
	    $destinationCityId = $request->request->get('destinationCityId');
	    $destinationZipCode = $request->request->get('destinationZipCode');
	
	    /**
	     * Selected item params.
	     */
	    $itemPrimaryType = $request->request->get('itemPrimaryType');
	    $itemSecondaryType = $request->request->get('itemSecondaryType');
	    $itemBaseWeight = $request->request->get('itemBaseWeight');
	    $itemWeightUnit = $request->request->get('itemWeightUnit');
	    $itemInvoiceValue = $request->request->get('itemInvoiceValue');
	    $itemPriceUnit = $request->request->get('itemPriceUnit');
	    $riskType = $request->request->get('riskType');
	    $codChoice = $request->request->get('codChoice');
	    
	    $em = $this->getDoctrine()->getManager();
	    $deliveryChargeRepo = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge');
	
	    /**
	     * Query for matching delivery charge(s).
	     */
	    $deliveryCharge = $deliveryChargeRepo->findDeliverability($sourceCountryId, $sourceStateId, $sourceCityId, $sourceZipCode, $destinationCountryId, $destinationStateId, $destinationCityId, $destinationZipCode);
	    
	    if (! $deliveryCharge) {
		    /**
		     * No delivery charge available for the source-destination location combination. i.e. we don't deliver there.
		     */
		    return $this->json(['no_deliverability_error' => 'We don\'t deliver at this location']);
	    }
	    
	    if ((1 === count($deliveryCharge)) && ($deliveryModeId != $deliveryCharge[0]->getDeliveryModeTimelineId()->getDeliveryModeId()->getId())) {
		    /**
		     * The requested delivery mode is not available for the source-destination location combination. Though another mode is available.
		     */
		    $deliveryModeRepo = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryMode');
			
		    return $this->json(['delivery_mode_error' => 'We don\'t deliver via ' . $deliveryModeRepo->findDeliveryModeName($deliveryModeId)->getModeName() . ' at this location. But you can select ' . $deliveryCharge[0]->getDeliveryModeTimelineId()->getDeliveryModeId()->getModeName() . '.']);
	    }
	    
	    if (1 === count($deliveryCharge)) {
		    /**
		     * There is only one delivery charge and that's the intended one.
		     */
		    $actualDeliveryCharge = $deliveryCharge[0];
	    } else {
		    foreach ($deliveryCharge as $thisDeliveryCharge) {
			    /**
			     * Since there may be more than one delivery charges, we need the extract the intended one.
			     */
			    $actualDeliveryCharge = $thisDeliveryCharge;
			
			    if ($deliveryModeId == $thisDeliveryCharge->getDeliveryModeTimelineId()->getDeliveryModeId()->getId()) {
				    break;
			    }
		    }
	    }
	
	    /**
	     * Charge calculations.
	     */
	    // Weight.
	    $shipmentBaseWeightUpperLimit = $actualDeliveryCharge->getShipmentBaseWeightUpperLimit();
	    $shipmentAccountableExtraWeight = $actualDeliveryCharge->getShipmentAccountableExtraWeight();
	    $shipmentWeightUnitId = $actualDeliveryCharge->getShipmentWeightUnitId()->getId();
	    $itemAccountableExtraWeight = 0;
	    
	    if ($itemWeightUnit != $shipmentWeightUnitId) {
		    /**
		     * Weight units are different. Convert.
		     */
		    if (1 == $shipmentWeightUnitId) {
			    // In kg. convert to gm.
			    $itemBaseWeight = ($itemBaseWeight * 1000);
		    } else {
			    // In gm. convert to kg.
			    $itemBaseWeight = ($itemBaseWeight / 1000);
		    }
		    
		    $itemWeightUnit = $shipmentWeightUnitId;
	    }
	    
	    if ($shipmentBaseWeightUpperLimit < $itemBaseWeight) {
		    /**
		     * Item weight is greater than permitted base weight. So add the extra as accountable weight for extra charge.
		     */
		    $itemAccountableExtraWeight = ($itemBaseWeight - $shipmentBaseWeightUpperLimit);
		    $itemBaseWeight = $shipmentBaseWeightUpperLimit;
	    }
	    
	    // Charge.
	    $deliveryBasePrice = $actualDeliveryCharge->getDeliveryBasePrice();
	    $deliveryExtraPriceMultiplier = $actualDeliveryCharge->getDeliveryExtraPriceMultiplier();
	    $codDeliveryBasePrice = $actualDeliveryCharge->getCodDeliveryBasePrice();
	    $codeDeliveryPercentageOnBasePrice = $actualDeliveryCharge->getCodDeliveryPercentageOnBasePrice();
	    $fuelSurchargeFixedPrice = $actualDeliveryCharge->getFuelSurchargeFixedPrice();
	    $fuelSurchargePercentageOnBasePrice = $actualDeliveryCharge->getFuelSurchargePercentageOnBasePrice();
	    $serviceTaxPercentageOnBasePrice = $actualDeliveryCharge->getServiceTaxPercentageOnBasePrice();
	    $shipmentRiskChargableAbove = $actualDeliveryCharge->getShipmentRiskChargableAbove();
	    $shipmentRiskPercentageOnBasePrice = $actualDeliveryCharge->getShipmentRiskPercentageOnBasePrice();
	    $deliveryPriceUnitId = $actualDeliveryCharge->getDeliveryPriceUnitId()->getId();
	
	    /**
	     * Base and accountable extra weight charges.
	     */
	    $orderBaseCharge = $deliveryBasePrice;
	    $orderExtraWeightLeviedCharge = (ceil($itemAccountableExtraWeight / $shipmentAccountableExtraWeight) * $deliveryExtraPriceMultiplier);
	    $orderTotalCharge = ($orderBaseCharge + $orderExtraWeightLeviedCharge);
	
	    $orderCodPaymentAddedCharge = 0;
	    if ($codChoice) {
		    /**
		     * COD applied.
		     */
		    $orderCodPaymentAddedCharge = ($codDeliveryBasePrice) ? $codDeliveryBasePrice : (($orderTotalCharge * $codeDeliveryPercentageOnBasePrice) / 100);
	    }
	
	    /**
	     * Added fuel surcharge.
	     */
	    $orderFuelSurchargeAddedCharge = ($fuelSurchargeFixedPrice) ? $fuelSurchargeFixedPrice : ($fuelSurchargePercentageOnBasePrice ? (($orderTotalCharge * $fuelSurchargePercentageOnBasePrice) / 100) : 0);
	
	    /**
	     * Added service tax charge.
	     */
	    $orderServiceTaxAddedCharge = ($serviceTaxPercentageOnBasePrice) ? (($orderTotalCharge * $serviceTaxPercentageOnBasePrice) / 100) : 0;
	    
	    $orderCarrierRiskAddedCharge = 0;
	    if (($riskType) && ('carrier' == $riskType)) {
		    /**
		     * Added carrier risk charge.
		     */
		    $orderCarrierRiskAddedCharge = ($orderTotalCharge * $shipmentRiskPercentageOnBasePrice / 100);
	    }
	    
	    $deliveryParams = [
	    	'deliveryChargeId' => $actualDeliveryCharge->getId(),
		    'itemCalculatedBaseWeight' => $itemBaseWeight,
		    'itemCalculatedWeightUnit' => $itemWeightUnit,
		    'itemAccountableExtraWeight' => $itemAccountableExtraWeight,
		    'orderBaseCharge' => $orderBaseCharge,
		    'orderExtraWeightLeviedCharge' => $orderExtraWeightLeviedCharge,
		    'orderCodPaymentAddedCharge' => $orderCodPaymentAddedCharge,
		    'orderFuelSurchargeAddedCharge' => $orderFuelSurchargeAddedCharge,
		    'orderServiceTaxAddedCharge' => $orderServiceTaxAddedCharge,
		    'orderCarrierRiskAddedCharge' => $orderCarrierRiskAddedCharge,
	    ];
	    
	    return $this->json(['delivery_params' => $deliveryParams]);
    }
	
	/**
	 * Gets secondary item types.
	 *
	 * @Route("/dashboard/booking/get-secondary-item-type", name="get_secondary_item_type_from_dashboard")
	 * @Method({"POST"})
	 *
	 * @param  Request      $request A request instance
	 *
	 * @return JsonResponse
	 */
    public function getSecondaryItemTypes(Request $request)
    {
	    $itemRelatedSecondaryTypeList = [];
	
	    $itemPrimaryTypeId = $request->request->get('itemPrimaryTypeId');
	    
	    if (! $itemPrimaryTypeId) {
		    return $this->json(['itemRelatedSecondaryTypeList' => $itemRelatedSecondaryTypeList]);
	    }
	    
	    $itemTypeRepo = $this->getDoctrine()->getManager()->getRepository('NetFlexOrderBundle:ItemType');
	    
	    $itemRelatedSecondaryTypes = $itemTypeRepo->findBy(['parentId' => $itemTypeRepo->findOneById($itemPrimaryTypeId), 'status' => 1]);
	    
	    if ($itemRelatedSecondaryTypes) {
		    foreach ($itemRelatedSecondaryTypes as $thisItemRelatedSecondaryType) {
			    $itemRelatedSecondaryTypeList[$thisItemRelatedSecondaryType->getId()] = $thisItemRelatedSecondaryType->getItemTypeName();
		    }
	    }
	    
	    return $this->json(['itemRelatedSecondaryTypeList' => $itemRelatedSecondaryTypeList]);
    }
}
