var registerClientFromDashboard = function () {
	var activateCustomStyledCheckBox = function () {
		jQuery('input[type="checkbox"].flat-grey').iCheck({
			checkboxClass: 'icheckbox_flat-grey',
		});
	};
	var runSetDefaultValidation = function () {
		jQuery.validator.setDefaults({
			errorElement: "span",
			errorClass: 'help-block',
			errorPlacement: function (error, element) {
				if (element.attr("type") == "radio" || element.attr("type") == "checkbox") {
					error.insertAfter(jQuery(element).closest('.form-group').children('div').children().last());
				} else if (element.attr("name") == "card_expiry_mm" || element.attr("name") == "card_expiry_yyyy") {
					error.appendTo(jQuery(element).closest('.form-group').children('div'));
				} else {
					error.insertAfter(element);
				}
			},
			ignore: ':hidden',
			highlight: function (element) {
				jQuery(element).closest('.help-block').removeClass('valid');
				jQuery(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
			},
			unhighlight: function (element) {
				jQuery(element).closest('.form-group').removeClass('has-error');
			},
			success: function (label, element) {
				label.addClass('help-block valid');
				jQuery(element).closest('.form-group').removeClass('has-error');
			},
			highlight: function (element) {
				jQuery(element).closest('.help-block').removeClass('valid');
				jQuery(element).closest('.form-group').addClass('has-error');
			},
			unhighlight: function (element) {
				jQuery(element).closest('.form-group').removeClass('has-error');
			}
		});
	};
	var runRegisterClientFromDashboardValidator = function () {
		var form = jQuery('.form-register-client-from-dashboard');
		var errorHandler = jQuery('.errorHandler', form);
		form.validate({
			rules: {
				_username: {
					required: true
				},
				_password: {
					required: true
				}
			},
			submitHandler: function (form) {
				errorHandler.hide();
				form.submit();
			},
			invalidHandler: function (event, validator) {
				errorHandler.show();
			}
		});
	};
	return {
		init: function () {
			activateCustomStyledCheckBox();
			runSetDefaultValidation();
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
		var thisRelatedAddressType = jQuery(element).parent().parent().parent().prev(".form-group").find(".address-type-selector").val();
		
		jQuery(".primary-address-selector").each(function() {
			var thatId = jQuery(this).attr("id");
			
			if ((thisId !== thatId) && (jQuery(this).parent('[class*="icheckbox"]').hasClass("checked"))) {
				var thatRelatedAddressType = jQuery(this).parent().parent().parent().prev(".form-group").find(".address-type-selector").val();
				
				if (thisRelatedAddressType == thatRelatedAddressType) {
					swal("Not Allowed!", "You can set only one billing and one pickup address as preferred at a time!");
					
					jQuery("#" + thisId).iCheck("uncheck");
				}
			}
		});
	});
	
	jQuery("#email-container").on("ifChecked", ".primary-email-selector", function(e) {
		var primaryEmails = jQuery(".primary-email-selector").parent('[class*="icheckbox"]').hasClass("checked").length;
		
		if (1 < primaryEmails) {
			swal("Not Allowed!", "You can set only one email as preferred at a time!");
			
			jQuery(this).iCheck("uncheck");
		}
	});
	
	jQuery("#contact-container").on("ifChecked", ".primary-contact-selector", function(e) {
		var primaryContactCount = 1;
		var thisId = jQuery(this).attr("id");
		
		jQuery(".primary-contact-selector").each(function() {
			if (jQuery(this).parent('[class*="icheckbox"]').hasClass("checked")) {
				primaryContactCount++;
			}
		});
		
		if (1 < primaryContactCount) {
			swal("Not Allowed!", "You can set only one contact number as preferred at a time!");
			
			jQuery("#" + thisId).parent('[class*="icheckbox"]').removeClass("checked");
		}
	});
});