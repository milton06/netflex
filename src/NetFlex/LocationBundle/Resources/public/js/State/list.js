$(document).ready(function() {
    "use strict";
    
    $('input[type="checkbox"].flat-grey').iCheck({
        checkboxClass: 'icheckbox_flat-grey',
    });
    
    $("#bulkRecordSelector").on("ifChecked", function() {
        $(".singleRecordSelector").iCheck("check");
    });
    $("#bulkRecordSelector").on("ifUnchecked", function() {
        $(".singleRecordSelector").iCheck("uncheck");
    });
    
    $("#bulkOperationSelector").on("change", function(e) {
        var selectedOption = $(this).val();
        
        if ('' !== selectedOption) {
            var stateIds = [];
    
            $(".singleRecordSelector").each(function() {
                if ($(this).parent('[class*="icheckbox"]').hasClass("checked")) {
                    stateIds.push($(this).val());
                }
            });
    
            if (0 < stateIds.length) {
                stateIds = stateIds.join("-");
        
                $.ajax({
                    url: bulkStatusChangeUrl,
                    type: "POST",
                    dataType: "json",
                    data: {
                        "stateIds": stateIds,
                        "changeStatusTo": selectedOption
                    },
                    beforeSend: function(jqXHR, settings) {
                        $(".serverMessage").remove();
                        /**
                         * Show the loader as page overlay.
                         */
                        $("#ajaxLoader").show();
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        //
                    },
                    success: function(data, textStatus, jqXHR) {
                        location.reload();
                    },
                    complete: function(jqXHR, textStatus) {
                        /**
                         * Hide the page overlay loader.
                         */
                        $("#ajaxLoader").hide();
                    }
                });
            }
        }
    });
});

$(window).load(function() {
    "use strict";
    
    if (0 < $(".serverMessage").length) {
        setTimeout("$('.serverMessage').remove()", 3000);
    }
    
    $("#bulkRecordSelector, .singleRecordSelector").iCheck("uncheck");
});