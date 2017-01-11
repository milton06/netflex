<?php

namespace NetFlex\FrontBundle\Service\Guest;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use NetFlex\DeliveryChargeBundle\Entity\DeliveryCharge;

class BookAShipmentService
{
	private $em;
	private $orderRiskTypes;
	
	public function __construct(EntityManager $em, $orderRiskTypes)
	{
		$this->em = $em;
		$this->orderRiskTypes = $orderRiskTypes;
	}
	
	/**
	 * Gets location data.
	 *
	 * @param null|int  $defaultCountryId
	 * @param null|int  $defaultStateId
	 * @param null|int  $defaultCityId
	 * @param array     $excludedStateIds
	 *
	 * @return array
	 */
	public function getLocationData($defaultCountryId = null, $defaultStateId = null, $defaultCityId = null, $excludedStateIds = [])
	{
		/**
		 * Set default country, state and city IDs. Also, set state IDs to be excluded from find query.
		 */
		$defaultCountryId = ($defaultCountryId) ? $defaultCountryId : 1;
		$defaultStateId = ($defaultStateId) ? $defaultStateId : 41;
		$defaultCityId = ($defaultCityId) ? $defaultCityId : 5583;
		$excludedStateIds = ($excludedStateIds) ? $excludedStateIds : [42, 43, 44, 45, 46, 47];
		
		/**
		 * Get all active countries and set a country as default.
		 */
		$countries = $this->em->getRepository('NetFlexLocationBundle:Country')->findActiveCountries($defaultCountryId, $defaultStateId, $excludedStateIds);
		$defaultCountry = $this->getDefaultLocation($defaultCountryId, $countries);
		
		/**
		 * Get all active states of the default country and set a state as default.
		 */
		$states = ($defaultCountry) ? (! empty($defaultCountry->getStates()) ? $defaultCountry->getStates()->getValues() : []) : [];
		$defaultState = $this->getDefaultLocation($defaultStateId, $states);
		
		/**
		 * Get all active cities of the default state and set a city as default.
		 */
		$cities = ($defaultCountry) ? (! empty($defaultCountry->getCities()) ? $defaultCountry->getCities()->getValues() : []) : [];
		$defaultCity = $this->getDefaultLocation($defaultCityId, $cities);
		
		return [$countries, $defaultCountry, $states, array_shift($defaultState), $cities, array_shift($defaultCity)];
	}
	
	/**
	 * Gets a default location based on user-supplied option.
	 *
	 * @param  int   $defaultLocationId
	 * @param  array $locations
	 *
	 * @return array|null
	 */
	protected function getDefaultLocation($defaultLocationId, $locations)
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
	 * Gets order item types.
	 *
	 * @return array
	 */
	public function getOrderItemTypes()
	{
		/**
		 * Get all active primary order item types (those with parent ID set to NULL) and set the first one as default.
		 */
		$primaryItemtypes = $this->em->getRepository('NetFlexOrderBundle:ItemType')->findActiveItemTypes();
		$defaultPrimaryType = (! empty($primaryItemtypes)) ? $primaryItemtypes[0] : null;
		
		/**
		 * Get all active secondary order item types for the default primary order item type and set the first one as default.
		 */
		$secondaryItemtypes = ($defaultPrimaryType) ? $defaultPrimaryType->getChildren()->getValues() : [];
		$defaultSecondaryItemtype = (! empty($secondaryItemtypes)) ? $secondaryItemtypes[0] : null;
		
		return [$primaryItemtypes, $defaultPrimaryType, $secondaryItemtypes, $defaultSecondaryItemtype];
	}
	
	/**
	 * Gets order weight and currency units.
	 *
	 * @param null|int $defaultWeightUnitId
	 * @param null|int $defaultCurrencyUnitId
	 *
	 * @return array
	 */
	public function getOrderWeightAndCurrencyUnits($defaultWeightUnitId = null, $defaultCurrencyUnitId = null)
	{
		/**
		 * Set default weight and currency unit IDs.
		 */
		$defaultWeightUnitId = ($defaultWeightUnitId) ? $defaultWeightUnitId : 1;
		$defaultCurrencyUnitId = ($defaultCurrencyUnitId) ? $defaultCurrencyUnitId : 1;
		
		/**
		 * Get all the active weight units and set the default one.
		 */
		$weightUnits = $this->em->getRepository('NetFlexDeliveryChargeBundle:WeightUnit')->findBy(['status' => 1]);
		$defaultWeightUnit = $this->getDefaultUnit($defaultWeightUnitId, $weightUnits);
		
		/**
		 * Get all the active currency units and set the default one.
		 */
		$currencyUnits = $this->em->getRepository('NetFlexDeliveryChargeBundle:Currency')->findBy(['status' => 1]);
		$defaultCurrencyUnit = $this->getDefaultUnit($defaultCurrencyUnitId, $currencyUnits);
		
		return [$weightUnits, array_shift($defaultWeightUnit), $currencyUnits, array_shift($defaultCurrencyUnit)];
	}
	
	/**
	 * Gets a default weight|currency unit based on user supplied option.
	 *
	 * @param int   $defaultUnitId
	 * @param array $units
	 *
	 * @return array
	 */
	protected function getDefaultUnit($defaultUnitId, $units)
	{
		return array_filter($units, function($thisUnit) use($defaultUnitId) {
			if ($defaultUnitId == $thisUnit->getId()) {
				return $thisUnit;
			}
		});
	}
	
	/**
	 * Gets lists of state and city for a selected country.
	 *
	 * @param int      $countryId
	 * @param array $excludedStateIds
	 *
	 * @return array
	 */
	public function getStateAndCityListByCountryId($countryId, $excludedStateIds = [])
	{
		/**
		 * Initialize state and city list as empty arrays.
		 */
		$stateList = $cityList = [];
		
		/**
		 * No country was selected, so state and city lists will be empty.
		 */
		if (empty($countryId)) {
			return [$stateList, $cityList];
		}
		
		/**
		 * Fetch the selected country, its states and cities for each states.
		 */
		$country = $this->em->getRepository('NetFlexLocationBundle:Country')->findStatesAndCitiesByCountryId($countryId, (($excludedStateIds) ? $excludedStateIds : [42, 43, 44, 45, 46, 47]));
		
		/**
		 * No such country active in the DB, so state and city lists will be empty.
		 */
		if (empty($country)) {
			return [$stateList, $cityList];
		}
		
		/**
		 * States for the selected country.
		 */
		$states = $country->getStates()->getValues();
		
		$stateList = $this->getLocationList($states);
		
		/**
		 * Populate the city list with those for the first state in the state list.
		 */
		$cityList = ($states) ? $this->getLocationList($states[0]->getCities()->getValues()) : [];
		
		return [$stateList, $cityList];
	}
	
	/**
	 * Gets list of cities for a selected state.
	 *
	 * @param int $stateId
	 *
	 * @return array
	 */
	public function getCityListByStateId($stateId)
	{
		/**
		 * Initialize an empty city list.
		 */
		$cityList = [];
		
		/**
		 * No state was selected, so city list will be empty.
		 */
		if (empty($stateId)) {
			return $cityList;
		}
		
		/**
		 * Get the state and all the cities under it.
		 */
		$state = $this->em->getRepository('NetFlexLocationBundle:State')->findCitiesByStateId($stateId);
		
		/**
		 * No such active state exists in the DB, so city list will be empty.
		 */
		if (empty($state)) {
			return $cityList;
		}
		
		/**
		 * Get the cities for the selected state.
		 */
		$cities = $state->getCities()->getValues();
		
		$cityList = $this->getLocationList($cities);
		
		return $cityList;
	}
	
	/**
	 * Constructs a list of supplied locations.
	 *
	 * @param  array $locations
	 *
	 * @return array
	 */
	protected function getLocationList($locations)
	{
		$locationList = [];
		
		if ($locations) {
			foreach ($locations as $thisLocation) {
				$locationList[$thisLocation->getId()] = $thisLocation->getName();
			}
		}
		
		return $locationList;
	}
	
	/**
	 * Gets secondary oredr item types for a selected primary one.
	 *
	 * @param int $primaryItemTypeId
	 *
	 * @return array
	 */
	public function getSecondaryItemTypeList($primaryItemTypeId)
	{
		$secondaryItemTypeList = [];
		
		if ($primaryItemTypeId) {
			$secondaryItemTypes = $this->em->getRepository('NetFlexOrderBundle:ItemType')->findBy(['parentId' => $primaryItemTypeId, 'status' => 1]);
			
			foreach ($secondaryItemTypes as $thisSecondaryItemType) {
				$secondaryItemTypeList[$thisSecondaryItemType->getId()] = $thisSecondaryItemType->getItemTypeName();
			}
		}
		
		return $secondaryItemTypeList;
	}
	
	/**
	 * Gets deliverability check and charge calculation parameters.
	 *
	 * @param  array $requestParameters
	 *
	 * @return array
	 */
	public function getDeliverabilityCheckAndChargeCalculationParameters($requestParameters)
	{
		/**
		 * Selected delivery mode.
		 */
		$deliveryModeId = (isset($requestParameters['deliveryModeId'])) ? $requestParameters['deliveryModeId'] : null;
		
		/**
		 * Selected source params.
		 */
		$sourceCountryId = (isset($requestParameters['sourceCountryId'])) ? $requestParameters['sourceCountryId'] : null;
		$sourceStateId = (isset($requestParameters['sourceStateId'])) ? $requestParameters['sourceStateId'] : null;
		$sourceCityId = (isset($requestParameters['sourceCityId'])) ? $requestParameters['sourceCityId'] : null;
		$sourceZipCode = (isset($requestParameters['sourceZipCode'])) ? $requestParameters['sourceZipCode'] : null;
		
		/**
		 * Selected destination params.
		 */
		$destinationCountryId = (isset($requestParameters['destinationCountryId'])) ? $requestParameters['destinationCountryId'] : null;
		$destinationStateId = (isset($requestParameters['destinationStateId'])) ? $requestParameters['destinationStateId'] : null;
		$destinationCityId = (isset($requestParameters['destinationCityId'])) ? $requestParameters['destinationCityId'] : null;
		$destinationZipCode = (isset($requestParameters['destinationZipCode'])) ? $requestParameters['destinationZipCode'] : null;
		
		/**
		 * Selected item params.
		 */
		$itemPrimaryType = (isset($requestParameters['itemPrimaryType'])) ? $requestParameters['itemPrimaryType'] : null;
		$itemSecondaryType = (isset($requestParameters['itemSecondaryType'])) ? $requestParameters['itemSecondaryType'] : null;
		$itemBaseWeight = (isset($requestParameters['itemBaseWeight'])) ? $requestParameters['itemBaseWeight'] : null;
		$itemWeightUnit = (isset($requestParameters['itemWeightUnit'])) ? $requestParameters['itemWeightUnit'] : null;
		$itemInvoiceValue = (isset($requestParameters['itemInvoiceValue'])) ? $requestParameters['itemInvoiceValue'] : null;
		$itemPriceUnit = (isset($requestParameters['itemPriceUnit'])) ? $requestParameters['itemPriceUnit'] : null;
		$riskType = (isset($requestParameters['riskType'])) ? $requestParameters['riskType'] : null;
		
		return [$deliveryModeId, $sourceCountryId, $sourceStateId, $sourceCityId, $sourceZipCode, $destinationCountryId, $destinationStateId, $destinationCityId, $destinationZipCode, $itemPrimaryType, $itemSecondaryType, $itemBaseWeight, $itemWeightUnit, $itemInvoiceValue, $itemPriceUnit, $riskType];
	}
	
	/**
	 * Checks deliverability.
	 *
	 * @param int $deliveryModeId
	 * @param int $sourceCountryId
	 * @param int $sourceStateId
	 * @param int $sourceCityId
	 * @param int $sourceZipCode
	 * @param int $destinationCountryId
	 * @param int $destinationStateId
	 * @param int $destinationCityId
	 * @param int $destinationZipCode
	 *
	 * @return array
	 */
	public function checkDeliverability($deliveryModeId, $sourceCountryId, $sourceStateId, $sourceCityId, $sourceZipCode, $destinationCountryId, $destinationStateId, $destinationCityId, $destinationZipCode)
	{
		/**
		 * Query for matching delivery charge(s).
		 */
		$deliveryCharge = $this->em->getRepository('NetFlexDeliveryChargeBundle:DeliveryCharge')->findDeliverability($sourceCountryId, $sourceStateId, $sourceCityId, $sourceZipCode, $destinationCountryId, $destinationStateId, $destinationCityId, $destinationZipCode);
		
		if (! $deliveryCharge) {
			/**
			 * No delivery charge available for the source-destination location combination. i.e. we don't deliver there.
			 */
			return ['status' => false, 'message' => ['no_deliverability_error' => 'Shipment is not possible between these locations presently']];
		}
		
		if ((1 === count($deliveryCharge)) && ($deliveryModeId != $deliveryCharge[0]->getDeliveryModeTimelineId()->getDeliveryModeId()->getId())) {
			/**
			 * The requested delivery mode is not available for the source-destination location combination. Though another mode is available.
			 */
			$deliveryModeRepo = $this->em->getRepository('NetFlexDeliveryChargeBundle:DeliveryMode');
			
			return ['status' => false, 'message' => ['delivery_mode_error' => 'We don\'t deliver via ' . $deliveryModeRepo->findDeliveryModeName($deliveryModeId)->getModeName() . ' at this location. But you can select ' . $deliveryCharge[0]->getDeliveryModeTimelineId()->getDeliveryModeId()->getModeName() . '.']];
		}
		
		if (1 === count($deliveryCharge)) {
			/**
			 * There is only one delivery charge and that's the intended one.
			 */
			return ['status' => true, 'actualDeliveryCharge' => $deliveryCharge[0]];
		} else {
			foreach ($deliveryCharge as $thisDeliveryCharge) {
				/**
				 * Since there may be more than one delivery charges, we need the extract the intended one.
				 */
				if ($deliveryModeId == $thisDeliveryCharge->getDeliveryModeTimelineId()->getDeliveryModeId()->getId()) {
					return ['status' => true, 'actualDeliveryCharge' => $thisDeliveryCharge];
				}
			}
		}
	}
	
	/**
	 * Gets calculated delivery charge parameters.
	 *
	 * @param DeliveryCharge $actualDeliveryCharge
	 * @param float          $itemBaseWeight
	 * @param int            $itemWeightUnit
	 * @param string         $riskType
	 *
	 * @return array
	 */
	public function getDeliveryChargeParameters(DeliveryCharge $actualDeliveryCharge, $itemBaseWeight, $itemWeightUnit, $riskType)
	{
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
		$fuelSurchargeFixedPrice = $actualDeliveryCharge->getFuelSurchargeFixedPrice();
		$fuelSurchargePercentageOnBasePrice = $actualDeliveryCharge->getFuelSurchargePercentageOnBasePrice();
		$serviceTaxPercentageOnBasePrice = $actualDeliveryCharge->getServiceTaxPercentageOnBasePrice();
		$shipmentRiskPercentageOnBasePrice = $actualDeliveryCharge->getShipmentRiskPercentageOnBasePrice();
		
		/**
		 * Base and accountable extra weight charges.
		 */
		$orderBaseCharge = $deliveryBasePrice;
		$orderExtraWeightLeviedCharge = (ceil($itemAccountableExtraWeight / $shipmentAccountableExtraWeight) * $deliveryExtraPriceMultiplier);
		$orderTotalCharge = ($orderBaseCharge + $orderExtraWeightLeviedCharge);
		
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
		
		return [
			'deliveryChargeId' => $actualDeliveryCharge->getId(),
			'itemCalculatedBaseWeight' => $itemBaseWeight,
			'itemCalculatedWeightUnit' => $itemWeightUnit,
			'itemAccountableExtraWeight' => $itemAccountableExtraWeight,
			'orderBaseCharge' => $orderBaseCharge,
			'orderExtraWeightLeviedCharge' => $orderExtraWeightLeviedCharge,
			'orderFuelSurchargeAddedCharge' => $orderFuelSurchargeAddedCharge,
			'orderServiceTaxAddedCharge' => $orderServiceTaxAddedCharge,
			'orderCarrierRiskAddedCharge' => $orderCarrierRiskAddedCharge,
		];
	}
	
	public function getExpieryYears()
	{
		$expieryYers = [];
		
		$currentDateTimeObject = new \DateTime();
		$currentYear = $currentDateTimeObject->format('Y');
		
		$maxDateTimeObject = $currentDateTimeObject->add(new \DateInterval('P50Y'));
		$latestYear = $maxDateTimeObject->format('Y');
		
		for ($i = $currentYear; $i <= $latestYear; $i++) {
			$expieryYers[$i] = (string) $i;
		}
		
		return $expieryYers;
	}
}
