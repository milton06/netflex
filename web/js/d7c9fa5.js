/* idTabs ~ Sean Catchpole - Version 2.2 - MIT/GPL */
(function(){var dep={"jQuery":"http://code.jquery.com/jquery-latest.min.js"};var init=function(){(function($){$.fn.idTabs=function(){var s={};for(var i=0;i<arguments.length;++i){var a=arguments[i];switch(a.constructor){case Object:$.extend(s,a);break;case Boolean:s.change=a;break;case Number:s.start=a;break;case Function:s.click=a;break;case String:if(a.charAt(0)=='.')s.selected=a;else if(a.charAt(0)=='!')s.event=a;else s.start=a;break;}}
if(typeof s['return']=="function")
s.change=s['return'];return this.each(function(){$.idTabs(this,s);});}
$.idTabs=function(tabs,options){var meta=($.metadata)?$(tabs).metadata():{};var s=$.extend({},$.idTabs.settings,meta,options);if(s.selected.charAt(0)=='.')s.selected=s.selected.substr(1);if(s.event.charAt(0)=='!')s.event=s.event.substr(1);if(s.start==null)s.start=-1;var showId=function(){if($(this).is('.'+s.selected))
return s.change;var id="#"+this.href.split('#')[1];var aList=[];var idList=[];$("a",tabs).each(function(){if(this.href.match(/#/)){aList.push(this);idList.push("#"+this.href.split('#')[1]);}});if(s.click&&!s.click.apply(this,[id,idList,tabs,s]))return s.change;for(i in aList)$(aList[i]).removeClass(s.selected);for(i in idList)$(idList[i]).hide();$(this).addClass(s.selected);$(id).show();return s.change;}
var list=$("a[href*='#']",tabs).unbind(s.event,showId).bind(s.event,showId);list.each(function(){$("#"+this.href.split('#')[1]).hide();});var test=false;if((test=list.filter('.'+s.selected)).length);else if(typeof s.start=="number"&&(test=list.eq(s.start)).length);else if(typeof s.start=="string"&&(test=list.filter("[href*='#"+s.start+"']")).length);if(test){test.removeClass(s.selected);test.trigger(s.event);}
return s;}
$.idTabs.settings={start:0,change:false,click:null,selected:".selected",event:"!click"};$.idTabs.version="2.2";$(function(){$(".idTabs").idTabs();});})(jQuery);}
var check=function(o,s){s=s.split('.');while(o&&s.length)o=o[s.shift()];return o;}
var head=document.getElementsByTagName("head")[0];var add=function(url){var s=document.createElement("script");s.type="text/javascript";s.src=url;head.appendChild(s);}
var s=document.getElementsByTagName('script');var src=s[s.length-1].src;var ok=true;for(d in dep){if(check(this,d))continue;ok=false;add(dep[d]);}if(ok)return init();add(src);})();

jQuery(document).ready(function() {
	var validateCheckDeliverabilityForm = function() {
		var errorCount = 0;
		jQuery(".errorHandler").hide();
		
		jQuery("#booking-options select").each(function() {
			if (('choose-another-pickup-address' !== jQuery(this).attr("id")) && (! jQuery(this).val())) {
				++errorCount;
				
				jQuery(this).tooltip({
					'title': 'Required field'
				}).tooltip('show');
			} else {
				jQuery(this).tooltip('destroy');
			}
		});
		
		if (jQuery("#cd-source-city").val() === jQuery("#cd-destination-city").val()) {
			if (! jQuery("#cd-source-zip-code").val()) {
				++errorCount;
				
				jQuery("#cd-source-zip-code").tooltip({
					'title': 'Required field'
				}).tooltip('show');
			} else {
				jQuery("#cd-source-zip-code").tooltip('destroy');
			}
			
			if (! jQuery("#cd-destination-zip-code").val()) {
				++errorCount;
				
				jQuery("#cd-destination-zip-code").tooltip({
					'title': 'Required field'
				}).tooltip('show');
			} else {
				jQuery("#cd-destination-zip-code").tooltip('destroy');
			}
		}
		
		if (! jQuery("#item-base-weight").val()) {
			++errorCount;
			
			jQuery("#item-base-weight").tooltip({
				'title': 'Required field'
			}).tooltip('show');
		} else {
			jQuery("#item-base-weight").tooltip('destroy');
		}
		
		if (! jQuery("#item-invoice-value").val()) {
			++errorCount;
			
			jQuery("#item-invoice-value").tooltip({
				'title': 'Required field'
			}).tooltip('show');
		} else {
			jQuery("#item-invoice-value").tooltip('destroy');
		}
		
		if (errorCount) {
			jQuery(".global-form-error").show();
			
			return false;
		}
		
		return true;
	};
	
	var validateOrderForm = function() {
		var errorCount = 0;
		jQuery(".errorHandler").hide();
		jQuery("#shipment-addresses .validationError").remove();
		
		jQuery("#shipment-addresses input, #shipment-addresses select").each(function() {
			var thisElementId = jQuery(this).attr("id");
			var exclude = ["pickup-mid-name", "pickup-address-line-2", "pickup-land-mark", "pickup-email", "choose-another-billing-address", "billing-mid-name", "billing-address-line-2", "billing-land-mark", "shipping-mid-name", "shipping-address-line-2", "shipping-land-mark", "shipping-email", "order_for_client_from_dashboard_deliveryChargeId"];
			
			if ((-1 === exclude.indexOf(thisElementId)) && (! jQuery(this).val())) {
				++errorCount;
				
				jQuery(this).tooltip({
					'title': 'Required field'
				}).tooltip('show');
			} else {
				jQuery(this).tooltip('destroy');
			}
		});
		
		var emailRegex = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		
		if ((jQuery("#shipment-addresses #billing-email").val()) && (! emailRegex.test(jQuery("#shipment-addresses #billing-email").val()))) {
			++errorCount;
			
			jQuery("#shipment-addresses #billing-email").tooltip({
				'title': 'Required field'
			}).tooltip('show');
		} else {
			jQuery("#shipment-addresses #billing-email").tooltip('destroy');
		}
		
		if ((jQuery("#shipment-addresses #shipping-email").val()) && (! emailRegex.test(jQuery("#shipment-addresses #shipping-email").val()))) {
			++errorCount;
			
			jQuery("#shipment-addresses #shipping-email").tooltip({
				'title': 'Required field'
			}).tooltip('show');
		} else {
			jQuery("#shipment-addresses #shipping-email").tooltip('destroy');
		}
		
		if (errorCount) {
			jQuery(".global-form-error").show();
			
			return false;
		}
		
		return true;
	};
	
	jQuery("#choose-another-pickup-address").on("change", function() {
		var addressId = jQuery(this).val();
		if (addressId) {
			jQuery(".server-fault").hide();
			jQuery.ajax({
				url: setPreferredAddressUrl,
				type: "post",
				dataType: "json",
				data: {
					'addressId': addressId
				},
				beforeSend: function(jqXHR, settings) {
                    /**
                     * Show the loader as page overlay.
                     */
                    $("#ajaxLoader").show();
				},
				success: function(response, textStatus, jqXHR) {
					if (true === response.status) {
						var countryList = '<option value="">-Select A Country</option>';
						var stateList = '<option value="">-Select A State-</option>';
						var cityList = '<option value="">-Select A City-</option>';
						jQuery.each(response.address.countryList, function(key, value) {
							var selectedClass = (key == response.address.countryId) ? 'selected="selected"' : ''
							countryList += '<option value="' + key + '"' + selectedClass + '>' + value + '</option>';
						});
						jQuery.each(response.address.stateList, function(key, value) {
							var selectedClass = (key == response.address.stateId) ? 'selected="selected"' : ''
							stateList += '<option value="' + key + '"' + selectedClass + '>' + value + '</option>';
						});
						jQuery.each(response.address.cityList, function(key, value) {
							var selectedClass = (key == response.address.cityId) ? 'selected="selected"' : ''
							cityList += '<option value="' + key + '"' + selectedClass + '>' + value + '</option>';
						});
						jQuery(".cd-country-selectors:first").empty().html(countryList);
						jQuery(".cd-state-selectors:first").empty().html(stateList);
						jQuery(".cd-city-selectors:first").empty().html(cityList);
						jQuery("#cd-source-zip-code").val(response.address.zipCode);
						jQuery("#pickup-first-name").val(response.address.firstName);
						jQuery("#pickup-mid-name").val(response.address.midName);
						jQuery("#pickup-last-name").val(response.address.lastName);
						jQuery("#pickup-address-line-1").val(response.address.addressLine1);
						jQuery("#pickup-address-line-2").val(response.address.addressLine2);
						jQuery("#pickup-country").empty().html(countryList);
						jQuery("#pickup-state").empty().html(stateList);
						jQuery("#pickup-city").empty().html(cityList);
						jQuery("#pickup-zip-code").val(response.address.zipCode);
						jQuery("#pickup-email").val(response.address.email);
						jQuery("#pickup-contact-number").val(response.address.contactNumber);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					jQuery(".server-fault").show();
				},
				complete: function(jqXHR, textStatus) {
                    /**
                     * Hide the page overlay loader.
                     */
                    $("#ajaxLoader").hide();
				}
			});
		}
	});
	
	jQuery("#choose-another-billing-address").on("change", function() {
		var addressId = jQuery(this).val();
		if (addressId) {
			jQuery(".server-fault").hide();
			jQuery.ajax({
				url: setPreferredAddressUrl,
				type: "post",
				dataType: "json",
				data: {
					'addressId': addressId
				},
                beforeSend: function(jqXHR, settings) {
                    /**
                     * Show the loader as page overlay.
                     */
                    $("#ajaxLoader").show();
                },
				success: function(response, textStatus, jqXHR) {
					if (true === response.status) {
						var countryList = '<option value="">-Select A Country</option>';
						var stateList = '<option value="">-Select A State-</option>';
						var cityList = '<option value="">-Select A City-</option>';
						jQuery.each(response.address.countryList, function(key, value) {
							var selectedClass = (key == response.address.countryId) ? 'selected="selected"' : ''
							countryList += '<option value="' + key + '"' + selectedClass + '>' + value + '</option>';
						});
						jQuery.each(response.address.stateList, function(key, value) {
							var selectedClass = (key == response.address.stateId) ? 'selected="selected"' : ''
							stateList += '<option value="' + key + '"' + selectedClass + '>' + value + '</option>';
						});
						jQuery.each(response.address.cityList, function(key, value) {
							var selectedClass = (key == response.address.cityId) ? 'selected="selected"' : ''
							cityList += '<option value="' + key + '"' + selectedClass + '>' + value + '</option>';
						});
						jQuery("#billing-first-name").val(response.address.firstName);
						jQuery("#billing-mid-name").val(response.address.midName);
						jQuery("#billing-last-name").val(response.address.lastName);
						jQuery("#billing-address-line-1").val(response.address.addressLine1);
						jQuery("#billing-address-line-2").val(response.address.addressLine2);
						jQuery("#billing-country").empty().html(countryList);
						jQuery("#billing-state").empty().html(stateList);
						jQuery("#billing-city").empty().html(cityList);
						jQuery("#billing-zip-code").val(response.address.zipCode);
						jQuery("#billing-email").val(response.address.email);
						jQuery("#billing-contact-number").val(response.address.contactNumber);
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					jQuery(".server-fault").show();
				},
				complete: function(jqXHR, textStatus) {
                    /**
                     * Hide the page overlay loader.
                     */
                    $("#ajaxLoader").hide();
				}
			});
		}
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
            beforeSend: function(jqXHR, settings) {
                /**
                 * Show the loader as page overlay.
                 */
                $("#ajaxLoader").show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //
            },
			success: function(response, textStatus, jqXHR) {
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
				
				jQuery("#pickup-country").val(countryId);
				
				jQuery(element).parent().next(".dropdown").find(".cd-state-selectors").empty().html(stateOptions);
				if ('cd-source-country' == jQuery(element).attr("id")) {
					jQuery("#pickup-state").empty().html(stateOptions);
				}
				if ('cd-destination-country' == jQuery(element).attr("id")) {
					jQuery("#shipping-state").empty().html(stateOptions);
				}
				
				jQuery(element).parent().next(".dropdown").next(".dropdown").find(".cd-city-selectors").empty().html(cityOptions);
				if ('cd-source-country' == jQuery(element).attr("id")) {
					jQuery("#pickup-city").empty().html(cityOptions);
				}
				if ('cd-destination-country' == jQuery(element).attr("id")) {
					jQuery("#shipping-city").empty().html(cityOptions);
				}
			},
            complete: function(jqXHR, textStatus) {
                /**
                 * Hide the page overlay loader.
                 */
                $("#ajaxLoader").hide();
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
            beforeSend: function(jqXHR, settings) {
                /**
                 * Show the loader as page overlay.
                 */
                $("#ajaxLoader").show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //
            },
			success: function(response, textStatus, jqXHR) {
				var cityList = response.cityList;
				var cityOptions = "<option value=''>-Select A City-</option>";
				var i = 0;
				
				jQuery.each(cityList, function(key, value) {
					cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
				});
				
				jQuery(element).parent().next(".dropdown").find(".cd-city-selectors").empty().html(cityOptions);
				if ('cd-source-state' == jQuery(element).attr("id")) {
					jQuery("#pickup-city").empty().html(cityOptions);
				}
				if ('cd-destination-state' == jQuery(element).attr("id")) {
					jQuery("#shipping-city").empty().html(cityOptions);
				}
			},
            complete: function(jqXHR, textStatus) {
                /**
                 * Hide the page overlay loader.
                 */
                $("#ajaxLoader").hide();
            }
		});
	});
	
	jQuery("#item-invoice-value").on("input", function() {
		var itemInvoiceValue = jQuery(this).val();
		
		if (parseFloat(5000) <= parseFloat(itemInvoiceValue)) {
			jQuery("#risk-type-container").show();
			jQuery(".risk-types:first").prop("checked", true);
		} else {
			jQuery(".risk-types").prop("checked", false);
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
            beforeSend: function(jqXHR, settings) {
                /**
                 * Show the loader as page overlay.
                 */
                $("#ajaxLoader").show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //
            },
			success: function(response, textStatus, jqXHR) {
				var itemRelatedSecondaryTypeList = response.itemRelatedSecondaryTypeList;
				var itemRelatedSecondaryTypeListMarkUp = "<option value=''>-Select A Secondary Type-</option>";
				var i = 1;
				
				jQuery.each(itemRelatedSecondaryTypeList, function(key, value) {
					itemRelatedSecondaryTypeListMarkUp += "<option value='" + key +"'" + ((1 === i++) ? 'selected="selected"' : '') + ">" + value + "</option>";
				});
				
				jQuery("#item-secondary-type").empty().html(itemRelatedSecondaryTypeListMarkUp);
			},
            complete: function(jqXHR, textStatus) {
                /**
                 * Hide the page overlay loader.
                 */
                $("#ajaxLoader").hide();
            }
		});
	});
	
	jQuery("#prev-button").on("click", function(e) {
		jQuery("#tab-shipment-addresses > a").removeClass("selected");
		jQuery("#tab-shipment-addresses > a").addClass("inactiveLink");
		jQuery("#tab-booking-options > a").removeClass("inactiveLink");
		jQuery("#tab-booking-options > a").addClass("selected");
		jQuery("#usual1 ul").idTabs("tab-booking-options");
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
            beforeSend: function(jqXHR, settings) {
                /**
                 * Show the loader as page overlay.
                 */
                $("#ajaxLoader").show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //
            },
			success: function(response, textStatus, jqXHR) {
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
				
				jQuery(element).parent().next(".dropdown").find(".state-selectors").empty().html(stateOptions);
				
				jQuery(element).parent().next(".dropdown").next(".dropdown").find(".city-selectors").empty().html(cityOptions);
			},
            complete: function(jqXHR, textStatus) {
                /**
                 * Hide the page overlay loader.
                 */
                $("#ajaxLoader").hide();
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
            beforeSend: function(jqXHR, settings) {
                /**
                 * Show the loader as page overlay.
                 */
                $("#ajaxLoader").show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                //
            },
			success: function(response, textStatus, jqXHR) {
				var cityList = response.cityList;
				var cityOptions = "<option value=''>-Select A City-</option>";
				var i = 0;
				
				jQuery.each(cityList, function(key, value) {
					cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
				});
				
				jQuery(element).parent().next(".dropdown").find(".city-selectors").empty().html(cityOptions);
			},
            complete: function(jqXHR, textStatus) {
                /**
                 * Hide the page overlay loader.
                 */
                $("#ajaxLoader").hide();
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
				beforeSend: function(jqXHR, settings) {
					jQuery(".delivery-mode-error").find("span").remove();
					jQuery(".errorHandler").hide();
					
					jQuery("#check-deliverability-button").prop("disabled", true);

                    /**
                     * Show the loader as page overlay.
                     */
                    $("#ajaxLoader").show();
				},
				success: function(response, textStatus, jqXHR) {
					if (response.no_deliverability_error) {
						jQuery(".no-deliverability-error").show();
					} else if (response.delivery_mode_error) {
						jQuery(".delivery-mode-error").find(".alert").append("<span>" + response.delivery_mode_error + "</span>");
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
						jQuery("#pickup-country").val(sourceCountryId);
						jQuery("#pickup-state").val(sourceStateId);
						jQuery("#pickup-city").val(sourceCityId);
						jQuery("#pickup-zip-code").val(sourceZipCode);
						jQuery("#shipping-country").val(destinationCountryId);
						jQuery("#shipping-state").val(destinationStateId);
						jQuery("#shipping-city").val(destinationCityId);
						jQuery("#shipping-zip-code").val(destinationZipCode);
						
						if ('' !== jQuery("#pickup-zip-code").val()) {
							jQuery("#pickup-zip-code").prop("disabled", true);
						} else {
							jQuery("#pickup-zip-code").prop("disabled", false);
						}
						
						if ('' !== jQuery("#shipping-zip-code").val()) {
							jQuery("#shipping-zip-code").prop("disabled", true);
						} else {
							jQuery("#shipping-zip-code").prop("disabled", false);
						}
						
						jQuery("#tab-booking-options > a").removeClass("selected");
						jQuery("#tab-booking-options > a").addClass("inactiveLink");
						jQuery("#tab-shipment-addresses > a").removeClass("inactiveLink");
						jQuery("#tab-shipment-addresses > a").addClass("selected");
						jQuery("#usual1 ul").idTabs("tab-shipment-addresses");
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					jQuery(".server-fault").show();
				},
				complete: function (jqXHR, textStatus) {
					jQuery("#check-deliverability-button").prop("disabled", false);

                    /**
                     * Hide the page overlay loader.
                     */
                    $("#ajaxLoader").hide();
				}
			});
		}
	});
	
	jQuery("#book-a-shipment-button").on("click", function(e) {
		e.preventDefault();
		
		if (validateCheckDeliverabilityForm() && validateOrderForm()) {
			$("#pickup-country, #pickup-state, #pickup-city, #pickup-zip-code, #shipping-country, #shipping-state, #shipping-city, #shipping-zip-code").prop("disabled", false);
			jQuery.ajax({
				url: bookShipmentUrl,
				type: "post",
				dataType: "json",
				data: jQuery("#book-a-shipment-form").serialize(),
				beforeSend: function(jqXHR, settings) {
					jQuery(".errorHandler").hide();
					
					jQuery("#book-a-shipment-button").prop("disabled", true);

                    /**
                     * Show the loader as page overlay.
                     */
                    $("#ajaxLoader").show();
				},
				success: function(response, textStatus, jqXHR) {
					if ('validationErrors' === response.status) {
						jQuery.each(response.errorMessages, function(key, value) {
							key = key.replace("-id", "");
							jQuery("#" + key).parent().append("<span class='help-block'>" + value + "</span>");
							jQuery("#" + key).closest(".form-group").addClass("has-error");
						});
					} else if ('success' === response.status) {
						self.location.href = orderConfirmationUrl;
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
					jQuery(".server-fault").show();
				},
				complete: function(jqXHR, textStatus) {
					jQuery("#book-a-shipment-button").prop("disabled", false);

                    /**
                     * Hide the page overlay loader.
                     */
                    $("#ajaxLoader").hide();
				}
			});
		}
	});
});