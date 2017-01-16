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
            var cityIds = [];
    
            $(".singleRecordSelector").each(function() {
                if ($(this).parent('[class*="icheckbox"]').hasClass("checked")) {
                    cityIds.push($(this).val());
                }
            });
    
            if (0 < cityIds.length) {
                cityIds = cityIds.join("-");
        
                $.ajax({
                    url: bulkStatusChangeUrl,
                    type: "POST",
                    dataType: "json",
                    data: {
                        "cityIds": cityIds,
                        "changeStatusTo": selectedOption
                    },
                    beforeSend: function() {
                        $(".serverMessage").remove();
                    },
                    success: function(data) {
                        location.reload();
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