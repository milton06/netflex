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
            var countryIds = [];
    
            $(".singleRecordSelector").each(function() {
                if ($(this).parent('[class*="icheckbox"]').hasClass("checked")) {
                    countryIds.push($(this).val());
                }
            });
    
            if (0 < countryIds.length) {
                countryIds = countryIds.join("-");
        
                $.ajax({
                    url: bulkStatusChangeUrl,
                    type: "POST",
                    dataType: "json",
                    data: {
                        "countryIds": countryIds,
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