var toggleServerMessage = function(displayMode, messageType, message, messageContainer) {
    if ("OFF" === displayMode) {
        $(messageContainer + ">span").empty();
        $(messageContainer).hide();
    } else if ("ON" === displayMode) {
        if ("ERROR" === messageType) {
            if ($(messageContainer).hasClass("alert-success")) {
                $(messageContainer).removeClass("alert-success");
            } else {
                //
            }
            
            $(messageContainer).addClass("alert-danger");
        } else if ("SUCCESS" === messageType) {
            if ($(messageContainer).hasClass("alert-danger")) {
                $(messageContainer).removeClass("alert-danger");
            } else {
                //
            }
            
            $(messageContainer).addClass("alert-success");
        }
        
        $(messageContainer + ">span").html(message);
        $(messageContainer).show();
        
        $("html, body").animate({
            scrollTop: $(".main-content").offset().top
        }, 2000);
    } else {
        //
    }
};

var toggleErrorMessage = function(displayMode, targetElementId, errorMessage) {
    if ("OFF" === displayMode) {
        $(".form-group").hasClass("has-error");
        $(".form-group").removeClass("has-error");
        $(".validationError").remove();
    } else if ("ON" === displayMode) {
        $("#" + targetElementId).after("<span class='help-block validationError'>" + errorMessage + "</span>");
        $("#" + targetElementId).closest(".form-group").addClass("has-error");
    }
}

var changeDeliveryZone = function(event, element) {
    event.preventDefault();
    
    var selectedDeliveryZoneId = $(element).val();
    
    $.ajax({
        url: deliveryZoneChangeUrl,
        type: "POST",
        data: {
            "selectedDeliveryZoneId": selectedDeliveryZoneId,
        },
        dataType: "json",
        beforeSend: function (jqXHR, settings) {
            toggleServerMessage("OFF", null, null, ".serverMessage");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //
        },
        success: function(data, textStatus, jqXHR) {
            "use strict";
            
            if ("success" === data.status) {
                var countryList = data.destinationCountryList;
                var defaultDestinationCountry = data.defaultDestinationCountry;
                var stateList = data.destinationStateList;
                var defaultDestinationState = data.defaultDestinationState;
                var cityList = data.destinationCityList;
                var defaultDestinationCity = data.defaultDestinationCity;
                
                var countryOptions = "<option value=''>-Select A Destination Country-</option>";
                var stateOptions = "<option value=''>-Select A Destination State-</option>";
                var cityOptions = "<option value=''>-Select A Destination City-</option>";
    
                $.each(countryList, function(key, value) {
                    countryOptions += "<option value='" + key + "' " + ((key == defaultDestinationCountry) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                $.each(stateList, function(key, value) {
                    stateOptions += "<option value='" + key + "' " + ((key == defaultDestinationState) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                $.each(cityList, function(key, value) {
                    cityOptions += "<option value='" + key + "' " + ((key == defaultDestinationCity) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
    
                $("#destinationCountryId").empty().html(countryOptions);
                $("#destinationStateId").empty().html(stateOptions);
                $("#destinationCityId").empty().html(cityOptions);
                
                if (1 == selectedDeliveryZoneId) {
                    if ("none" == $("#sourceZipCodeRange").parent().parent().parent().css("display")) {
                        $("#sourceZipCodeRange").val("");
                        $("#sourceZipCodeRange").parent().parent().parent().show();
                    }
                    if ("none" == $("#destinationZipCodeRange").parent().parent().parent().css("display")) {
                        $("#destinationZipCodeRange").val("");
                        $("#destinationZipCodeRange").parent().parent().parent().show();
                    }
                } else {
                    if ("block" == $("#sourceZipCodeRange").parent().parent().parent().css("display")) {
                        $("#sourceZipCodeRange").val("");
                        $("#sourceZipCodeRange").parent().parent().parent().hide();
                    }
                    if ("block" == $("#destinationZipCodeRange").parent().parent().parent().css("display")) {
                        $("#destinationZipCodeRange").val("");
                        $("#destinationZipCodeRange").parent().parent().parent().hide();
                    }
                }
                
                if (1 == selectedDeliveryZoneId || 2 == selectedDeliveryZoneId) {
                    if (! $("#destinationCountryId").hasClass("countrySelectors")) {
                        $("#destinationCountryId").addClass("countrySelectors");
                    }
                } else {
                    if ($("#destinationCountryId").hasClass("countrySelectors")) {
                        $("#destinationCountryId").removeClass("countrySelectors");
                    }
                }
            }
        },
        complete: function(jqXHR, textStatus) {
            //
        }
    });
};

var updateDeliveryCharge = function(event, element) {
    event.preventDefault();
    
    var deliveryChargeForm = $(element).closest("form");
    var deliveryChargeUpdateUrl = $(deliveryChargeForm).attr("action");
    deliveryChargeUpdateUrl = deliveryChargeUpdateUrl.replace(9999999999, $("#deliveryZone").val());
    
    $.ajax({
        url: deliveryChargeUpdateUrl,
        type: "POST",
        data: $(deliveryChargeForm).serialize(),
        dataType: "json",
        beforeSend: function (jqXHR, settings) {
            toggleServerMessage("OFF", null, null, ".serverMessage");
            toggleErrorMessage("OFF");
        },
        error: function(jqXHR, textStatus, errorThrown) {
            toggleServerMessage("ON", "ERROR", "Server error occurred", ".serverMessage");
        },
        success: function(data, textStatus, jqXHR) {
            if ("failure" === data.status) {
                if ("redundancyError" === data.reason) {
                    toggleServerMessage("ON", "ERROR", data.redundancyError, ".serverMessage");
                } else if ("validationError" === data.reason) {
                    $.each(data.validationErrorList, function(key, value) {
                        toggleErrorMessage("ON", key, value);
                    });
                    
                    toggleServerMessage("ON", "ERROR", "You have some form errors. Please check below.", ".serverMessage");
                }
            } else if ('success' === data.status) {
                
            } else {
                //
            }
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
    
    $(".main-content").on("change", ".countrySelectors", function(e) {
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
    
    $(".main-content").on("change", ".stateSelectors", function(e) {
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
