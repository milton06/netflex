<?php

namespace NetFlex\DeliveryChargeBundle\Controller;

use Doctrine\Common\Cache\ArrayCache;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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
        /**
         * Get predefined delivery zones.
         */
        $deliveryZones = $this->getParameter('delivery_zones');
        
        /**
         * Create the delivery zone selection form.
         */
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
