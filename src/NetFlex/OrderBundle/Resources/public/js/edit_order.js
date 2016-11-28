jQuery(document).ready(function() {
	var validateCheckDeliverabilityForm = function() {
		var errorCount = 0;
		jQuery("#booking-options .help-block").remove();
		jQuery("#booking-options .form-group").removeClass("has-error");
		
		jQuery("#booking-options select").each(function() {
			if (! jQuery(this).val()) {
				++errorCount;
				
				jQuery(this).parent().append("<span class='help-block'>Required field</span>");
				jQuery(this).closest(".form-group").addClass("has-error");
			}
		});
		
		if (jQuery("#cd-source-city").val() === jQuery("#cd-destination-city").val()) {
			if (! jQuery("#cd-source-zip-code").val()) {
				++errorCount;
				
				jQuery("#cd-source-zip-code").parent().append("<span class='help-block'>Required field</span>");
				jQuery("#cd-source-zip-code").closest(".form-group").addClass("has-error");
			}
			
			if (! jQuery("#cd-destination-zip-code").val()) {
				++errorCount;
				
				jQuery("#cd-destination-zip-code").parent().append("<span class='help-block'>Required field</span>");
				jQuery("#cd-destination-zip-code").closest(".form-group").addClass("has-error");
			}
		}
		
		if (! jQuery("#item-base-weight").val()) {
			++errorCount;
			
			jQuery("#item-base-weight").parent().append("<span class='help-block'>Required field</span>");
			jQuery("#item-base-weight").closest(".form-group").addClass("has-error");
		}
		
		if (! jQuery("#item-invoice-value").val()) {
			++errorCount;
			
			jQuery("#item-invoice-value").parent().append("<span class='help-block'>Required field</span>");
			jQuery("#item-invoice-value").closest(".form-group").addClass("has-error");
		}
		
		if (errorCount) {
			jQuery(".global-form-error").show();
			
			return false;
		}
		
		return true;
	};
	
	var validateOrderForm = function() {
		var errorCount = 0;
		jQuery("#shipment-addresses .help-block").remove();
		jQuery("#shipment-addresses .form-group").removeClass("has-error");
		
		jQuery("#shipment-addresses input, #shipment-addresses select").each(function() {
			var thisElementId = jQuery(this).attr("id");
			var exclude = ["pickup-mid-name", "pickup-address-line-2", "pickup-land-mark", "pickup-email", "billing-mid-name", "billing-address-line-2", "billing-land-mark", "shipping-mid-name", "shipping-address-line-2", "shipping-land-mark", "shipping-email", "order_for_client_from_dashboard_deliveryChargeId"];
			
			if ((-1 === exclude.indexOf(thisElementId)) && (! jQuery(this).val())) {
				++errorCount;
				
				jQuery(this).parent().append("<span class='help-block'>Required field</span>");
				jQuery(this).closest(".form-group").addClass("has-error");
			}
		});
		
		var emailRegex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		
		if ((jQuery("#shipment-addresses #pickup-email").val()) && (! emailRegex.test(jQuery("#shipment-addresses #pickup-email").val()))) {
			++errorCount;
			
			jQuery(this).parent().append("<span class='help-block'>Please enter a valid email</span>");
			jQuery(this).closest(".form-group").addClass("has-error");
		}
		
		if ((jQuery("#shipment-addresses #shipping-email").val()) && (! emailRegex.test(jQuery("#shipment-addresses #shipping-email").val()))) {
			++errorCount;
			
			jQuery(this).parent().append("<span class='help-block'>Please enter a valid email</span>");
			jQuery(this).closest(".form-group").addClass("has-error");
		}
		
		if (errorCount) {
			jQuery(".global-form-error").show();
			
			return false;
		}
		
		return true;
	};
	
	jQuery('.nav li').not('.active').addClass('disabled');
	jQuery('.nav li').not('.active').find('a').removeAttr("data-toggle");
	
	jQuery('input[type="checkbox"].flat-grey, input[type="radio"].flat-grey').iCheck({
		checkboxClass: 'icheckbox_flat-grey',
		radioClass: 'iradio_flat-grey',
		increaseArea: '10%' // optional
	});
	
	jQuery(".cd-country-selectors").on("change", function(e) {
		var element = jQuery(this);
		var countryId = jQuery(this).val();
		
		jQuery.ajax({
			url: stateListFetchUrl,
			type: "post",
			dataType: "json",
			data: {
				'countryId': countryId
			},
			success: function(response) {
				var stateList = response.stateList;
				var cityList = response.cityList;
				var stateOptions = "<option value=''>-Select A State-</option>";
				var cityOptions = "<option value=''>-Select A City-</option>";
				var i = 0;
				
				jQuery.each(stateList, function(key, value) {
					stateOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
				});
				
				i = 0;
				jQuery.each(cityList, function(key, value) {
					cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
				});
				
				jQuery(element).parent().parent().parent().next(".col-md-3").find(".cd-state-selectors").empty().html(stateOptions);
				
				jQuery(element).parent().parent().parent().next(".col-md-3").next(".col-md-3").find(".cd-city-selectors").empty().html(cityOptions);
			}
		});
	});
	
	jQuery(".cd-state-selectors").on("change", function(e) {
		var element = jQuery(this);
		var stateId = jQuery(this).val();
		
		jQuery.ajax({
			url: cityListFetchUrl,
			type: "post",
			dataType: "json",
			data: {
				'stateId': stateId
			},
			success: function(response) {
				var cityList = response.cityList;
				var cityOptions = "<option value=''>-Select A City-</option>";
				var i = 0;
				
				jQuery.each(cityList, function(key, value) {
					cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
				});
				
				jQuery(element).parent().parent().parent().next(".col-md-3").find(".cd-city-selectors").empty().html(cityOptions);
			}
		});
	});
	
	jQuery("#item-invoice-value").on("input", function() {
		var itemInvoiceValue = jQuery(this).val();
		var orderRiskApplicableAbove = orderRiskApplicableAbove;
		
		if (parseFloat(5000) <= parseFloat(itemInvoiceValue)) {
			jQuery("#risk-type-container").show();
		} else {
			jQuery(".risk-types").iCheck("uncheck");
			jQuery("#risk-type-container").hide();
		}
	});
	
	jQuery("#item-primary-type").on("change", function() {
		jQuery.ajax({
			url: getSecondaryItemTypeUrl,
			type: "post",
			dataType: "json",
			data: {
				"itemPrimaryTypeId": jQuery("#item-primary-type").val()
			},
			success: function(response) {
				var itemRelatedSecondaryTypeList = response.itemRelatedSecondaryTypeList;
				var itemRelatedSecondaryTypeListMarkUp = "<option value=''>-Select A Secondary Type-</option>";
				var i = 1;
				
				jQuery.each(itemRelatedSecondaryTypeList, function(key, value) {
					itemRelatedSecondaryTypeListMarkUp += "<option value='" + key +"'" + ((1 === i++) ? 'selected="selected"' : '') + ">" + value + "</option>";
				});
				
				jQuery("#item-secondary-type").empty().html(itemRelatedSecondaryTypeListMarkUp);
			}
		});
	});
	
	jQuery(".country-selectors").on("change", function(e) {
		var element = jQuery(this);
		var countryId = jQuery(this).val();
		
		jQuery.ajax({
			url: stateListFetchUrl,
			type: "post",
			dataType: "json",
			data: {
				'countryId': countryId
			},
			success: function(response) {
				var stateList = response.stateList;
				var cityList = response.cityList;
				var stateOptions = "<option value=''>-Select A State-</option>";
				var cityOptions = "<option value=''>-Select A City-</option>";
				var i = 0;
				
				jQuery.each(stateList, function(key, value) {
					stateOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
				});
				
				i = 0;
				jQuery.each(cityList, function(key, value) {
					cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
				});
				
				jQuery(element).parent().parent().next(".form-group").find(".state-selectors").empty().html(stateOptions);
				
				jQuery(element).parent().parent().next(".form-group").next(".form-group").find(".city-selectors").empty().html(cityOptions);
			}
		});
	});
	
	jQuery(".state-selectors").on("change", function(e) {
		var element = jQuery(this);
		var stateId = jQuery(this).val();
		
		jQuery.ajax({
			url: cityListFetchUrl,
			type: "post",
			dataType: "json",
			data: {
				'stateId': stateId
			},
			success: function(response) {
				var cityList = response.cityList;
				var cityOptions = "<option value=''>-Select A City-</option>";
				var i = 0;
				
				jQuery.each(cityList, function(key, value) {
					cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
				});
				
				jQuery(element).parent().parent().next(".form-group").find(".city-selectors").empty().html(cityOptions);
			}
		});
	});
	
	jQuery("#check-deliverability-button").on("click", function(e) {
		e.preventDefault();
		
		if (validateCheckDeliverabilityForm()) {
			var deliveryModeId = jQuery(".delivery-modes:checked").val();
			
			var sourceCountryId = jQuery("#cd-source-country").val();
			var sourceStateId = jQuery("#cd-source-state").val();
			var sourceCityId = jQuery("#cd-source-city").val();
			var sourceZipCode = jQuery("#cd-source-zip-code").val();
			
			var destinationCountryId = jQuery("#cd-destination-country").val();
			var destinationStateId = jQuery("#cd-destination-state").val();
			var destinationCityId = jQuery("#cd-destination-city").val();
			var destinationZipCode = jQuery("#cd-destination-zip-code").val();
			
			var itemPrimaryType = jQuery("#item-primary-type").val();
			var itemSecondaryType = jQuery("#item-secondary-type").val();
			
			var itemBaseWeight = jQuery("#item-base-weight").val();
			var itemWeightUnit = jQuery("#item-weight-unit").val();
			
			var itemInvoiceValue = jQuery("#item-invoice-value").val();
			var itemPriceUnit = jQuery("#item-price-unit").val();
			
			var riskType = (jQuery(".risk-types:checked").val()) ? jQuery(".risk-types:checked").val() : 'own';
			
			var codChoice = jQuery(".cod-choice:checked").val();
			
			jQuery.ajax({
				url: checkDeliverabilityUrl,
				type: "post",
				dataType: "json",
				data :{
					"deliveryModeId": deliveryModeId,
					"sourceCountryId": sourceCountryId,
					"sourceStateId": sourceStateId,
					"sourceCityId": sourceCityId,
					"sourceZipCode": sourceZipCode,
					"destinationCountryId": destinationCountryId,
					"destinationStateId": destinationStateId,
					"destinationCityId": destinationCityId,
					"destinationZipCode": destinationZipCode,
					"itemPrimaryType": itemPrimaryType,
					"itemSecondaryType": itemSecondaryType,
					"itemBaseWeight": itemBaseWeight,
					"itemWeightUnit": itemWeightUnit,
					"itemInvoiceValue": itemInvoiceValue,
					"itemPriceUnit": itemPriceUnit,
					"riskType": riskType,
					"codChoice": codChoice
				},
				beforeSend: function() {
					jQuery(".delivery-mode-error").find("span").remove();
					jQuery(".errorHandler").hide();
					
					jQuery("#check-deliverability-button").prop("disabled", true);
				},
				success: function(response) {
					if (response.no_deliverability_error) {
						jQuery(".no-deliverability-error").show();
					} else if (response.delivery_mode_error) {
						jQuery(".delivery-mode-error").append("<span>" + response.delivery_mode_error + "</span>");
						jQuery(".delivery-mode-error").show();
					} else {
						var deliveryParams = response.delivery_params;
						
						jQuery("#delivery-charge-id").val(deliveryParams.deliveryChargeId);
						jQuery("#item-calculated-base-weight").val(deliveryParams.itemCalculatedBaseWeight);
						jQuery("#item-calculated-weight-unit").val(deliveryParams.itemCalculatedWeightUnit);
						jQuery("#item-accountable-extra-weight").val(deliveryParams.itemAccountableExtraWeight);
						jQuery("#order-base-charge").val(deliveryParams.orderBaseCharge);
						jQuery("#order-extra-weight-levied-charge").val(deliveryParams.orderExtraWeightLeviedCharge);
						jQuery("#order-cod-payment-added-charge").val(deliveryParams.orderCodPaymentAddedCharge);
						jQuery("#order-fuel-surcharge-added-charge").val(deliveryParams.orderFuelSurchargeAddedCharge);
						jQuery("#order-service-tax-added-charge").val(deliveryParams.orderServiceTaxAddedCharge);
						jQuery("#order-carrier-risk-added-charge").val(deliveryParams.orderCarrierRiskAddedCharge);
						
						jQuery("#tab-shipment-addresses").removeClass('disabled');
						jQuery("#tab-shipment-addresses > a").attr("data-toggle","tab");
						jQuery("#tab-shipment-addresses > a").tab("show");
					}
				},
				error: function() {
					jQuery(".server-fault").show();
				},
				complete: function () {
					jQuery("#check-deliverability-button").prop("disabled", false);
				}
			});
		}
	});
	
	jQuery("#book-a-shipment-button").on("click", function(e) {
		e.preventDefault();
		
		if (validateCheckDeliverabilityForm() && validateOrderForm()) {
			jQuery.ajax({
				url: updateOrderUrl,
				type: "post",
				dataType: "json",
				data: jQuery("#book-a-shipment-form").serialize(),
				beforeSend: function() {
					jQuery(".errorHandler").hide();
					
					jQuery("#book-a-shipment-button").prop("disabled", true);
				},
				success: function(response) {
					var orderId = response.orderId;
					
					jQuery("#tab-booking-confirmation").removeClass('disabled');
					jQuery("#tab-booking-confirmation > a").attr("data-toggle","tab");
					
					jQuery("#tab-shipment-addresses").addClass('disabled');
					jQuery("#tab-shipment-addresses > a").removeAttr("data-toggle");
					
					jQuery("#tab-booking-options").addClass('disabled');
					jQuery("#tab-booking-options > a").removeAttr("data-toggle");
					
					var editLink = jQuery("#booking-confirmation").find("#edit_link").attr("href");
					editLink = editLink.replace('%25id%25', orderId);
					jQuery("#booking-confirmation").find("#edit_link").attr("href", editLink);
					
					jQuery("#tab-booking-confirmation").find("#edit_link").attr("href", editLink);
					
					jQuery("#tab-booking-confirmation > a").tab("show");
				},
				error: function() {
					jQuery(".server-fault").show();
				},
				complete: function() {
					jQuery("#book-a-shipment-button").prop("disabled", false);
				}
			});
		}
	});
});