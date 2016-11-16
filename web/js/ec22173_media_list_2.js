var mediaList = function() {
	var activateCustomStylesCheckBox = function () {
		jQuery('input[type="checkbox"].flat-grey').iCheck({
			checkboxClass: 'icheckbox_flat-grey',
		});
	};
	
	return {
		init: function() {
			activateCustomStylesCheckBox();
		}
	};
}();

jQuery("#bulk-record-selector").on("ifChecked", function() {
	jQuery(".single-record-selector").iCheck("check");
});
jQuery("#bulk-record-selector").on("ifUnchecked", function() {
	jQuery(".single-record-selector").iCheck("uncheck");
});

jQuery("#bulk-delete-button").on("click", function(e) {
	e.preventDefault();
	
	var mediaIds = [];
	var selectedRecordCount = 0;
	
	jQuery(".single-record-selector").each(function() {
		if (jQuery(this).parent('[class*="icheckbox"]').hasClass("checked")) {
			mediaIds.push(jQuery(this).val());
		}
	});
	
	if (0 < mediaIds.length) {
		selectedRecordCount = mediaIds.length;
		mediaIds = mediaIds.join("-");
	}
});
