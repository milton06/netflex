<?php

namespace NetFlex\DeliveryChargeBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * DeliveryChargeRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DeliveryChargeRepository extends EntityRepository
{
	public function findDeliverability($sourceCountryId, $sourceStateId, $sourceCityId, $sourceZipCode, $destinationCountryId, $destinationStateId, $destinationCityId, $destinationZipCode)
	{
		$rsm = new ResultSetMapping();
		
		$rsm->addEntityResult('NetFlexDeliveryChargeBundle:DeliveryCharge', 'dc');
		$rsm->addFieldresult('dc', 'id', 'id');
		$rsm->addFieldresult('dc', 'shipment_base_weight_upper_limit', 'shipmentBaseWeightUpperLimit');
		$rsm->addFieldresult('dc', 'shipment_accountable_extra_weight', 'shipmentAccountableExtraWeight');
		$rsm->addFieldresult('dc', 'delivery_base_price', 'deliveryBasePrice');
		$rsm->addFieldresult('dc', 'delivery_extra_price_multiplier', 'deliveryExtraPriceMultiplier');
		$rsm->addFieldresult('dc', 'cod_delivery_base_price', 'codDeliveryBasePrice');
		$rsm->addFieldresult('dc', 'cod_delivery_percentage_on_base_price', 'codDeliveryPercentageOnBasePrice');
		$rsm->addFieldresult('dc', 'fuel_surcharge_fixed_price', 'fuelSurchargeFixedPrice');
		$rsm->addFieldresult('dc', 'fuel_surcharge_percentage_on_base_price', 'fuelSurchargePercentageOnBasePrice');
		$rsm->addFieldresult('dc', 'service_tax_percentage_on_base_price', 'serviceTaxPercentageOnBasePrice');
		$rsm->addFieldresult('dc', 'shipment_risk_chargable_above', 'shipmentRiskChargableAbove');
		$rsm->addFieldresult('dc', 'shipment_risk_percentage_on_base_price', 'shipmentRiskPercentageOnBasePrice');
		
		$rsm->addJoinedEntityResult('NetFlexDeliveryChargeBundle:DeliveryModeTimeline', 'dmt', 'dc', 'deliveryModeTimelineId');
		$rsm->addFieldresult('dmt', 'delivery_mode_timeline_id', 'id');
		
		$rsm->addJoinedEntityResult('NetFlexDeliveryChargeBundle:DeliveryMode', 'dm', 'dmt', 'deliveryModeId');
		$rsm->addFieldresult('dm', 'delivery_mode_id', 'id');
		$rsm->addFieldresult('dm', 'mode_name', 'modeName');
		
		$rsm->addJoinedEntityResult('NetFlexDeliveryChargeBundle:DeliveryTimeline', 'dt', 'dmt', 'deliveryTimelineId');
		$rsm->addFieldresult('dt', 'delivery_timeline_id', 'id');
		$rsm->addFieldresult('dt', 'timeline_name', 'timelineName');
		
		$rsm->addJoinedEntityResult('NetFlexDeliveryChargeBundle:WeightUnit', 'w', 'dc', 'shipmentWeightUnitId');
		$rsm->addFieldresult('w', 'shipment_weight_unit_id', 'id');
		$rsm->addFieldresult('w', 'symbol', 'symbol');
		
		$rsm->addJoinedEntityResult('NetFlexDeliveryChargeBundle:Currency', 'c', 'dc', 'deliveryPriceUnitId');
		$rsm->addFieldresult('c', 'delivery_price_unit_id', 'id');
		$rsm->addFieldresult('c', 'currency_symbol', 'currencySymbol');
		
		if ($sourceCountryId != $destinationCountryId) {
			/**
			 * We're delivering inter-country.
			 * TODO: implement logic later.
			 */
		} else {
			/**
			 * We're delivering intra-country.
			 */
			if ($sourceStateId != $destinationStateId) {
				/**
				 * Check if we're to deliver in NE
				 */
				if (in_array($destinationStateId, [3, 4, 23, 24, 25, 26, 34, 37])) {
					/**
					 * NE state.
					 */
					$sql = "select dc.id, dc.shipment_base_weight_upper_limit, dc.shipment_accountable_extra_weight, dc.delivery_base_price, dc.delivery_extra_price_multiplier, dc.cod_delivery_base_price, dc.cod_delivery_percentage_on_base_price, dc.fuel_surcharge_fixed_price, dc.fuel_surcharge_percentage_on_base_price, dc.service_tax_percentage_on_base_price, dc.shipment_risk_chargable_above, dc.shipment_risk_percentage_on_base_price, dmt.id delivery_mode_timeline_id, dm.id delivery_mode_id, dm.mode_name, dt.id delivery_timeline_id, dt.timeline_name, w.id shipment_weight_unit_id, w.symbol, c.id delivery_price_unit_id, c.currency_symbol from delivery_charges dc left join delivery_mode_timelines dmt on dc.delivery_mode_timeline_id = dmt.id left join delivery_modes dm on dmt.delivery_mode_id = dm.id left join delivery_timelines dt on dmt.delivery_timeline_id = dt.id left join weight_units w on dc.shipment_weight_unit_id = w.id left join currencies c on dc.delivery_price_unit_id = c.id where dc.source_country_id = dc.destination_country_id and dc.source_state_id = ? and dc.source_city_id = ? and dc.destination_state_id = 43 and dc.destination_city_id = 5743 and dc.status = 1";
					
					return $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters([1 => $sourceStateId, 2 => $sourceCityId])->getResult();
				}
				
				/**
				 * Check if we're to deliver in J&K
				 */
				if (15 == $destinationStateId) {
					/**
					 * J&K.
					 */
					$sql = "select dc.id, dc.shipment_base_weight_upper_limit, dc.shipment_accountable_extra_weight, dc.delivery_base_price, dc.delivery_extra_price_multiplier, dc.cod_delivery_base_price, dc.cod_delivery_percentage_on_base_price, dc.fuel_surcharge_fixed_price, dc.fuel_surcharge_percentage_on_base_price, dc.service_tax_percentage_on_base_price, dc.shipment_risk_chargable_above, dc.shipment_risk_percentage_on_base_price, dmt.id delivery_mode_timeline_id, dm.id delivery_mode_id, dm.mode_name, dt.id delivery_timeline_id, dt.timeline_name, w.id shipment_weight_unit_id, w.symbol, c.id delivery_price_unit_id, c.currency_symbol from delivery_charges dc left join delivery_mode_timelines dmt on dc.delivery_mode_timeline_id = dmt.id left join delivery_modes dm on dmt.delivery_mode_id = dm.id left join delivery_timelines dt on dmt.delivery_timeline_id = dt.id left join weight_units w on dc.shipment_weight_unit_id = w.id left join currencies c on dc.delivery_price_unit_id = c.id where dc.source_country_id = dc.destination_country_id and dc.source_state_id = ? and dc.source_city_id = ? and dc.destination_state_id = 44 and dc.destination_city_id = 5744 and dc.status = 1";
					
					return $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters([1 => $sourceStateId, 2 => $sourceCityId])->getResult();
				}
				
				/**
				 * Check if we're to deliver in Metros
				 */
				if (in_array($destinationCityId, [706, 707, 1558, 2707, 3659, 4460, 4800, 5583])) {
					/**
					 * Metro city.
					 */
					$sql = "select dc.id, dc.shipment_base_weight_upper_limit, dc.shipment_accountable_extra_weight, dc.delivery_base_price, dc.delivery_extra_price_multiplier, dc.cod_delivery_base_price, dc.cod_delivery_percentage_on_base_price, dc.fuel_surcharge_fixed_price, dc.fuel_surcharge_percentage_on_base_price, dc.service_tax_percentage_on_base_price, dc.shipment_risk_chargable_above, dc.shipment_risk_percentage_on_base_price, dmt.id delivery_mode_timeline_id, dm.id delivery_mode_id, dm.mode_name, dt.id delivery_timeline_id, dt.timeline_name, w.id shipment_weight_unit_id, w.symbol, c.id delivery_price_unit_id, c.currency_symbol from delivery_charges dc left join delivery_mode_timelines dmt on dc.delivery_mode_timeline_id = dmt.id left join delivery_modes dm on dmt.delivery_mode_id = dm.id left join delivery_timelines dt on dmt.delivery_timeline_id = dt.id left join weight_units w on dc.shipment_weight_unit_id = w.id left join currencies c on dc.delivery_price_unit_id = c.id where dc.source_country_id = dc.destination_country_id and dc.source_state_id = ? and dc.source_city_id = ? and dc.destination_state_id = 45 and dc.status = 1";
					
					return $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters([1 => $sourceStateId, 2 => $sourceCityId])->getResult();
				}
				
				/**
				 * Check if we're delivering inter-state to some specific destination state
				 */
				$sql = "select dc.id, dc.shipment_base_weight_upper_limit, dc.shipment_accountable_extra_weight, dc.delivery_base_price, dc.delivery_extra_price_multiplier, dc.cod_delivery_base_price, dc.cod_delivery_percentage_on_base_price, dc.fuel_surcharge_fixed_price, dc.fuel_surcharge_percentage_on_base_price, dc.service_tax_percentage_on_base_price, dc.shipment_risk_chargable_above, dc.shipment_risk_percentage_on_base_price, dmt.id delivery_mode_timeline_id, dm.id delivery_mode_id, dm.mode_name, dt.id delivery_timeline_id, dt.timeline_name, w.id shipment_weight_unit_id, w.symbol, c.id delivery_price_unit_id, c.currency_symbol from delivery_charges dc left join delivery_mode_timelines dmt on dc.delivery_mode_timeline_id = dmt.id left join delivery_modes dm on dmt.delivery_mode_id = dm.id left join delivery_timelines dt on dmt.delivery_timeline_id = dt.id left join weight_units w on dc.shipment_weight_unit_id = w.id left join currencies c on dc.delivery_price_unit_id = c.id where dc.source_country_id = dc.destination_country_id and dc.source_state_id = ? and dc.source_city_id = ? and dc.destination_state_id = ? and dc.status = 1";
				
				$result = $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters([1 => $sourceStateId, 2 => $sourceCityId, 3 => $destinationStateId])->getResult();
				
				if (empty($result)) {
					/**
					 * Check if we're delivering inter-state to some specific destination state-city combo
					 */
					$sql = "
						select dc.id, dc.shipment_base_weight_upper_limit, dc.shipment_accountable_extra_weight, dc.delivery_base_price, dc
.delivery_extra_price_multiplier, dc.cod_delivery_base_price, dc.cod_delivery_percentage_on_base_price, dc
.fuel_surcharge_fixed_price, dc.fuel_surcharge_percentage_on_base_price, dc.service_tax_percentage_on_base_price, dc.shipment_risk_chargable_above, dc.shipment_risk_percentage_on_base_price, dmt.id delivery_mode_timeline_id, dm.id delivery_mode_id, dm.mode_name, dt.id delivery_timeline_id, dt.timeline_name, w.id shipment_weight_unit_id, w.symbol, c.id delivery_price_unit_id, c.currency_symbol from delivery_charges dc left join delivery_mode_timelines dmt on dc.delivery_mode_timeline_id = dmt.id left join delivery_modes dm on dmt.delivery_mode_id = dm.id left join delivery_timelines dt on dmt.delivery_timeline_id = dt.id left join weight_units w on dc.shipment_weight_unit_id = w.id left join currencies c on dc.delivery_price_unit_id = c.id where dc.source_country_id = dc.destination_country_id and dc.source_state_id = ? and dc.source_city_id = ? and dc.destination_state_id = ? and dc.destination_city_id = ? and dc.status = 1
					";
					
					$result = $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters([1 => $sourceStateId, 2 => $sourceCityId, 3 => $destinationStateId, 4 => $destinationCityId])->getResult();
				}
				
				if (empty($result)) {
					/**
					 * Last check ROI.
					 */
					$sql = "select dc.id, dc.shipment_base_weight_upper_limit, dc.shipment_accountable_extra_weight, dc.delivery_base_price, dc
.delivery_extra_price_multiplier, dc.cod_delivery_base_price, dc.cod_delivery_percentage_on_base_price, dc.fuel_surcharge_fixed_price, dc.fuel_surcharge_percentage_on_base_price, dc.service_tax_percentage_on_base_price, dc.shipment_risk_chargable_above, dc.shipment_risk_percentage_on_base_price, dmt.id delivery_mode_timeline_id, dm.id delivery_mode_id, dm.mode_name, dt.id delivery_timeline_id, dt.timeline_name, w.id shipment_weight_unit_id, w.symbol, c.id delivery_price_unit_id, c.currency_symbol from delivery_charges dc left join delivery_mode_timelines dmt on dc.delivery_mode_timeline_id = dmt.id left join delivery_modes dm on dmt.delivery_mode_id = dm.id left join delivery_timelines dt on dmt.delivery_timeline_id = dt.id left join weight_units w on dc.shipment_weight_unit_id = w.id left join currencies c on dc.delivery_price_unit_id = c.id where dc.source_country_id = dc.destination_country_id and dc.source_state_id = ? and dc.source_city_id = ? and dc.destination_state_id = 42 and dc.destination_city_id = 5742 and dc.status = 1";
					
					return $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters([1 => $sourceStateId, 2 => $sourceCityId])->getResult();
				}
			} else {
				/**
				 * We're delivering intra-state.
				 */
				if ($sourceCityId != $destinationCityId) {
					/**
					 * We're delivering to another city.
					 */
					$sql = "select dc.id, dc.shipment_base_weight_upper_limit, dc.shipment_accountable_extra_weight, dc.delivery_base_price, dc.delivery_extra_price_multiplier, dc.cod_delivery_base_price, dc.cod_delivery_percentage_on_base_price, dc.fuel_surcharge_fixed_price, dc.fuel_surcharge_percentage_on_base_price, dc.service_tax_percentage_on_base_price, dc.shipment_risk_chargable_above, dc.shipment_risk_percentage_on_base_price, dmt.id delivery_mode_timeline_id, dm.id delivery_mode_id, dm.mode_name, dt.id delivery_timeline_id, dt.timeline_name, w.id shipment_weight_unit_id, w.symbol, c.id delivery_price_unit_id, c.currency_symbol from delivery_charges dc left join delivery_mode_timelines dmt on dc.delivery_mode_timeline_id = dmt.id left join delivery_modes dm on dmt.delivery_mode_id = dm.id left join delivery_timelines dt on dmt.delivery_timeline_id = dt.id left join weight_units w on dc.shipment_weight_unit_id = w.id left join currencies c on dc.delivery_price_unit_id = c.id where dc.source_country_id = dc.destination_country_id and dc.source_state_id = dc.destination_state_id and dc.source_city_id = ? and dc.destination_city_id = ? and dc.status = 1";
					
					return $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters([1 => $sourceCityId, 2 => $destinationCityId])->getResult();
				} else {
					/**
					 * We're delivering inter-city.
					 */
					$sql = "select dc.id, dc.shipment_base_weight_upper_limit, dc.shipment_accountable_extra_weight, dc.delivery_base_price, dc.delivery_extra_price_multiplier, dc.cod_delivery_base_price, dc.cod_delivery_percentage_on_base_price, dc.fuel_surcharge_fixed_price, dc.fuel_surcharge_percentage_on_base_price, dc.service_tax_percentage_on_base_price, dc.shipment_risk_chargable_above, dc.shipment_risk_percentage_on_base_price, dmt.id delivery_mode_timeline_id, dm.id delivery_mode_id, dm.mode_name, dt.id delivery_timeline_id, dt.timeline_name, w.id shipment_weight_unit_id, w.symbol, c.id delivery_price_unit_id, c.currency_symbol from delivery_charges dc left join delivery_mode_timelines dmt on dc.delivery_mode_timeline_id = dmt.id left join delivery_modes dm on dmt.delivery_mode_id = dm.id left join delivery_timelines dt on dmt.delivery_timeline_id = dt.id left join weight_units w on dc.shipment_weight_unit_id = w.id left join currencies c on dc.delivery_price_unit_id = c.id where dc.source_country_id = dc.destination_country_id and dc.source_state_id = dc.destination_state_id and dc.source_city_id = dc.destination_city_id and dc.source_zip_code = ? and dc.destination_zip_code = ? and dc.status = 1";
					
					return $this->getEntityManager()->createNativeQuery($sql, $rsm)->setParameters([1 => $sourceZipCode, 2 => $destinationZipCode])->getResult();
				}
			}
		}
	}
}
