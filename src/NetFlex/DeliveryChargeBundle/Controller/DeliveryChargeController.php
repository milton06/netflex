<?php

namespace NetFlex\DeliveryChargeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryMode;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryTimeline;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryModeTimeline;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryCharge;
use NetFlex\DeliveryChargeBundle\Form\DeliveryCharge\DeliveryZoneType;
use NetFlex\DeliveryChargeBundle\Form\DeliveryCharge\DeliveryChargeNewType;
use NetFlex\DeliveryChargeBundle\Form\DeliveryCharge\DeliveryChargeEditType;
use NetFlex\DeliveryChargeBundle\Form\DeliveryCharge\DeliveryChargeSearchType;

/**
 * @Route("/dashboard/delivery-charge")
 */
class DeliveryChargeController extends Controller
{
    /**
     * Renders delivery charge list.
     *
     *  @Route("/list/{page}/{sortColumn}/{sortOrder}", name="dashboard_delivery_charge_list", defaults={"page": 1, "sortColumn": "id", "sortOrder": "desc"}, requirements={"page": "\d+", "sortColumn": "id", "sortOrder": "asc|desc"})
     * @Method({"GET", "POST"})
     *
     * @param  int     $page
     * @param  string  $sortColumn
     * @param  string  $sortOrder
     * @param  Request $request
     *
     * @return Response
     */
    public function dashboardDeliveryChargeListAction($page, $sortColumn, $sortOrder, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        $paginationService = $this->get('pagination_service');
    
        $routeParameters = [
            'page' => $page,
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
        ];
        
        $sourceCountryId = ($session->has('sourceCountryId')) ? $session->get('sourceCountryId') : null;
        $sourceStateId = ($session->has('sourceStateId')) ? $session->get('sourceStateId') : null;
        $sourceCityId = ($session->has('sourceCityId')) ? $session->get('sourceCityId') : null;
        $destinationCountryId = ($session->has('destinationCountryId')) ? $session->get('destinationCountryId') : null;
        $destinationStateId = ($session->has('destinationStateId')) ? $session->get('destinationStateId') : null;
        $destinationCityId = ($session->has('destinationCityId')) ? $session->get('destinationCityId') : null;
        $sortOrderFormatted = strtoupper($sortOrder);
        
        list($countryList, $sourceStateList, $sourceCityList, $destinationStateList, $destinationCityList) = $this->getSearchFilterOptions($sourceCountryId, $sourceStateId, $destinationCountryId, $destinationStateId);
        
        $deliveryChargeSearchForm = $this->createForm(DeliveryChargeSearchType::class, [], [
            'actionUrl' => $this->generateUrl('dashboard_delivery_charge_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'countryList' => $countryList,
            'sourceCountryId' => $sourceCountryId,
            'sourceStateList' => $sourceStateList,
            'sourceStateId' => $sourceStateId,
            'sourceCityList' => $sourceCityList,
            'sourceCityId' => $sourceCityId,
            'destinationCountryId' => $destinationCountryId,
            'destinationStateList' => $destinationStateList,
            'destinationStateId' => $destinationStateId,
            'destinationCityList' => $destinationCityList,
            'destinationCityId' => $destinationCityId,
        ]);
    
        $deliveryChargeSearchForm->handleRequest($request);
        
        if ($deliveryChargeSearchForm->isSubmitted()) {
            $deliveryChargeSearchFormData = $deliveryChargeSearchForm->getData();
            
            $session->set('sourceCountryId', $deliveryChargeSearchFormData['sourceCountryId']);
            $session->set('sourceStateId', $deliveryChargeSearchFormData['sourceStateId']);
            $session->set('sourceCityId', $deliveryChargeSearchFormData['sourceCityId']);
            $session->set('destinationCountryId', $deliveryChargeSearchFormData['destinationCountryId']);
            $session->set('destinationStateId', $deliveryChargeSearchFormData['destinationStateId']);
            $session->set('destinationCityId', $deliveryChargeSearchFormData['destinationCityId']);
            
            return $this->redirectToRoute('dashboard_delivery_charge_list');
        }
        
        $deliveryChargeCount = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->countDeliveryCharges($sourceCountryId, $sourceStateId, $sourceCityId, $destinationCountryId, $destinationStateId, $destinationCityId, $sortColumn, $sortOrderFormatted);
    
        $limit = 10;
        $neighbor = 3;
        $offset = $paginationService->getRecordOffset($page, $limit);
        
        $deliveryCharges = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findDeliveryCharges($offset, $limit, $sourceCountryId, $sourceStateId, $sourceCityId, $destinationCountryId, $destinationStateId, $destinationCityId, $sortColumn, $sortOrderFormatted);
    
        $totalPageCount = $paginationService->getTotalPageCount($limit, $deliveryChargeCount);
        $pageLinks = $paginationService->getPageLinks($page, $limit, $neighbor, $deliveryChargeCount, $totalPageCount, 'dashboard_delivery_charge_list', $routeParameters, []);
    
        $breadCrumbs = [
            [
                'title' => 'Dashboard Home',
                'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ],
            [
                'title' => 'Delivery Charge List',
                'link' => $this->generateUrl('dashboard_delivery_charge_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL),
            ],
        ];
    
        return $this->render('NetFlexDeliveryChargeBundle:DeliveryCharge:list.html.twig', [
            'pageTitle' => 'Delivery Charge List',
            'breadCrumbs' => $breadCrumbs,
            'pageHeader' => '<h1>Delivery Charge<small> list </small></h1>',
            'listHeader' => 'Delivery Charge List',
            'deliveryChargeSearchForm' => $deliveryChargeSearchForm->createView(),
            'deliveryChargeCount' => $deliveryChargeCount,
            'totalPageCount' => $totalPageCount,
            'deliveryCharges' => $deliveryCharges,
            'pageLinks' => $pageLinks,
            'referer' => urlencode($this->generateUrl('dashboard_delivery_charge_list', $routeParameters, UrlGeneratorInterface::ABSOLUTE_URL)),
            'routeParameters' => $routeParameters,
            'allRouteParameters' => $routeParameters,
        ]);
    }
    
    /**
     * Gets all the states of a country.
     *
     * @Route("/state-list", name="dashboard_delivery_charge_state_list")
     * @Method({"POST"})
     *
     * @param  Request       $request
     *
     * @return JsonResponse
     */
    public function dashboardDeliveryChargeStateListAction(Request $request)
    {
        $stateList = $cityList = [];
        $countryId = $request->request->get('countryId');
        $excludeStates = ($request->request->has('excludeStates')) ? $request->request->get('excludeStates') : false;
        
        if (! $countryId) {
            return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
        }
    
        $em = $this->getDoctrine()->getManager();
        
        $states = $em->getRepository('NetFlexLocationBundle:Country')->findOneById($countryId)->getStates();
        
        if (! $states) {
            return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
        }
        
        $cities = $states[0]->getCities();
        
        foreach ($states as $thisState) {
            if ($excludeStates && in_array($thisState->getId(), [42, 43, 44, 45, 46, 47])) {
                continue;
            }
            
            $stateList[$thisState->getId()] = $thisState->getName();
        }
        
        if (! $cities) {
            return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
        }
        
        foreach ($cities as $thisCity) {
            $cityList[$thisCity->getId()] = $thisCity->getName();
        }
        
        return $this->json(['stateList' => $stateList, 'cityList' => $cityList]);
    }
    
    /**
     * Gets all the cities of a state.
     *
     * @Route("/city-list", name="dashboard_delivery_charge_city_list")
     * @Method({"POST"})
     *
     * @param  Request      $request
     *
     * @return JsonResponse
     */
    public function dashboardDeliveryChargeCityListAction(Request $request)
    {
        $cityList = [];
        $stateId = $request->request->get('stateId');
        
        if (! $stateId) {
            return $this->json(['cityList' => $cityList]);
        }
        
        $cities = $this->getDoctrine()->getManager()->getRepository('NetFlexLocationBundle:State')->findOneById($stateId)->getCities();
        
        if (! $cities) {
            return $this->json(['cityList' => $cityList]);
        }
        
        foreach ($cities as $thisCity) {
            $cityList[$thisCity->getId()] = $thisCity->getName();
        }
        
        return $this->json(['cityList' => $cityList]);
    }
    
    /**
     * Exits from delivery charge search mode.
     *
     * @Route("/exit-search", name="dashboard_delivery_charge_exit_search")
     * @Method({"GET"})
     *
     * @param  Request          $request
     *
     * @return RedirectResponse
     */
    public function dashboardDeliveryChargeExitSearchAction(Request $request)
    {
        $session = $request->getSession();
        
        $session->remove('sourceCountryId');
        $session->remove('sourceStateId');
        $session->remove('sourceCityId');
        $session->remove('destinationCountryId');
        $session->remove('destinationStateId');
        $session->remove('destinationCityId');
        
        return $this->redirectToRoute('dashboard_delivery_charge_list');
    }
    
    /**
	 * Renders add delivery charge page.
	 *
	 * @Route("/new", name="dashboard_delivery_charge_new")
	 * @Method({"GET", "POST"})
	 *
	 * @param  Request $request
	 *
	 * @return Response
	 */
	public function dashboardDeliveryChargeNewAction(Request $request)
	{
        $deliveryZones = $this->getParameter('delivery_zones');
        
        $deliveryZoneTypeForm = $this->createForm(DeliveryZoneType::class, null, [
			'actionUrl' => $this->generateUrl('dashboard_delivery_charge_new'),
			'deliveryZones' => $deliveryZones,
		]);
		
		$deliveryZoneTypeForm->handleRequest($request);
		
		if ($deliveryZoneTypeForm->isSubmitted()) {
			$deliveryZoneData = $deliveryZoneTypeForm->getData();
            
            $deliveryChargeForm = $this->getDeliveryChargeNewTypeForm((isset($deliveryZoneData['deliveryZone']) &&
                $deliveryZoneData['deliveryZone']) ? $deliveryZoneData['deliveryZone'] : null);
            
            return $this->render('NetFlexDeliveryChargeBundle:DeliveryCharge:new_async.html.twig', [
                'deliveryZone' => $deliveryZoneData['deliveryZone'],
                'deliveryChargeForm' => ($deliveryChargeForm) ? $deliveryChargeForm->createView() : null,
            ]);
		}
		
		return $this->render('NetFlexDeliveryChargeBundle:DeliveryCharge:new.html.twig', [
			'pageTitle' => 'New Delivery Charge',
			'breadCrumbs' => [
				[
					'title' => 'Dashboard Home',
					'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
				],
				[
					'title' => 'Delivery Charges',
					'link' => 'javascript:void(0)',
				],
				[
					'title' => 'New Delivery Charge',
					'link' => $this->generateUrl('dashboard_delivery_charge_new', [], UrlGeneratorInterface::ABSOLUTE_URL),
				],
			],
			'pageHeader' => '<h1>New <small>delivery charge</small></h1>',
			'deliveryZoneTypeForm' => $deliveryZoneTypeForm->createView(),
            'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_delivery_charge_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
		]);
	}
    
    /**
     * Adds new delivery charge.
     *
     * @Route("/{deliveryZoneId}/add/", name="dashboard_delivery_charge_add")
     * @Method({"POST"})
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
	public function dashboardDeliveryChargeAddAction($deliveryZoneId, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $deliveryChargeForm = $this->getDeliveryChargeNewTypeForm($deliveryZoneId);
            
            $deliveryChargeForm->handleRequest($request);
            
            if ($deliveryChargeForm->isSubmitted()) {
                $validationErrors = $this->get('validator')->validate($deliveryChargeForm);
                
                if (0 < count($validationErrors)) {
                    $validationErrorList = [];
                    
                    foreach ($validationErrors as $thisValidationError) {
                        $validationErrorList[str_replace(['children[', '].data'], '',
                            $thisValidationError->getPropertyPath())] = $thisValidationError->getMessage();
                    }
                    
                    return $this->json(['status' => 'failure', 'reason' => 'validationError', 'validationErrorList' =>
                    $validationErrorList]);
                } else {
                    $formData = $deliveryChargeForm->getData();
    
                    $validationErrorList = [];
                    
                    if (1 == $deliveryZoneId) {
                        if ($formData['sourceCountryId'] != $formData['destinationCountryId']) {
                            $validationErrorList['destinationCountryId'] = 'Must be identical to source country';
                        }
    
                        if ($formData['sourceStateId'] != $formData['destinationStateId']) {
                            $validationErrorList['destinationStateId'] = 'Must be identical to source state';
                        }
    
                        if ($formData['sourceCityId'] != $formData['destinationCityId']) {
                            $validationErrorList['destinationCityId'] = 'Must be identical to source city';
                        }
                    } elseif (2 == $deliveryZoneId) {
                        if ($formData['sourceCountryId'] != $formData['destinationCountryId']) {
                            $validationErrorList['destinationCountryId'] = 'Must be identical to source country';
                        }
    
                        if ($formData['sourceStateId'] != $formData['destinationStateId']) {
                            $validationErrorList['destinationStateId'] = 'Must be identical to source state';
                        }
                        
                        if ($formData['sourceCityId'] == $formData['destinationCityId']) {
                            $validationErrorList['destinationCityId'] = 'Cannot be identical to source city';
                        }
                    } elseif (3 == $deliveryZoneId) {
                        if ($formData['sourceCountryId'] != $formData['destinationCountryId']) {
                            $validationErrorList['destinationCountryId'] = 'Must be identical to source country';
                        }
    
                        if ($formData['sourceStateId'] == $formData['destinationStateId']) {
                            $validationErrorList['destinationStateId'] = 'Cannot be identical to source state';
                        }
    
                        if ($formData['sourceCityId'] == $formData['destinationCityId']) {
                            $validationErrorList['destinationCityId'] = 'Cannot be identical to source city';
                        }
                    } else {
                        //
                    }
    
                    if ($validationErrorList) {
                        return $this->json(['status' => 'failure', 'reason' => 'validationError', 'validationErrorList' => $validationErrorList]);
                    }
    
                    $deliveryModeTimeline = new DeliveryModeTimeline();
                    $deliveryCharge = new DeliveryCharge();
    
                    $currentDateTime = new \DateTime();
                    $currentUser = $this->getUser()->getId();
    
                    $deliveryModeTimeline->setSourceCountryId((isset($formData['sourceCountryId']) && ! empty
                        ($formData['sourceCountryId'])) ? $formData['sourceCountryId'] : null);
                    $deliveryModeTimeline->setSourceStateId((isset($formData['sourceStateId']) && ! empty
                        ($formData['sourceStateId'])) ? $formData['sourceStateId'] : null);
                    $deliveryModeTimeline->setSourceCityId((isset($formData['sourceCityId']) && ! empty
                        ($formData['sourceCityId'])) ? $formData['sourceCityId'] : null);
                    $deliveryModeTimeline->setSourceZipCode((isset($formData['sourceZipCodeRange']) && ! empty
                        ($formData['sourceZipCodeRange'])) ? $formData['sourceZipCodeRange'] : null);
                    $deliveryModeTimeline->setDestinationCountryId((isset($formData['destinationCountryId']) && ! empty
                        ($formData['destinationCountryId'])) ? $formData['destinationCountryId'] : null);
                    $deliveryModeTimeline->setDestinationStateId((isset($formData['destinationStateId']) && ! empty
                        ($formData['destinationStateId'])) ? $formData['destinationStateId'] : null);
                    $deliveryModeTimeline->setDestinationCityId((isset($formData['destinationCityId']) && ! empty
                        ($formData['destinationCityId'])) ? $formData['destinationCityId'] : null);
                    $deliveryModeTimeline->setDestinationZipCode((isset($formData['destinationZipCodeRange']) && ! empty
                        ($formData['destinationZipCodeRange'])) ? $formData['destinationZipCodeRange'] : null);
                    $deliveryModeTimeline->setDeliveryModeId((isset($formData['deliveryModeId']) && ! empty
                        ($formData['deliveryModeId'])) ? $formData['deliveryModeId'] : null);
                    $deliveryModeTimeline->setDeliveryTimelineId((isset($formData['deliveryTimelineId']) && ! empty
                        ($formData['deliveryTimelineId'])) ? $formData['deliveryTimelineId'] : null);
                    $deliveryModeTimeline->setStatus(1);
                    $deliveryModeTimeline->setCreatedOn($currentDateTime);
                    $deliveryModeTimeline->setCreatedBy($currentUser);
                    $deliveryModeTimeline->setLastModifiedOn($currentDateTime);
                    $deliveryModeTimeline->setLastModifiedBy($currentUser);
    
                    $deliveryCharge->setSourceCountryId((isset($formData['sourceCountryId']) && ! empty
                        ($formData['sourceCountryId'])) ? $formData['sourceCountryId'] : null);
                    $deliveryCharge->setSourceStateId((isset($formData['sourceStateId']) && ! empty
                        ($formData['sourceStateId'])) ? $formData['sourceStateId'] : null);
                    $deliveryCharge->setSourceCityId((isset($formData['sourceCityId']) && ! empty
                        ($formData['sourceCityId'])) ? $formData['sourceCityId'] : null);
                    $deliveryCharge->setSourceZipCode((isset($formData['sourceZipCodeRange']) && ! empty
                        ($formData['sourceZipCodeRange'])) ? $formData['sourceZipCodeRange'] : null);
                    $deliveryCharge->setDestinationCountryId((isset($formData['destinationCountryId']) && ! empty
                        ($formData['destinationCountryId'])) ? $formData['destinationCountryId'] : null);
                    $deliveryCharge->setDestinationStateId((isset($formData['destinationStateId']) && ! empty
                        ($formData['destinationStateId'])) ? $formData['destinationStateId'] : null);
                    $deliveryCharge->setDestinationCityId((isset($formData['destinationCityId']) && ! empty
                        ($formData['destinationCityId'])) ? $formData['destinationCityId'] : null);
                    $deliveryCharge->setDestinationZipCode((isset($formData['destinationZipCodeRange']) && ! empty
                        ($formData['destinationZipCodeRange'])) ? $formData['destinationZipCodeRange'] : null);
                    $deliveryCharge->setDeliveryModeTimelineId($deliveryModeTimeline);
                    $deliveryCharge->setShipmentBaseWeightUpperLimit((isset($formData['baseWeight']) && ! empty
                        ($formData['baseWeight'])) ? $formData['baseWeight'] : null);
                    $deliveryCharge->setShipmentAccountableExtraWeight((isset($formData['extraWeight']) && ! empty
                        ($formData['extraWeight'])) ? $formData['extraWeight'] : null);
                    $deliveryCharge->setShipmentWeightUnitId((isset($formData['weightUnitId']) && ! empty
                        ($formData['weightUnitId'])) ? $formData['weightUnitId'] : null);
                    $deliveryCharge->setDeliveryBasePrice((isset($formData['basePrice']) && ! empty
                        ($formData['basePrice'])) ? $formData['basePrice'] : null);
                    $deliveryCharge->setDeliveryExtraPriceMultiplier((isset($formData['extraPriceMultiplier']) && ! empty
                        ($formData['extraPriceMultiplier'])) ? $formData['extraPriceMultiplier'] : null);
                    $deliveryCharge->setCodDeliveryBasePrice((isset($formData['codBasePrice']) && ! empty
                        ($formData['codBasePrice'])) ? $formData['codBasePrice'] : null);
                    $deliveryCharge->setFuelSurchargePercentageOnBasePrice((isset($formData['fuelSurchargePercentageOnBasePrice']) && ! empty
                        ($formData['fuelSurchargePercentageOnBasePrice'])) ? $formData['fuelSurchargePercentageOnBasePrice'] : null);
                    $deliveryCharge->setServiceTaxPercentageOnBasePrice((isset($formData['serviceTaxPercentageOnBasePrice']) && ! empty
                        ($formData['serviceTaxPercentageOnBasePrice'])) ? $formData['serviceTaxPercentageOnBasePrice'] : null);
                    $deliveryCharge->setShipmentRiskPercentageOnBasePrice((isset($formData['carrierRiskPercentageOnBasePrice']) && ! empty
                        ($formData['carrierRiskPercentageOnBasePrice'])) ? $formData['carrierRiskPercentageOnBasePrice'] : null);
                    $deliveryCharge->setDeliveryPriceUnitId((isset($formData['currencyUnitId']) && ! empty
                        ($formData['currencyUnitId'])) ? $formData['currencyUnitId'] : null);
                    $deliveryCharge->setStatus(1);
                    $deliveryCharge->setCreatedOn($currentDateTime);
                    $deliveryCharge->setCreatedBy($currentUser);
                    $deliveryCharge->setLastModifiedOn($currentDateTime);
                    $deliveryCharge->setLastModifiedBy($currentUser);
                    
                    $validationErrors = $this->get('validator')->validate($deliveryModeTimeline);
                    
                    if (0 < count($validationErrors)) {
                        return $this->json(['status' => 'failure', 'reason' => 'redundancyError', 'redundancyError' => $validationErrors[0]->getMessage()]);
                    } else {
                        $em = $this->getDoctrine()->getManager();
                        
                        $em->persist($deliveryModeTimeline);
                        $em->persist($deliveryCharge);
    
                        $em->flush();
    
                        return $this->json(['status' => 'success', 'redirectTo' => $this->generateUrl('dashboard_delivery_charge_edit', ['deliveryZoneId' => $deliveryZoneId, 'deliveryChargeId' =>
                            $deliveryCharge->getId
                            ()], UrlGeneratorInterface::ABSOLUTE_URL)]);
                    }
                }
            }
        }
    }
    
    /**
     * Creates and returns delivery charge new type form.
     *
     * @param  int                        $deliveryZone
     *
     * @return null|DeliveryChargeNewType
     */
	public function getDeliveryChargeNewTypeForm($deliveryZone)
    {
        if (! $deliveryZone) {
            return null;
        } else {
            $em = $this->getDoctrine()->getManager();
    
            /**
             * Get source location parameters.
             */
            $sourceCountries = $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findActiveCountries(1, 41, [42, 43, 44, 45, 46, 47]);
            $defaultSourceCountry = $defaultDestinationCountry = $this->getDefaultLocation(1, $sourceCountries);
            $sourceStates = $destinationStates = ($defaultSourceCountry) ? (! empty($defaultSourceCountry->getStates()) ? $defaultSourceCountry->getStates()->getValues() : []) : [];
            $defaultSourceState = $defaultDestinationState = $this->getDefaultLocation(41, $sourceStates);
            $sourceCities = $destinationCities = ($defaultSourceCountry) ? (! empty($defaultSourceCountry->getCities()) ? $defaultSourceCountry->getCities()->getValues() : []) : [];
            $defaultSourceCity = $defaultDestinationCity = $this->getDefaultLocation(5583, $sourceCities);
    
            /**
             * Get delivery modes.
             */
            $deliveryModes = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryMode')->findBy(['status' => 1]);
    
            /**
             * Get delivery timelines.
             */
            $deliveryTimelines = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryTimeline')->findBy(['status'
                => 1]);
    
            /**
             * Get weight units.
             */
            $weightUnits = $em->getRepository('NetFlexDeliveryChargeBundle:WeightUnit')->findBy(['status' => 1]);
            $defaultWeightUnit = $this->getDefaultUnit(1, $weightUnits);
    
            /**
             * Get currency units.
             */
            $currencyUnits = $em->getRepository('NetFlexDeliveryChargeBundle:Currency')->findBy(['status' => 1]);
            $defaultCurrencyUnit = $this->getDefaultUnit(1, $currencyUnits);
    
            /**
             * Construct delivery charge form options.
             */
            $deliveryChargeFormOptions = [
                'actionUrl' => $this->generateUrl('dashboard_delivery_charge_add', ['deliveryZoneId' => $deliveryZone],
                    UrlGeneratorInterface::ABSOLUTE_URL),
                'sourceCountries' => $sourceCountries,
                'defaultSourceCountry' => $defaultSourceCountry,
                'sourceStates' => $sourceStates,
                'defaultSourceState' => (is_array($defaultSourceState)) ? array_shift($defaultSourceState) : $defaultSourceState,
                'sourceCities' => $sourceCities,
                'defaultSourceCity' => (is_array($defaultSourceCity)) ? array_shift($defaultSourceCity) : $defaultSourceCity,
                'destinationCountries' => $destinationCountries,
                'defaultDestinationCountry' => $defaultDestinationCountry,
                'destinationStates' => $destinationStates,
                'defaultDestinationState' => (is_array($defaultDestinationState)) ? array_shift($defaultDestinationState) : $defaultDestinationState,
                'destinationCities' => $destinationCities,
                'defaultDestinationCity' => (is_array($defaultDestinationCity)) ? array_shift($defaultDestinationCity) : $defaultDestinationCity,
                'deliveryModes' => $deliveryModes,
                'deliveryTimelines' => $deliveryTimelines,
                'weightUnits' => $weightUnits,
                'defaultWeightUnit' => (is_array($defaultWeightUnit)) ? array_shift($defaultWeightUnit) : $defaultWeightUnit,
                'currencyUnits' => $currencyUnits,
                'defaultCurrencyUnit' => (is_array($defaultCurrencyUnit)) ? array_shift($defaultCurrencyUnit) : $defaultCurrencyUnit,
            ];
            
            switch($deliveryZone) {
                case 3:
                    /**
                     * Get destination location parameters.
                     */
                    $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findBy(['status' =>
                        1]);
                    $defaultDestinationCountry = ($destinationCountries) ? $this->getDefaultLocation(1,
                    $destinationCountries) : null;
                    $defaultDestinationCountry = (is_array($defaultDestinationCountry)) ? array_shift($defaultDestinationCountry) : $defaultDestinationCountry;
                    $destinationStates = ($defaultDestinationCountry) ? $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' => $defaultDestinationCountry->getId(), 'id' => [42, 43, 44, 45, 46, 47], 'status' => 1]) : [];
                    $defaultDestinationState = ($destinationStates) ? $this->getDefaultLocation(45,
                        $destinationStates) : null;
                    $defaultDestinationState = ($defaultDestinationState) ? ((is_array($defaultDestinationState)) ? array_shift($defaultDestinationState) : $defaultDestinationState) : null;
                    $destinationCities = ($defaultDestinationState) ? $em->getRepository('NetFlexLocationBundle:City')->findBy(['stateId' => $defaultDestinationState->getId(), 'status' => 1]) : [];
                    $defaultDestinationCity = ($destinationCities) ? $this->getDefaultLocation(5745,
                        $destinationCities) : null;
    
                    /**
                     * Modifiy delivery charge form options.
                     */
                    $deliveryChargeFormOptions = array_merge($deliveryChargeFormOptions, [
                        'deliveryZone' => 3,
                        'destinationCountries' => $destinationCountries,
                        'defaultDestinationCountry' => $defaultDestinationCountry,
                        'destinationStates' => $destinationStates,
                        'defaultDestinationState' => $defaultDestinationState,
                        'destinationCities' => $destinationCities,
                        'defaultDestinationCity' => $defaultDestinationCity,
                    ]);
                    
                    break;
        
                case 2:
                    /**
                     * Modifiy delivery charge form options.
                     */
                    $deliveryChargeFormOptions = array_merge(['deliveryZone' => 2], $deliveryChargeFormOptions);
                    
                    break;
        
                case 1:
                default:
                /**
                 * Modifiy delivery charge form options.
                 */
                $deliveryChargeFormOptions = array_merge(['deliveryZone' => 1], $deliveryChargeFormOptions);
                    
                    break;
            }
    
            /**
             * Create and return the delivery charge form.
             */
            return $this->createForm(DeliveryChargeNewType::class, null, $deliveryChargeFormOptions);
        }
    }
    
    /**
     * Renders edit delivery charge page.
     *
     * @Route("/edit/{deliveryZoneId}/{deliveryChargeId}", name="dashboard_delivery_charge_edit",
     *     requirements={"deliveryZoneId": "\d+", "deliveryChargeId": "\d+"})
     * @Method({"GET", "POST"})
     *
     * @param  int     $deliveryZoneId
     * @param  int     $deliveryChargeId
     * @param  Request $request
     *
     * @return Response
     */
    public function dashboardDeliveryChargeEditAction($deliveryZoneId, $deliveryChargeId, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        $deliveryCharge = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findDeliveryChargeById
        ($deliveryChargeId);
        
        if (! $deliveryCharge) {
            throw $this->createNotFoundException('Delivery charge not found');
        } else {
            $deliveryModeTimeline = $deliveryCharge->getDeliveryModeTimelineId();
    
            $deliveryChargeForm = $this->getDeliveryChargeEditTypeForm($deliveryZoneId, $deliveryCharge, $deliveryModeTimeline);
    
            return $this->render('NetFlexDeliveryChargeBundle:DeliveryCharge:edit.html.twig', [
                'pageTitle' => 'Edit Delivery Charge',
                'breadCrumbs' => [
                    [
                        'title' => 'Dashboard Home',
                        'link' => $this->generateUrl('dashboard', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                    [
                        'title' => 'Delivery Charges',
                        'link' => 'javascript:void(0)',
                    ],
                    [
                        'title' => 'Edit Delivery Charge',
                        'link' => $this->generateUrl('dashboard_delivery_charge_edit', ['deliveryZoneId' => $deliveryZoneId, 'deliveryChargeId' => $deliveryChargeId], UrlGeneratorInterface::ABSOLUTE_URL),
                    ],
                ],
                'pageHeader' => '<h1>Edit <small>delivery charge</small></h1>',
                'deliveryZone' => $deliveryZoneId,
                'deliveryChargeForm' => $deliveryChargeForm->createView(),
                'referer' => ($request->query->has('referer')) ? urldecode($request->query->get('referer')) : $this->generateUrl('dashboard_delivery_charge_list', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        }
    }
    
    /**
     * Changes delivery zone.
     *
     * @Route("/change-delivery-zone", name="dashboard_delivery_charge_change_zone")
     * @Method({"POST"})
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function dashboardDeliveryChargeChangeZoneAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
            
            $selectedDeliveryZoneId = $request->request->get('selectedDeliveryZoneId');
            
            switch ($selectedDeliveryZoneId) {
                case 3:
                    $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findBy(['status' =>
                        1]);
                    $defaultDestinationCountry = $this->getDefaultLocation(1, $destinationCountries);
                    $defaultDestinationCountry = (is_array($defaultDestinationCountry)) ? array_shift($defaultDestinationCountry) : $defaultDestinationCountry;
                    $destinationStates = ($defaultDestinationCountry) ? $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' =>
                        $defaultDestinationCountry->getId(), 'id' => [42, 43, 44, 45, 46, 47], 'status' => 1]) : [];
                    $defaultDestinationState = $this->getDefaultLocation(45, $destinationStates);
                    $defaultDestinationState = (is_array($defaultDestinationState)) ? array_shift($defaultDestinationState) : $defaultDestinationState;
                    $destinationCities = ($defaultDestinationState) ? $em->getRepository('NetFlexLocationBundle:City')->findBy(['stateId' => $defaultDestinationState->getId(), 'status' => 1]) : [];
                    $defaultDestinationCity = $this->getDefaultLocation(5742, $destinationCities);
                    
                    return $this->json([
                        'status' => 'success',
                        'destinationCountryList' => $this->getLocationList($destinationCountries),
                        'defaultDestinationCountry' => $defaultDestinationCountry->getId(),
                        'destinationStateList' => $this->getLocationList($destinationStates),
                        'defaultDestinationState' => $defaultDestinationState->getId(),
                        'destinationCityList' => $this->getLocationList($destinationCities),
                        'defaultDestinationCity' => $defaultDestinationCity->getId(),
                    ]);
                    
                    break;
                
                case 2:
                    $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findActiveCountries(1, 41, [42, 43, 44, 45, 46, 47]);
                    $defaultDestinationCountry = $this->getDefaultLocation(1, $destinationCountries);
                    $destinationStates = ($defaultDestinationCountry) ? (! empty($defaultDestinationCountry->getStates()) ? $defaultDestinationCountry->getStates()->getValues() : []) : [];
                    $defaultDestinationState = $this->getDefaultLocation(41, $destinationStates);
                    $defaultDestinationState = (is_array($defaultDestinationState)) ? array_shift($defaultDestinationState) : $defaultDestinationState;
                    $destinationCities = ($defaultDestinationCountry) ? (! empty($defaultDestinationCountry->getCities()) ? $defaultDestinationCountry->getCities()->getValues() : []) : [];
                    $defaultDestinationCity = $this->getDefaultLocation(5583, $destinationCities);
                    $defaultDestinationCity = (is_array($defaultDestinationCity)) ? array_shift($defaultDestinationCity) : $defaultDestinationCity;
    
                    return $this->json([
                        'status' => 'success',
                        'destinationCountryList' => $this->getLocationList($destinationCountries),
                        'defaultDestinationCountry' => $defaultDestinationCountry->getId(),
                        'destinationStateList' => $this->getLocationList($destinationStates),
                        'defaultDestinationState' => $defaultDestinationState->getId(),
                        'destinationCityList' => $this->getLocationList($destinationCities),
                        'defaultDestinationCity' => $defaultDestinationCity->getId(),
                    ]);
                    
                    break;
                    
                case 1:
                default:
                    $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findActiveCountries(1, 41, [42, 43, 44, 45, 46, 47]);
                    $defaultDestinationCountry = $this->getDefaultLocation(1, $destinationCountries);
                    $destinationStates = ($defaultDestinationCountry) ? (! empty($defaultDestinationCountry->getStates()) ? $defaultDestinationCountry->getStates()->getValues() : []) : [];
                    $defaultDestinationState = $this->getDefaultLocation(41, $destinationStates);
                    $defaultDestinationState = (is_array($defaultDestinationState)) ? array_shift($defaultDestinationState) : $defaultDestinationState;
                    $destinationCities = ($defaultDestinationCountry) ? (! empty($defaultDestinationCountry->getCities()) ? $defaultDestinationCountry->getCities()->getValues() : []) : [];
                    $defaultDestinationCity = $this->getDefaultLocation(5583, $destinationCities);
                    $defaultDestinationCity = (is_array($defaultDestinationCity)) ? array_shift($defaultDestinationCity) : $defaultDestinationCity;
        
                    return $this->json([
                        'status' => 'success',
                        'destinationCountryList' => $this->getLocationList($destinationCountries),
                        'defaultDestinationCountry' => $defaultDestinationCountry->getId(),
                        'destinationStateList' => $this->getLocationList($destinationStates),
                        'defaultDestinationState' => $defaultDestinationState->getId(),
                        'destinationCityList' => $this->getLocationList($destinationCities),
                        'defaultDestinationCity' => $defaultDestinationCity->getId(),
                    ]);
                
                    break;
            }
        }
    }
    
    /**
     * Updates a delivery charge.
     *
     * @Route("/update/{deliveryZoneId}/{deliveryChargeId}", name="dashboard_delivery_charge_update")
     * @Method({"POST"})
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function dashboardDeliveryChargeUpdateAction($deliveryZoneId, $deliveryChargeId, Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $em = $this->getDoctrine()->getManager();
    
            $deliveryCharge = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findDeliveryChargeById
            ($deliveryChargeId);
    
            if (! $deliveryCharge) {
                throw $this->createNotFoundException('Delivery charge not found');
            } else {
                $deliveryModeTimeline = $deliveryCharge->getDeliveryModeTimelineId();
    
                $deliveryChargeForm = $this->getDeliveryChargeEditTypeForm($deliveryZoneId, $deliveryCharge, $deliveryModeTimeline);
    
                $deliveryChargeForm->handleRequest($request);
    
                if ($deliveryChargeForm->isSubmitted()) {
                    $validationErrors = $this->get('validator')->validate($deliveryChargeForm);
        
                    if (0 < count($validationErrors)) {
                        $validationErrorList = [];
            
                        foreach ($validationErrors as $thisValidationError) {
                            $validationErrorList[str_replace(['children[', '].data'], '',
                                $thisValidationError->getPropertyPath())] = $thisValidationError->getMessage();
                        }
            
                        return $this->json(['status' => 'failure', 'reason' => 'validationError', 'validationErrorList' =>
                            $validationErrorList]);
                    } else {
                        $formData = $deliveryChargeForm->getData();
    
                        $validationErrorList = [];
    
                        if (1 == $deliveryZoneId) {
                            if ($formData['sourceCountryId'] != $formData['destinationCountryId']) {
                                $validationErrorList['destinationCountryId'] = 'Must be identical to source country';
                            }
        
                            if ($formData['sourceStateId'] != $formData['destinationStateId']) {
                                $validationErrorList['destinationStateId'] = 'Must be identical to source state';
                            }
        
                            if ($formData['sourceCityId'] != $formData['destinationCityId']) {
                                $validationErrorList['destinationCityId'] = 'Must be identical to source city';
                            }
                        } elseif (2 == $deliveryZoneId) {
                            if ($formData['sourceCountryId'] != $formData['destinationCountryId']) {
                                $validationErrorList['destinationCountryId'] = 'Must be identical to source country';
                            }
        
                            if ($formData['sourceStateId'] != $formData['destinationStateId']) {
                                $validationErrorList['destinationStateId'] = 'Must be identical to source state';
                            }
        
                            if ($formData['sourceCityId'] == $formData['destinationCityId']) {
                                $validationErrorList['destinationCityId'] = 'Cannot be identical to source city';
                            }
                        } elseif (3 == $deliveryZoneId) {
                            if ($formData['sourceCountryId'] != $formData['destinationCountryId']) {
                                $validationErrorList['destinationCountryId'] = 'Must be identical to source country';
                            }
        
                            if ($formData['sourceStateId'] == $formData['destinationStateId']) {
                                $validationErrorList['destinationStateId'] = 'Cannot be identical to source state';
                            }
        
                            if ($formData['sourceCityId'] == $formData['destinationCityId']) {
                                $validationErrorList['destinationCityId'] = 'Cannot be identical to source city';
                            }
                        } else {
                            //
                        }
    
                        if ($validationErrorList) {
                            return $this->json(['status' => 'failure', 'reason' => 'validationError', 'validationErrorList' => $validationErrorList]);
                        }
            
                        $currentDateTime = new \DateTime();
                        $currentUser = $this->getUser()->getId();
            
                        $deliveryModeTimeline->setSourceCountryId((isset($formData['sourceCountryId']) && ! empty
                            ($formData['sourceCountryId'])) ? $formData['sourceCountryId'] : null);
                        $deliveryModeTimeline->setSourceStateId((isset($formData['sourceStateId']) && ! empty
                            ($formData['sourceStateId'])) ? $formData['sourceStateId'] : null);
                        $deliveryModeTimeline->setSourceCityId((isset($formData['sourceCityId']) && ! empty
                            ($formData['sourceCityId'])) ? $formData['sourceCityId'] : null);
                        $deliveryModeTimeline->setSourceZipCode((isset($formData['sourceZipCodeRange']) && ! empty
                            ($formData['sourceZipCodeRange'])) ? $formData['sourceZipCodeRange'] : null);
                        $deliveryModeTimeline->setDestinationCountryId((isset($formData['destinationCountryId']) && ! empty
                            ($formData['destinationCountryId'])) ? $formData['destinationCountryId'] : null);
                        $deliveryModeTimeline->setDestinationStateId((isset($formData['destinationStateId']) && ! empty
                            ($formData['destinationStateId'])) ? $formData['destinationStateId'] : null);
                        $deliveryModeTimeline->setDestinationCityId((isset($formData['destinationCityId']) && ! empty
                            ($formData['destinationCityId'])) ? $formData['destinationCityId'] : null);
                        $deliveryModeTimeline->setDestinationZipCode((isset($formData['destinationZipCodeRange']) && ! empty
                            ($formData['destinationZipCodeRange'])) ? $formData['destinationZipCodeRange'] : null);
                        $deliveryModeTimeline->setDeliveryModeId((isset($formData['deliveryModeId']) && ! empty
                            ($formData['deliveryModeId'])) ? $formData['deliveryModeId'] : null);
                        $deliveryModeTimeline->setDeliveryTimelineId((isset($formData['deliveryTimelineId']) && ! empty
                            ($formData['deliveryTimelineId'])) ? $formData['deliveryTimelineId'] : null);
                        $deliveryModeTimeline->setStatus(1);
                        $deliveryModeTimeline->setCreatedOn($currentDateTime);
                        $deliveryModeTimeline->setCreatedBy($currentUser);
                        $deliveryModeTimeline->setLastModifiedOn($currentDateTime);
                        $deliveryModeTimeline->setLastModifiedBy($currentUser);
            
                        $deliveryCharge->setSourceCountryId((isset($formData['sourceCountryId']) && ! empty
                            ($formData['sourceCountryId'])) ? $formData['sourceCountryId'] : null);
                        $deliveryCharge->setSourceStateId((isset($formData['sourceStateId']) && ! empty
                            ($formData['sourceStateId'])) ? $formData['sourceStateId'] : null);
                        $deliveryCharge->setSourceCityId((isset($formData['sourceCityId']) && ! empty
                            ($formData['sourceCityId'])) ? $formData['sourceCityId'] : null);
                        $deliveryCharge->setSourceZipCode((isset($formData['sourceZipCodeRange']) && ! empty
                            ($formData['sourceZipCodeRange'])) ? $formData['sourceZipCodeRange'] : null);
                        $deliveryCharge->setDestinationCountryId((isset($formData['destinationCountryId']) && ! empty
                            ($formData['destinationCountryId'])) ? $formData['destinationCountryId'] : null);
                        $deliveryCharge->setDestinationStateId((isset($formData['destinationStateId']) && ! empty
                            ($formData['destinationStateId'])) ? $formData['destinationStateId'] : null);
                        $deliveryCharge->setDestinationCityId((isset($formData['destinationCityId']) && ! empty
                            ($formData['destinationCityId'])) ? $formData['destinationCityId'] : null);
                        $deliveryCharge->setDestinationZipCode((isset($formData['destinationZipCodeRange']) && ! empty
                            ($formData['destinationZipCodeRange'])) ? $formData['destinationZipCodeRange'] : null);
                        $deliveryCharge->setDeliveryModeTimelineId($deliveryModeTimeline);
                        $deliveryCharge->setShipmentBaseWeightUpperLimit((isset($formData['baseWeight']) && ! empty
                            ($formData['baseWeight'])) ? $formData['baseWeight'] : null);
                        $deliveryCharge->setShipmentAccountableExtraWeight((isset($formData['extraWeight']) && ! empty
                            ($formData['extraWeight'])) ? $formData['extraWeight'] : null);
                        $deliveryCharge->setShipmentWeightUnitId((isset($formData['weightUnitId']) && ! empty
                            ($formData['weightUnitId'])) ? $formData['weightUnitId'] : null);
                        $deliveryCharge->setDeliveryBasePrice((isset($formData['basePrice']) && ! empty
                            ($formData['basePrice'])) ? $formData['basePrice'] : null);
                        $deliveryCharge->setDeliveryExtraPriceMultiplier((isset($formData['extraPriceMultiplier']) && ! empty
                            ($formData['extraPriceMultiplier'])) ? $formData['extraPriceMultiplier'] : null);
                        $deliveryCharge->setCodDeliveryBasePrice((isset($formData['codBasePrice']) && ! empty
                            ($formData['codBasePrice'])) ? $formData['codBasePrice'] : null);
                        $deliveryCharge->setFuelSurchargePercentageOnBasePrice((isset($formData['fuelSurchargePercentageOnBasePrice']) && ! empty
                            ($formData['fuelSurchargePercentageOnBasePrice'])) ? $formData['fuelSurchargePercentageOnBasePrice'] : null);
                        $deliveryCharge->setServiceTaxPercentageOnBasePrice((isset($formData['serviceTaxPercentageOnBasePrice']) && ! empty
                            ($formData['serviceTaxPercentageOnBasePrice'])) ? $formData['serviceTaxPercentageOnBasePrice'] : null);
                        $deliveryCharge->setShipmentRiskPercentageOnBasePrice((isset($formData['carrierRiskPercentageOnBasePrice']) && ! empty
                            ($formData['carrierRiskPercentageOnBasePrice'])) ? $formData['carrierRiskPercentageOnBasePrice'] : null);
                        $deliveryCharge->setDeliveryPriceUnitId((isset($formData['currencyUnitId']) && ! empty
                            ($formData['currencyUnitId'])) ? $formData['currencyUnitId'] : null);
                        $deliveryCharge->setStatus(1);
                        $deliveryCharge->setCreatedOn($currentDateTime);
                        $deliveryCharge->setCreatedBy($currentUser);
                        $deliveryCharge->setLastModifiedOn($currentDateTime);
                        $deliveryCharge->setLastModifiedBy($currentUser);
            
                        $validationErrors = $this->get('validator')->validate($deliveryModeTimeline);
            
                        if (0 < count($validationErrors)) {
                            return $this->json(['status' => 'failure', 'reason' => 'redundancyError', 'redundancyError' => $validationErrors[0]->getMessage()]);
                        } else {
                            $em = $this->getDoctrine()->getManager();
                
                            $em->persist($deliveryModeTimeline);
                            $em->persist($deliveryCharge);
                
                            $em->flush();
                            
                            return $this->json(['status' => 'success', 'redirectTo' => $this->generateUrl('dashboard_delivery_charge_edit', ['deliveryZoneId' => $deliveryZoneId, 'deliveryChargeId' => $deliveryCharge->getId()], UrlGeneratorInterface::ABSOLUTE_URL)]);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Creates and returns delivery charge edit form.
     *
     * @param  int                        $deliveryZone
     * @param  DeliveryCharge             $deliveryCharge
     * @param  DeliveryModeTimeleine      $deliveryModeTimeline
     *
     * @return null|DeliveryChargeNewType
     */
    public function getDeliveryChargeEditTypeForm($deliveryZone, $deliveryCharge, $deliveryModeTimeline)
    {
        $em = $this->getDoctrine()->getManager();
    
        /**
         * Get delivery zones.
         */
        $deliveryZones = $this->getParameter('delivery_zones');
    
        /**
         * Get source location parameters.
         */
        $sourceCountries = $em->getRepository('NetFlexLocationBundle:Country')
        ->findActiveCountries(1,
            $deliveryModeTimeline->getSourceStateId()->getId(), [42,
            43, 44, 45, 46, 47]);
        $defaultSourceCountry = $deliveryModeTimeline->getSourceCountryId();
        $sourceStates = ($defaultSourceCountry) ? (! empty($defaultSourceCountry->getStates()) ? $defaultSourceCountry->getStates()->getValues() : []) : [];
        $defaultSourceState = $deliveryModeTimeline->getSourceStateId();
        $sourceCities = ($defaultSourceCountry) ? (! empty($defaultSourceCountry->getCities()) ? $defaultSourceCountry->getCities()->getValues() : []) : [];
        $defaultSourceCity = $deliveryModeTimeline->getSourceCityId();
    
        /**
         * Get delivery modes.
         */
        $deliveryModes = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryMode')->findBy(['status' => 1]);
        $defaultDeliveryMode = $deliveryModeTimeline->getDeliveryModeId();
    
        /**
         * Get delivery timelines.
         */
        $deliveryTimelines = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryTimeline')->findBy(['status'
        => 1]);
        $defaultDeliveryTimeline = $deliveryModeTimeline->getDeliveryTimelineId();
    
        /**
         * Get weight units.
         */
        $weightUnits = $em->getRepository('NetFlexDeliveryChargeBundle:WeightUnit')->findBy(['status' => 1]);
        $defaultWeightUnit = $deliveryCharge->getShipmentWeightUnitId();
    
        /**
         * Get currency units.
         */
        $currencyUnits = $em->getRepository('NetFlexDeliveryChargeBundle:Currency')->findBy(['status' => 1]);
        $defaultCurrencyUnit = $deliveryCharge->getDeliveryPriceUnitId();
    
        /**
         * Construct delivery charge form options.
         */
        $deliveryChargeFormOptions = [
            'actionUrl' => $this->generateUrl('dashboard_delivery_charge_update', ['deliveryZoneId' => 9999999999, 'deliveryChargeId' => $deliveryCharge->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL),
            'deliveryZones' => $deliveryZones,
            'sourceCountries' => $sourceCountries,
            'defaultSourceCountry' => $defaultSourceCountry,
            'sourceStates' => $sourceStates,
            'defaultSourceState' => $defaultSourceState,
            'sourceCities' => $sourceCities,
            'defaultSourceCity' => $defaultSourceCity,
            'deliveryModes' => $deliveryModes,
            'defaultDeliveryMode' => $defaultDeliveryMode,
            'deliveryTimelines' => $deliveryTimelines,
            'defaultDeliveryTimeline' => $defaultDeliveryTimeline,
            'weightUnits' => $weightUnits,
            'defaultWeightUnit' => $defaultWeightUnit,
            'currencyUnits' => $currencyUnits,
            'defaultCurrencyUnit' => $defaultCurrencyUnit,
            'baseWeight' => $deliveryCharge->getShipmentBaseWeightUpperLimit(),
            'extraWeight' => $deliveryCharge->getShipmentAccountableExtraWeight(),
            'basePrice' => $deliveryCharge->getDeliveryBasePrice(),
            'extraPriceMultiplier' => $deliveryCharge->getDeliveryExtraPriceMultiplier(),
            'codBasePrice' => $deliveryCharge->getCodDeliveryBasePrice(),
            'fuelSurchargePercentageOnBasePrice' => $deliveryCharge->getFuelSurchargePercentageOnBasePrice(),
            'serviceTaxPercentageOnBasePrice' => $deliveryCharge->getServiceTaxPercentageOnBasePrice(),
            'carrierRiskPercentageOnBasePrice' => $deliveryCharge->getShipmentRiskPercentageOnBasePrice(),
        ];
        
        switch ($deliveryZone) {
            case 3:
                /**
                 * Get destination location parameters.
                 */
                $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findBy(['status' =>
                    1]);
                $defaultDestinationCountry = $deliveryModeTimeline->getDestinationCountryId();
                $destinationStates = ($defaultDestinationCountry) ? $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' => $defaultDestinationCountry->getId(), 'id' => [42, 43, 44, 45, 46, 47], 'status' => 1]) : [];
                $defaultDestinationState = $deliveryModeTimeline->getDestinationStateId();
                $destinationCities = ($defaultDestinationState) ? $em->getRepository('NetFlexLocationBundle:City')->findBy(['stateId' => $defaultDestinationState->getId(), 'status' => 1]) : [];
                $defaultDestinationCity = $deliveryModeTimeline->getDestinationCityId();
    
                /**
                 * Modify delivery charge form options.
                 */
                $deliveryChargeFormOptions = array_merge($deliveryChargeFormOptions, [
                    'deliveryZone' => 3,
                    'destinationCountries' => $destinationCountries,
                    'defaultDestinationCountry' => $defaultDestinationCountry,
                    'destinationStates' => $destinationStates,
                    'defaultDestinationState' => $defaultDestinationState,
                    'destinationCities' => $destinationCities,
                    'defaultDestinationCity' => $defaultDestinationCity,
                ]);
                
                break;
            
            case 2:
                /**
                 * Get destination location parameters.
                 */
                $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findBy(['status' =>
                    1]);
                $defaultDestinationCountry = $deliveryModeTimeline->getDestinationCountryId();
                $destinationStates = ($defaultDestinationCountry) ? $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' => $defaultDestinationCountry->getId(), 'status' => 1]) : [];
                foreach ($destinationStates as $key => $thisDestinationState) {
                    if (in_array($thisDestinationState->getId(), [42, 43, 44, 45, 46, 47])) {
                        unset($destinationStates[$key]);
                    }
                }
                $defaultDestinationState = $deliveryModeTimeline->getDestinationStateId();
                $destinationCities = ($defaultDestinationState) ? $em->getRepository('NetFlexLocationBundle:City')->findBy(['stateId' => $defaultDestinationState->getId(), 'status' => 1]) : [];
                $defaultDestinationCity = $deliveryModeTimeline->getDestinationCityId();
                
                /**
                 * Modify delivery charge form options.
                 */
                $deliveryChargeFormOptions = array_merge($deliveryChargeFormOptions, [
                    'deliveryZone' => 2,
                    'destinationCountries' => $destinationCountries,
                    'defaultDestinationCountry' => $defaultDestinationCountry,
                    'destinationStates' => $destinationStates,
                    'defaultDestinationState' => $defaultDestinationState,
                    'destinationCities' => $destinationCities,
                    'defaultDestinationCity' => $defaultDestinationCity,
                ]);
                
                break;
            
            case 1:
            default:
                /**
                 * Get destination location parameters.
                 */
                $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findBy(['status' =>
                    1]);
                $defaultDestinationCountry = $deliveryModeTimeline->getDestinationCountryId();
                $destinationStates = ($defaultDestinationCountry) ? $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' => $defaultDestinationCountry->getId(), 'status' => 1]) : [];
                foreach ($destinationStates as $key => $thisDestinationState) {
                    if (in_array($thisDestinationState->getId(), [42, 43, 44, 45, 46, 47])) {
                        unset($destinationStates[$key]);
                    }
                }
                $defaultDestinationState = $deliveryModeTimeline->getDestinationStateId();
                $destinationCities = ($defaultDestinationState) ? $em->getRepository('NetFlexLocationBundle:City')->findBy(['stateId' => $defaultDestinationState->getId(), 'status' => 1]) : [];
                $defaultDestinationCity = $deliveryModeTimeline->getDestinationCityId();
                
                /**
                 * Get source and destination zip code range.
                 */
                $sourceZipCodeRange = $deliveryCharge->getSourceZipCode();
                $destinationZipCodeRange = $deliveryCharge->getDestinationZipCode();
    
                /**
                 * Modify delivery charge form options.
                 */
                $deliveryChargeFormOptions = array_merge($deliveryChargeFormOptions, [
                    'deliveryZone' => 1,
                    'sourceZipCodeRange' => $sourceZipCodeRange,
                    'destinationCountries' => $destinationCountries,
                    'defaultDestinationCountry' => $defaultDestinationCountry,
                    'destinationStates' => $destinationStates,
                    'defaultDestinationState' => $defaultDestinationState,
                    'destinationCities' => $destinationCities,
                    'defaultDestinationCity' => $defaultDestinationCity,
                    'destinationZipCodeRange' => $destinationZipCodeRange,
                ]);
            
                break;
        }
    
        /**
         * Create and return the delivery charge form.
         */
        return $this->createForm(DeliveryChargeEditType::class, null, $deliveryChargeFormOptions);
    }
    
    /**
     * Changes status for a delivery charge.
     *
     * @Route("/status-change/{changeStatusTo}/{deliveryChargeId}", name="dashboard_delivery_charge_status_change", requirements={"deliveryChargeId": "\d+"})
     * @Method({"GET"})
     *
     * @param  string            $changeStatusTo
     * @param  int               $deliveryChargeId
     * @param  Request           $request
     *
     * @return RedirectResponse
     */
    public function DashboardDeliveryChargeStatusChangeAction($changeStatusTo, $deliveryChargeId, Request $request)
    {
        $changeStatusTo = ('activate' == $changeStatusTo) ? 1 : 0;
        $referer = ($request->query->has('referer')) ? $request->query->get('referer') : $this->generateUrl('dashboard_delivery_charge_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
        
        if (! $this->changeDeliveryChargeStatus($deliveryChargeId, $changeStatusTo)) {
            $this->addFlash('error', 'Selected delivery charge statuse could not be changed');
        } else {
            $this->addFlash('success', 'Selected delivery charge status was changed successfully');
        }
        
        return $this->redirect(urldecode($referer));
    }
    
    /**
     * Changes status for one/multiple delivery charge(s) at a go.
     *
     * @Route("/bulk-status-change", name="dashboard_delivery_charge_bulk_status_change")
     * @Method({"POST"})
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function DashboardDeliveryChargeBulkStatusChangeAction(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $deliveryChargeIds = $request->request->get('deliveryChargeIds');
            $changeStatusTo = $request->request->get('changeStatusTo');
            $deliveryChargeIds = (false === strpos($deliveryChargeIds, '-')) ? [$deliveryChargeIds] : explode('-', $deliveryChargeIds);
            $unaffectedDeliveryChargeIds = [];
            
            foreach ($deliveryChargeIds as $thisDeliveryChargeId) {
                if (! $this->changeDeliveryChargeStatus($thisDeliveryChargeId, $changeStatusTo)) {
                    $unaffectedDeliveryChargeIds[] = $thisDeliveryChargeId;
                }
            }
            
            if ($unaffectedDeliveryChargeIds) {
                if (count($deliveryChargeIds) == count($unaffectedDeliveryChargeIds)) {
                    $this->addFlash('error', 'Selected delivery charge statuses could not be changed');
                } else {
                    $this->addFlash('warning', 'Statuses for delivery charges with ids: ' . implode(', ', $unaffectedDeliveryChargeIds) . ' could not be changed');
                }
            } else {
                $this->addFlash('success', 'Selected delivery charge statuses were changed successfully');
            }
            
            return $this->json(['status' => 'complete']);
        }
    }
    
    /**
     * Gets a default location based on user-supplied option.
     *
     * @param  int   $defaultLocationId
     * @param  array $locations
     *
     * @return array|null
     */
    private function getDefaultLocation($defaultLocationId, $locations)
    {
        if (empty($locations)) {
            /**
             * No locations exist.
             */
            return null;
        }
        
        if (1 == count($locations)) {
            /**
             * Only one location exist.
             */
            return $locations[0];
        }
        
        /**
         * Filter out default location from locations array.
         */
        return array_filter($locations, function($thisLocation) use($defaultLocationId) {
            if ($defaultLocationId == $thisLocation->getId()) {
                return $thisLocation;
            }
        });
    }
    
    /**
     * Gets a default weight|currency unit based on user supplied option.
     *
     * @param int   $defaultUnitId
     * @param array $units
     *
     * @return array
     */
    private function getDefaultUnit($defaultUnitId, $units)
    {
        return array_filter($units, function($thisUnit) use($defaultUnitId) {
            if ($defaultUnitId == $thisUnit->getId()) {
                return $thisUnit;
            }
        });
    }
    
    /**
     * Gets location list.
     *
     * @param  array $locations
     *
     * @return array
     */
    private function getLocationList($locations)
    {
        $locationList = [];
        
        foreach ($locations as $thisLocation) {
            $locationList[$thisLocation->getId()] = $thisLocation->getName();
        }
        
        return $locationList;
    }
    
    /**
     * Gets inversed location list.
     *
     * @param  array $locations
     *
     * @return array
     */
    private function getInversedLocationList($locations)
    {
        $locationList = [];
        
        foreach ($locations as $thisLocation) {
            $locationList[$thisLocation->getName()] = $thisLocation->getId();
        }
        
        return $locationList;
    }
    
    /**
     * Gets options for delivery charge search form.
     *
     * @param int $sourceCountryId
     * @param int $sourceStateId
     * @param int $destinationCountryId
     * @param int $destinationStateId
     *
     * @return array
     */
    private function getSearchFilterOptions($sourceCountryId, $sourceStateId, $destinationCountryId, $destinationStateId)
    {
        $em = $this->getDoctrine()->getManager();
        
        $countryList = $sourceStateList = $sourceCityList = $destinationStateList = $destinationCityList = [];
        
        $countries = $em->getRepository('NetFlexLocationBundle:Country')->findBy(['status' => 1]);
        $countryList = $this->getInversedLocationList($countries);
        
        if ($sourceCountryId) {
            $sourceStates = $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' => $sourceCountryId, 'status' => 1]);
            
            foreach ($sourceStates as $key => $thisSourceState) {
                if (in_array($thisSourceState->getId(), [42, 43, 44, 45, 46, 47])) {
                    unset($sourceStates[$key]);
                }
            }
            
            $sourceStateList = $this->getInversedLocationList($sourceStates);
        }
        
        if ($sourceStateId) {
            $sourceCities = $em->getRepository('NetFlexLocationBundle:City')->findBy(['stateId' => $sourceStateId, 'status' => 1]);
            $sourceCityList = $this->getInversedLocationList($sourceCities);
        }
        
        if ($destinationCountryId) {
            $destinationStates = $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' => $destinationCountryId, 'status' => 1]);
            $destinationStateList = $this->getInversedLocationList($destinationStates);
        }
        
        if ($destinationStateId) {
            $destinationCities = $em->getRepository('NetFlexLocationBundle:City')->findBy(['stateId' => $destinationStateId, 'status' => 1]);
            $destinationCityList = $this->getInversedLocationList($destinationCities);
        }
        
        return [$countryList, $sourceStateList, $sourceCityList, $destinationStateList, $destinationCityList];
    }
    
    /**
     * Changes status of a delivery charge.
     *
     * @param  int $deliveryChargeId
     * @param  int $changeStatusTo
     *
     * @return bool
     */
    private function changeDeliveryChargeStatus($deliveryChargeId, $changeStatusTo)
    {
        $em = $this->getDoctrine()->getManager();
        
        $deliveryCharge = $em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findOneById($deliveryChargeId);
        
        if (! $deliveryCharge) {
            return false;
        } else {
            $deliveryModeTimeline = $deliveryCharge->getDeliveryModeTimelineId();
            
            if (! $deliveryModeTimeline) {
                return false;
            } else {
                $currentDateTime = new \DateTime();
                
                $deliveryCharge->setStatus($changeStatusTo);
                $deliveryModeTimeline->setStatus($changeStatusTo);
                $deliveryCharge->setLastModifiedOn($currentDateTime);
                $deliveryModeTimeline->setLastModifiedOn($currentDateTime);
                $deliveryCharge->setLastModifiedBy($this->getUser()->getId());
                $deliveryModeTimeline->setLastModifiedBy($this->getUser()->getId());
                
                $em->persist($deliveryCharge);
                $em->persist($deliveryModeTimeline);
                
                $em->flush();
                
                return true;
            }
        }
    }
}
