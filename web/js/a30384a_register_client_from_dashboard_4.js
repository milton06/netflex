var registerClientFromDashboard = function () {
	var activateCustomStyledCheckBox = function () {
		jQuery('input[type="checkbox"].flat-grey').iCheck({
			checkboxClass: 'icheckbox_flat-grey',
		});
	};
	var runRegisterClientFromDashboardValidator = function() {
		var form = $('#form-register-client-from-dashboard');
		var errorHandler = $('.errorHandler', form);
		var successHandler = $('.successHandler', form);
		
		$('#form-register-client-from-dashboard').validate({
			errorElement: "span",
			errorClass: 'help-block',
			errorPlacement: function (error, element) {
				if (element.attr("type") == "radio" || element.attr("type") == "checkbox") {
					error.insertAfter($(element).closest('.form-group').children('div').children().last());
				} else {
					error.insertAfter(element);
				}
			},
			ignore: "",
			rules: {
				"netflex_userbundle_user[password]": {
					maxlength: 255
				},
			},
			messages: {
				"netflex_userbundle_user[password]": {
					maxlength: 'Password cannot be more than 255 characters'
				}
			},
			invalidHandler: function (event, validator) {
				successHandler.hide();
				errorHandler.show();
			},
			highlight: function (element) {
				$(element).closest('.help-block').removeClass('valid');
				$(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
			},
			unhighlight: function (element) {
				$(element).closest('.form-group').removeClass('has-error');
			},
			success: function (label, element) {
				label.addClass('help-block valid');
				$(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
			},
			submitHandler: function (form) {
				errorHandler.hide();
				form.submit();
			}
		});
	};
	return {
		init: function () {
			activateCustomStyledCheckBox();
			runRegisterClientFromDashboardValidator();
		}
	};
}();

jQuery(document).ready(function() {
	jQuery("#add-another-address-button").off().on("click", function(e) {
		e.preventDefault();
		
		var addressList = jQuery("#address-container");
		var newAddressWidget = addressList.data("prototype");
		
		newAddressWidget = newAddressWidget.replace(/__name__/g, addressCount);
		addressCount++;
		
		addressList.append(newAddressWidget);
	});
	
	jQuery("#address-container").off().on("click", ".remove-address-button", function(e) {
		e.preventDefault();
		
		jQuery(this).parent().parent().parent().parent().remove();
	});
	
	jQuery("#add-another-email-button").off().on("click", function(e) {
		e.preventDefault();
		
		var emailList = jQuery("#email-container");
		var newEmailWidget = emailList.data("prototype");
		
		newEmailWidget = newEmailWidget.replace(/__name__/g, emailCount);
		emailCount++;
		
		emailList.append(newEmailWidget);
	});
	
	jQuery("#email-container").off().on("click", ".remove-email-button", function(e) {
		e.preventDefault();
		
		jQuery(this).parent().parent().parent().parent().remove();
	});
	
	jQuery("#add-another-contact-button").off().on("click", function(e) {
		e.preventDefault();
		
		var contactList = jQuery("#contact-container");
		var newContactWidget = contactList.data("prototype");
		
		newContactWidget = newContactWidget.replace(/__name__/g, contactCount);
		contactCount++;
		
		contactList.append(newContactWidget);
	});
	
	jQuery("#contact-container").off().on("click", ".remove-contact-button", function(e) {
		e.preventDefault();
		
		jQuery(this).parent().parent().parent().parent().remove();
		
		contactCount--;
	});
	
	jQuery("#address-container").on("change", ".country", function(e) {
		var element = jQuery(this);
		var countryId = jQuery(this).val();
		
		jQuery.ajax({
			url: stateListFetchUrl,
			type: "POST",
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
				
				jQuery(element).parent().parent().next(".form-group").find(".state").empty().html(stateOptions);
				
				jQuery(element).parent().parent().next(".form-group").next(".form-group").find(".city").empty().html(cityOptions);
			}
		});
	});
	
	jQuery("#address-container").on("change", ".state", function(e) {
		var element = jQuery(this);
		var stateId = jQuery(this).val();
		
		jQuery.ajax({
			url: cityListFetchUrl,
			type: "POST",
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
				
				jQuery(element).parent().parent().next(".form-group").find(".city").empty().html(cityOptions);
			}
		});
	});
	
	jQuery("#address-container").on("ifChecked", ".primary-address-selector", function(e) {
		var element = jQuery(this);
		var thisId = jQuery(element).attr("id");
		var thisRelatedAddressType = jQuery(element).parent().parent().parent().parent().prev(".form-group").find(".address-type-selector").val();
		
		jQuery("#address-container .primary-address-selector").each(function() {
			var thatId = jQuery(this).attr("id");
			
			if ((thisId !== thatId) && (jQuery(this).parent('[class*="icheckbox"]').hasClass("checked"))) {
				var thatRelatedAddressType = jQuery(this).parent().parent().parent().parent().prev(".form-group").find(".address-type-selector").val();
				
				if (thisRelatedAddressType == thatRelatedAddressType) {
					swal("Not Allowed!", "You can set only one billing and one pickup address as preferred at a time!");
					
					jQuery("#" + thisId).prop("checked", false);
					jQuery("#address-container .primary-address-selector").iCheck(update);
				}
			}
		});
	});
	
	jQuery("#address-container").on("change", ".address-type-selector", function(e) {
		var element = jQuery(this);
		var thisId = jQuery(element).attr("id");
		var thisValue = jQuery(element).val();
		var thisClosestPrimarySetter = jQuery(element).parent().parent().next(".form-group").find(".primary-address-selector");
		var thisClosestPrimarySetterId = jQuery(element).parent().parent().next(".form-group").find(".primary-address-selector").attr("id");
		var isThisMadePrimary = jQuery(thisClosestPrimarySetter).parent('[class*="icheckbox"]').hasClass("checked");
		
		jQuery("#address-container .address-type-selector").each(function() {
			var thatId = jQuery(this).attr("id");
			var thatValue = jQuery(this).val();
			var thatClosestPrimarySetter = jQuery(this).parent().parent().next(".form-group").find(".primary-address-selector");
			var isThatMadeMadePrimary = jQuery(thatClosestPrimarySetter).parent('[class*="icheckbox"]').hasClass("checked");
			
			if ((thisId !== thatId) && (thisValue === thatValue) && (isThisMadePrimary === isThatMadeMadePrimary)) {
				swal("Not Allowed!", "You can set only one billing and one pickup address as preferred at a time!");
				
				jQuery("#" + thisClosestPrimarySetterId).prop("checked", false);
				jQuery("#address-container .primary-address-selector").iCheck("update");
			}
		});
	});
	
	jQuery("#email-container").on("ifChecked", ".primary-email-selector", function(e) {
		var primaryEmailCount = 1;
		var thisId = jQuery(this).attr("id");
		
		jQuery("#email-container .primary-email-selector").each(function() {
			if (jQuery(this).parent('[class*="icheckbox"]').hasClass("checked")) {
				primaryEmailCount++;
			}
		});
		
		if (1 < primaryEmailCount) {
			swal("Not Allowed!", "You can set only one email as preferred at a time!");
			
			jQuery(this).prop("checked", false);
			jQuery("#email-container .primary-email-selector").iCheck(update);
		}
	});
	
	jQuery("#contact-container").on("ifChecked", ".primary-contact-selector", function(e) {
		var primaryContactCount = 1;
		var thisId = jQuery(this).attr("id");
		
		jQuery("#contact-container .primary-contact-selector").each(function() {
			if (jQuery(this).parent('[class*="icheckbox"]').hasClass("checked")) {
				primaryContactCount++;
			}
		});
		
		if (1 < primaryContactCount) {
			swal("Not Allowed!", "You can set only one contact number as preferred at a time!");
			
			jQuery(this).prop("checked", false);
			jQuery("#contact-container .primary-contact-selector").iCheck(update);
		}
	});
	
	jQuery(document).ready(function() {
		if ($(".show-tab").length) {
			$('.show-tab').bind('click', function (e) {
				e.preventDefault();
				var tabToShow = $(this).attr("href");
				if ($(tabToShow).length) {
					$('a[href="' + tabToShow + '"]').tab('show');
				}
			});
		};
	});
	
	if (0 < jQuery(".server-message").length) {
		setTimeout('jQuery(".server-message").remove()', 5000);
	}
});