var renderDeliveryChargeNewForm = function(event, element, targetContainer) {
    event.preventDefault();
    
    var deliveryZoneForm = $(element).closest("form");
    
    $.ajax({
        url: $(deliveryZoneForm).attr("action"),
        type: "POST",
        data: $(deliveryZoneForm).serialize(),
        dataType: "html",
        beforeSend: function (jqXHR, settings) {
            //
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //
        },
        success: function(data, textStatus, jqXHR) {
            $(targetContainer).empty().html(data);
        },
        complete: function(jqXHR, textStatus) {
            //
        }
    });
};

var saveNewDeliveryCharge = function(event, element) {
    event.preventDefault();
    
    var deliveryChargeForm = $(element).closest("form");
    
    $.ajax({
        url: $(deliveryChargeForm).attr("action"),
        type: "POST",
        data: $(deliveryChargeForm).serialize(),
        dataType: "json",
        beforeSend: function (jqXHR, settings) {
            //
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //
        },
        success: function(data, textStatus, jqXHR) {
            //
        },
        complete: function(jqXHR, textStatus) {
            //
        }
    });
};

$(document).ready(function() {
    $('input[type="checkbox"].flat-grey, input[type="radio"].flat-grey').iCheck({
        checkboxClass: 'icheckbox_flat-grey',
        radioClass: 'iradio_flat-grey',
        increaseArea: '10%' // optional
    });
    
    $(".countrySelectors").on("change", function(e) {
        var element = $(this);
        var countryId = $(this).val();
        
        $.ajax({
            url: stateListFetchUrl,
            type: "post",
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
                
                $.each(stateList, function(key, value) {
                    stateOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                
                i = 0;
                $.each(cityList, function(key, value) {
                    cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
    
                $(element).parent().parent().parent().next(".col-md-3").find(".stateSelectors").empty().html(stateOptions);
                $(element).parent().parent().parent().next(".col-md-3").next(".col-md-3").find(".citySelectors").empty().html(cityOptions);
            }
        });
    });
    
    $(".stateSelectors").on("change", function(e) {
        var element = $(this);
        var stateId = $(this).val();
        
        $.ajax({
            url: cityListFetchUrl,
            type: "post",
            dataType: "json",
            data: {
                'stateId': stateId
            },
            success: function(response) {
                var cityList = response.cityList;
                var cityOptions = "<option value=''>-Select A City-</option>";
                var i = 0;
                
                $.each(cityList, function(key, value) {
                    cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                
                $(element).parent().parent().parent().next(".col-md-3").find(".citySelectors").empty().html(cityOptions);
            }
        });
    });
});
