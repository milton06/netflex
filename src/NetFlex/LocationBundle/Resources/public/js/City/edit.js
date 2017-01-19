$(document).ready(function() {
    "use strict";
    
    $('input[type="checkbox"].flat-grey').iCheck({
        checkboxClass: 'icheckbox_flat-grey',
    });
    
    $("#countryId").on("change", function() {
        var countryId = $(this).val();
        
        $.ajax({
            url: stateListFetchUrl,
            type: "POST",
            dataType: "json",
            data: {
                "countryId": countryId
            },
            success: function(data) {
                var stateList = data.stateList;
                var stateOptions = "<option value=''>-Select A State-</option>";
                
                $.each(stateList, function(key, value) {
                    stateOptions += "<option value='" + key + "' " + ((key == selectedStateId) ? 'selected': '') + ">" + value + "</option>";
                });
                
                $("#stateId").empty().html(stateOptions);
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