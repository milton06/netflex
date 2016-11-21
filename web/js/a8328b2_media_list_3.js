var mediaList = function() {
	/**
	 * Activates custom styled checkboxes.
	 */
	var activateCustomStyledCheckBox = function () {
		jQuery('input[type="checkbox"].flat-grey').iCheck({
			checkboxClass: 'icheckbox_flat-grey',
		});
	};
	
	return {
		init: function() {
			activateCustomStyledCheckBox();
		}
	};
}();

/**
 * Checks the record selector checkboxes at-a-go upon checking the bulk selector checkbox.
 */
jQuery("#bulk-record-selector").on("ifChecked", function() {
	jQuery(".single-record-selector").iCheck("check");
});

/**
 * Unchecks the record selector checkboxes at-a-go upon unchecking the bulk selector checkbox.
 */
jQuery("#bulk-record-selector").on("ifUnchecked", function() {
	jQuery(".single-record-selector").iCheck("uncheck");
});

/**
 * Upon document ready and if a server message exists, removes it after 5s.
 */
jQuery(document).ready(function() {
	if (0 < jQuery(".server-message").length) {
		setTimeout('jQuery(".server-message").remove()', 5000);
	}
});

/**
 * Handles single record deletion.
 */
jQuery(".delete-buttons").on("click", function(e) {
	e.preventDefault();
	
	var deleteUrl = jQuery(this).attr("href");
	
	swal({
		title: "Are You Sure To Delete?",
		text: "You will not be able to recover this media once deleted!",
		type: "warning",
		showCancelButton: true,
		confirmButtonColor: "#DD6B55",
		confirmButtonText: "Yes, delete!",
		closeOnConfirm: true,
	},
	function(isConfirm){
		if (isConfirm) {
			self.location = deleteUrl;
		}
	});
});

/**
 * Handles bulk record deletion.
 */
jQuery("#bulk-delete-button").on("click", function(e) {
	e.preventDefault();
	
	var mediaIds = [];
	var selectedRecordCount = 0;
	var deleteUrl = jQuery(this).attr("href");
	
	jQuery(".single-record-selector").each(function() {
		if (jQuery(this).parent('[class*="icheckbox"]').hasClass("checked")) {
			mediaIds.push(jQuery(this).val());
		}
	});
	
	if (0 < mediaIds.length) {
		swal({
			title: "Are You Sure To Delete?",
			text: "You will not be able to recover this medias once deleted!",
			type: "warning",
			showCancelButton: true,
			confirmButtonColor: "#DD6B55",
			confirmButtonText: "Yes, delete!",
			closeOnConfirm: true,
		},
		function(isConfirm){
			if (isConfirm) {
				selectedRecordCount = mediaIds.length;
				mediaIds = mediaIds.join("-");
				
				deleteUrl = deleteUrl.replace('%25mediaIds%25', mediaIds);
				deleteUrl = deleteUrl.replace('%25selectedRecordCount%25', selectedRecordCount);
				
				self.location = deleteUrl;
			} else {
				jQuery(".single-record-selector").iCheck("uncheck");
				jQuery("#bulk-record-selector").iCheck("uncheck");
			}
		});
	}
});
