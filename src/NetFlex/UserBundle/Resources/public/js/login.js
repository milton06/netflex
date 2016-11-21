var Login = function () {
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
	var runLoginValidator = function () {
		var form = jQuery('.form-login');
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
			runLoginValidator();
		}
	};
}();