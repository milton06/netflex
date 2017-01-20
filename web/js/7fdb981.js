$(document).ready(function() {
    "use strict";
    
    $("#countryId").on("change", function() {
        var countryId = $(this).val();
        
        $.ajax({
            url: stateListFetchUrl,
            type: "POST",
            dataType: "json",
            data: {
                "countryId": countryId
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
            success: function(data, textStatus, jqXHR) {
                var stateList = data.stateList;
                var stateOptions = "<option value=''>-Select A State-</option>";
                var i = 0;
                
                $.each(stateList, function(key, value) {
                    stateOptions += "<option value='" + key + "' " + ((0 === i++) ? 'selected': '') + ">" + value + "</option>";
                });
                
                $("#stateId").empty().html(stateOptions);
            },
            complete: function(jqXHR, textStatus) {
                /**
                 * Hide the page overlay loader.
                 */
                $("#ajaxLoader").hide();
            }
        });
    });
});

$(window).load(function() {
    "use strict";
    
    if (0 < $(".serverMessage").length) {
        setTimeout("$('.serverMessage').remove()", 3000);
    }
});