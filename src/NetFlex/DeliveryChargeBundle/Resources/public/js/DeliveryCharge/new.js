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

var renderDeliveryChargeNewForm = function(event, element, targetContainer) {
    event.preventDefault();
    
    var deliveryZoneForm = $(element).closest("form");
    
    $.ajax({
        url: $(deliveryZoneForm).attr("action"),
        type: "POST",
        data: $(deliveryZoneForm).serialize(),
        dataType: "html",
        beforeSend: function (jqXHR, settings) {
            toggleServerMessage("OFF", null, null, ".serverMessage");

            /**
             * Show the loader as page overlay.
             */
            $("#ajaxLoader").show();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            //
        },
        success: function(data, textStatus, jqXHR) {
            $(targetContainer).empty().html(data);
        },
        complete: function(jqXHR, textStatus) {
            /**
             * Hide the page overlay loader.
             */
            $("#ajaxLoader").hide();
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
            toggleServerMessage("OFF", null, null, ".serverMessage");
            toggleErrorMessage("OFF");

            /**
             * Show the loader as page overlay.
             */
            $("#ajaxLoader").show();
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
                var editUrl = data.redirectTo;
                
                self.location.href = editUrl;
            } else {
                //
            }
        },
        complete: function(jqXHR, textStatus) {
            /**
             * Hide the page overlay loader.
             */
            $("#ajaxLoader").hide();
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
            beforeSend: function (jqXHR, settings) {
                /**
                 * Show the loader as page overlay.
                 */
                $("#ajaxLoader").show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toggleServerMessage("ON", "ERROR", "Server error occurred", ".serverMessage");
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
            },
            complete: function(jqXHR, textStatus) {
                /**
                 * Hide the page overlay loader.
                 */
                $("#ajaxLoader").hide();
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
            beforeSend: function (jqXHR, settings) {
                /**
                 * Show the loader as page overlay.
                 */
                $("#ajaxLoader").show();
            },
            error: function(jqXHR, textStatus, errorThrown) {
                toggleServerMessage("ON", "ERROR", "Server error occurred", ".serverMessage");
            },
            success: function(response, textStatus, jqXHR) {
                var cityList = response.cityList;
                var cityOptions = "<option value=''>-Select A City-</option>";
                var i = 0;
                
                $.each(cityList, function(key, value) {
                    cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                
                $(element).parent().parent().parent().next(".col-md-3").find(".citySelectors").empty().html(cityOptions);
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
