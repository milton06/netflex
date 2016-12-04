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
	     * Get client's pickup and billing addresses.
	     */
	    $clientPickupAndBillingAddresses = $clientAddressRepo->findClientPreferredPickupAndBillingAddresses($clientId);
	    if (0 < $clientPickupAndBillingAddresses[0]['billingAddressCount']) {
		    $clientBillingAddressCount = $clientPickupAndBillingAddresses[0]['billingAddressCount'];
		    unset($clientPickupAndBillingAddresses[0]);
		    list($clientBillingAddresses, $clientPickupAddresses) = array_chunk($clientPickupAndBillingAddresses, $clientBillingAddressCount);
	    }
	    
	    /**
	     * Set client's preferred pickup address if any and create the check deliverability form.
	     */
	    $deliveryModeTimeline = new DeliveryModeTimeline();
	    if (isset($clientPickupAddresses) && ! empty($clientPickupAddresses)) {
		    $deliveryModeTimeline->setSourceCountryId($clientPickupAddresses[0][0]->getCountryId());
		    $deliveryModeTimeline->setSourceStateId($clientPickupAddresses[0][0]->getStateId());
		    $deliveryModeTimeline->setSourceCityId($clientPickupAddresses[0][0]->getCityId());
		    $deliveryModeTimeline->setSourceZipCode($clientPickupAddresses[0][0]->getZipCode());
	    }
	    $checkDeliverabilityForm = $this->createForm(CheckDeliverabilityType::class, $deliveryModeTimeline);
	
	    /**
	     * Set client's preferred pickup and billing addresses to order address fields and create the order form. Also generate the rest of pickup and billing addresses list.
	     */
	    $orderAddress = new Address();
	    $clientOtherPickupAddresses = $clientOtherBillingAddresses = [];
	    if (isset($clientPickupAddresses) && ! empty($clientPickupAddresses)) {
		    /**
		     * Set client's preferred pickup address.
		     */
		    $orderAddress->setPickupFirstName($clientPickupAddresses[0][0]->getUserId()->getFirstName());
		    $orderAddress->setPickupMidName($clientPickupAddresses[0][0]->getUserId()->getMidName());
		    $orderAddress->setPickupLastName($clientPickupAddresses[0][0]->getUserId()->getLastName());
		    $orderAddress->setPickupAddressLine1($clientPickupAddresses[0][0]->getAddressLine1());
		    $orderAddress->setPickupAddressLine2($clientPickupAddresses[0][0]->getAddressLine2());
		    $orderAddress->setPickupCountryId($clientPickupAddresses[0][0]->getCountryId());
		    $orderAddress->setPickupStateId($clientPickupAddresses[0][0]->getStateId());
		    $orderAddress->setPickupCityId($clientPickupAddresses[0][0]->getCityId());
		    $orderAddress->setPickupZipCode($clientPickupAddresses[0][0]->getZipCode());
		    $orderAddress->setPickupEmail($em->getRepository('NetFlexUserBundle:Email')->findBy(['userId' => $clientPickupAddresses[0][0]->getUserId(), 'isPrimary' => 1, 'status' => 1])[0]->getEmail());
		    $orderAddress->setPickupContactNumber($em->getRepository('NetFlexUserBundle:Contact')->findBy(['userId' => $clientPickupAddresses[0][0]->getUserId(), 'isPrimary' => 1, 'status' => 1])[0]->getContactNumber());
		    
		    /**
		     * Generate client's other pickup addresses list.
		     */
		    for ($i = 0; $i < count($clientPickupAddresses); $i++) {
			    $clientOtherPickupAddresses[$clientPickupAddresses[$i][0]->getAddressLine1() . '; ' . $clientPickupAddresses[$i][0]->getCountryId()->getName() . '; ' . $clientPickupAddresses[$i][0]->getStateId()->getName() . '; ' . $clientPickupAddresses[$i][0]->getCityId()->getName() . '; ' . $clientPickupAddresses[$i][0]->getZipCode()] = $clientPickupAddresses[$i][0]->getId();
		    }
	    }
	    if (isset($clientBillingAddresses) && ! empty($clientBillingAddresses)) {
		    /**
		     * Set client's preferred billing address.
		     */
		    $orderAddress->setBillingFirstName($clientBillingAddresses[0][0]->getUserId()->getFirstName());
		    $orderAddress->setBillingMidName($clientBillingAddresses[0][0]->getUserId()->getMidName());
		    $orderAddress->setBillingLastName($clientBillingAddresses[0][0]->getUserId()->getLastName());
		    $orderAddress->setBillingAddressLine1($clientBillingAddresses[0][0]->getAddressLine1());
		    $orderAddress->setBillingAddressLine2($clientBillingAddresses[0][0]->getAddressLine2());
		    $orderAddress->setBillingCountryId($clientBillingAddresses[0][0]->getCountryId());
		    $orderAddress->setBillingStateId($clientBillingAddresses[0][0]->getStateId());
		    $orderAddress->setBillingCityId($clientBillingAddresses[0][0]->getCityId());
		    $orderAddress->setBillingZipCode($clientBillingAddresses[0][0]->getZipCode());
		    $orderAddress->setBillingEmail($em->getRepository('NetFlexUserBundle:Email')->findBy(['userId' => $clientBillingAddresses[0][0]->getUserId(), 'isPrimary' => 1, 'status' => 1])[0]->getEmail());
		    $orderAddress->setBillingContactNumber($em->getRepository('NetFlexUserBundle:Contact')->findBy(['userId' => $clientBillingAddresses[0][0]->getUserId(), 'isPrimary' => 1, 'status' => 1])[0]->getContactNumber());
		
		    /**
		     * Generate client's other billing addresses list.
		     */
		    for ($i = 0; $i < count($clientBillingAddresses); $i++) {
			    $clientOtherBillingAddresses[$clientBillingAddresses[$i][0]->getAddressLine1() . '; ' . $clientBillingAddresses[$i][0]->getCountryId()->getName() . '; ' . $clientBillingAddresses[$i][0]->getStateId()->getName() . '; ' . $clientBillingAddresses[$i][0]->getCityId()->getName() . '; ' . $clientBillingAddresses[$i][0]->getZipCode()] = $clientBillingAddresses[$i][0]->getId();
		    }
	    }
	    $order = new OrderTransaction();
	    $order->setOrderAddress($orderAddress);
	    $orderForm = $this->createForm(OrderForClientFromDashboardType::class, $order, [
	    	'clientOtherPickupAddresses' => $clientOtherPickupAddresses,
		    'clientOtherBillingAddresses' => $clientOtherBillingAddresses,
	    ]);
	    
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
		    'clientId' => $clientId,
		    'checkDeliverabilityForm' => $checkDeliverabilityForm->createView(),
		    'orderForm' => $orderForm->createView(),
	    ]);
    }
	
	/**
	 * Set client's preferred address.
	 *
	 * @Route("/dashboard/client/set-preferred-address", name="set_preferred_address_for_client_from_dashboard")
	 * @Method({"POST"})
	 *
	 * @param  Request $request A request instance
	 *
	 * @return JsonResponse
	 */
    public function setPreferredAddress(Request $request)
    {
	    $addressId = $request->request->get('addressId');
	    if (! $addressId) { // No address selected.
		    return $this->json(['status' => false]);
	    }
	    $em = $this->getDoctrine()->getManager();
	    $address = $em->getRepository('NetFlexUserBundle:Address')->findOneById($addressId);
	    $countries = $em->getRepository('NetFlexLocationBundle:Country')->findBy(['status' => 1]);
	    $states = $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' => $address->getCountryId()->getId(), 'status' => 1]);
	    $cities = $em->getRepository('NetFlexLocationBundle:City')->findBy(['stateId' => $address->getStateId()->getId(), 'status' => 1]);
	    $email = $em->getRepository('NetFlexUserBundle:Email')->findBy(['userId' => $address->getUserId()->getId(), 'isPrimary' => 1, 'status' => 1]);
	    $contactNumber = $em->getRepository('NetFlexUserBundle:Contact')->findBy(['userId' => $address->getUserId()->getId(), 'isPrimary' => 1, 'status' => 1]);
	    $countryList = $stateList = $cityList = [];
	    foreach ($countries as $thisCountry) {
		    $countryList[$thisCountry->getId()] = $thisCountry->getName();
	    }
	    foreach ($states as $thisState) {
		    $stateList[$thisState->getId()] = $thisState->getName();
	    }
	    foreach($cities as $thisCity) {
		    $cityList[$thisCity->getId()] = $thisCity->getName();
	    }
	    return $this->json(['status' => true, 'address' => [
	    	'firstName' => $address->getUserId()->getFirstName(),
		    'midName' => $address->getUserId()->getMidName(),
		    'lastName' => $address->getUserId()->getLastName(),
		    'addressLine1' => $address->getAddressLine1(),
		    'addressLine2' => $address->getAddressLine2(),
		    'countryId' => $address->getCountryId()->getId(),
		    'stateId' => $address->getStateId()->getId(),
		    'cityId' => $address->getCityId()->getId(),
		    'zipCode' => $address->getZipCode(),
		    'email' => ($email) ? $email[0]->getEmail() : '',
		    'contactNumber' => ($contactNumber) ? $contactNumber[0]->getContactNumber() : '',
		    'countryList' => $countryList,
		    'stateList' => $stateList,
		    'cityList' => $cityList,
	    ]]);
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
	     * Get the entity manager.
	     */
	    $em = $this->getDoctrine()->getManager();
	    
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
	    $awbNumber = $order->getAwbNumber();
	    
	    return $this->json(['status' => 'success', 'orderId' => $orderId, 'awbNumber' => $awbNumber]);
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
		    return $this->json(['no_deliverability_error' => 'Shipment is not possible between these locations presently']);
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
