<?php

namespace NetFlex\DeliveryChargeBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryMode;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryTimeline;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryModeTimeline;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryCharge;
use NetFlex\DeliveryChargeBundle\Form\DeliveryCharge\DeliveryZoneType;
use NetFlex\DeliveryChargeBundle\Form\DeliveryCharge\DeliveryChargeNewType;

/**
 * @Route("/dashboard/delivery-charge")
 */
class DeliveryChargeController extends Controller
{
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
            
            $deliveryChargeForm = $this->getDeliveryChargeForm((isset($deliveryZoneData['deliveryZone']) &&
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
            $deliveryChargeForm = $this->getDeliveryChargeForm($deliveryZoneId);
            
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
                    
                    if (1 == $deliveryZoneId) {
                        if ($formData['sourceZipCodeRange'] == $formData['destinationZipCodeRange']) {
                            return $this->json(['status' => 'failure', 'reason' => 'validationError', 'validationErrorList' =>
                                ['destinationZipCodeRange' => 'Cannot match source zip code range']]);
                        }
                    } elseif (2 == $deliveryZoneId) {
                        if ($formData['sourceCityId'] == $formData['destinationCityId']) {
                            return $this->json(['status' => 'failure', 'reason' => 'validationError', 'validationErrorList' =>
                                ['destinationCityId' => 'Cannot match source city']]);
                        }
                    } else {
                        //
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
                    $deliveryCharge->setSourceZipCode((isset($formData['sourceZipCode']) && ! empty
                        ($formData['sourceZipCode'])) ? $formData['sourceZipCode'] : null);
                    $deliveryCharge->setDestinationCountryId((isset($formData['destinationCountryId']) && ! empty
                        ($formData['destinationCountryId'])) ? $formData['destinationCountryId'] : null);
                    $deliveryCharge->setDestinationStateId((isset($formData['destinationStateId']) && ! empty
                        ($formData['destinationStateId'])) ? $formData['destinationStateId'] : null);
                    $deliveryCharge->setDestinationCityId((isset($formData['destinationCityId']) && ! empty
                        ($formData['destinationCityId'])) ? $formData['destinationCityId'] : null);
                    $deliveryCharge->setDestinationZipCode((isset($formData['destinationZipCode']) && ! empty
                        ($formData['destinationZipCode'])) ? $formData['destinationZipCode'] : null);
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
                    
                    $validationErrors = $this->get('validator')->validate($deliveryCharge);
                    
                    if (0 < count($validationErrors)) {
                        return $this->json(['status' => 'failure', 'reason' => 'redundancyError', 'redundancyError' => $validationErrors[0]->getMessage()]);
                    } else {
                        $em = $this->getDoctrine()->getManager();
                        
                        $em->persist($deliveryModeTimeline);
                        $em->persist($deliveryCharge);
    
                        $em->flush();
    
                        return $this->json(['status' => 'success', 'id' => $deliveryCharge->getId()]);
                    }
                }
            }
        }
    }
    
    /**
     * Creates and returns delivery charge form.
     *
     * @param  int                        $deliveryZone
     *
     * @return null|DeliveryChargeNewType
     */
	public function getDeliveryChargeForm($deliveryZone)
    {
        if (! $deliveryZone) {
            return null;
        } else {
            $em = $this->getDoctrine()->getManager();
    
            /**
             * Get source location parameters.
             */
            $sourceCountries = $destinationCountries = $em->getRepository('NetFlexLocationBundle:Country')->findActiveCountries(1, 41, [42,
                43, 44, 45, 46, 47]);
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
                'defaultSourceState' => array_shift($defaultSourceState),
                'sourceCities' => $sourceCities,
                'defaultSourceCity' => array_shift($defaultSourceCity),
                'destinationCountries' => $destinationCountries,
                'defaultDestinationCountry' => $defaultDestinationCountry,
                'destinationStates' => $destinationStates,
                'defaultDestinationState' => array_shift($defaultDestinationState),
                'destinationCities' => $destinationCities,
                'defaultDestinationCity' => array_shift($defaultDestinationCity),
                'deliveryModes' => $deliveryModes,
                'deliveryTimelines' => $deliveryTimelines,
                'weightUnits' => $weightUnits,
                'defaultWeightUnit' => array_shift($defaultWeightUnit),
                'currencyUnits' => $currencyUnits,
                'defaultCurrencyUnit' => array_shift($defaultCurrencyUnit),
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
                    $destinationStates = ($defaultDestinationCountry) ? $em->getRepository('NetFlexLocationBundle:State')->findBy(['countryId' =>
                        $defaultDestinationCountry->getId(), 'id' => [42, 43, 44, 45, 46, 47], 'status' => 1]) : [];
                    $defaultDestinationState = ($destinationStates) ? $this->getDefaultLocation(45,
                        $destinationStates) : null;
                    $defaultDestinationState = ($defaultDestinationState) ? array_shift($defaultDestinationState) :
                        null;
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
}
