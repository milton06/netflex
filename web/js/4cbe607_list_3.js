$(document).ready(function() {
    "use strict";
    
    $('input[type="checkbox"].flat-grey').iCheck({
        checkboxClass: 'icheckbox_flat-grey',
    });
    
    $("#sourceCountryId").on("change", function() {
        var element = $(this);
        var countryId = $(this).val();
    
        $.ajax({
            url: stateListFetchUrl,
            type: "post",
            dataType: "json",
            data: {
                'countryId': countryId,
                'excludeStates': true,
            },
            success: function(response) {
                var stateList = response.stateList;
                var cityList = response.cityList;
                var stateOptions = "<option value=''>-All Source States-</option>";
                var cityOptions = "<option value=''>-All Source Cities-</option>";
                var i = 0;
            
                $.each(stateList, function(key, value) {
                    stateOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
            
                i = 0;
                $.each(cityList, function(key, value) {
                    cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
            
                $(element).parent().next(".col-md-3").find("#sourceStateId").empty().html(stateOptions);
                $(element).parent().next(".col-md-3").next(".col-md-3").find("#sourceCityId").empty().html(cityOptions);
            }
        });
    });
    
    $("#sourceStateId").on("change", function(e) {
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
                var cityOptions = "<option value=''>-All Source Cities-</option>";
                var i = 0;
                
                $.each(cityList, function(key, value) {
                    cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                
                $(element).parent().next(".col-md-3").find("#sourceCityId").empty().html(cityOptions);
            }
        });
    });
    
    $("#destinationCountryId").on("change", function() {
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
                var stateOptions = "<option value=''>-All Destination States-</option>";
                var cityOptions = "<option value=''>-All Destination Cities-</option>";
                var i = 0;
                
                $.each(stateList, function(key, value) {
                    stateOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                
                i = 0;
                $.each(cityList, function(key, value) {
                    cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                
                $(element).parent().next(".col-md-3").find("#destinationStateId").empty().html(stateOptions);
                $(element).parent().next(".col-md-3").next(".col-md-3").find("#destinationCityId").empty().html(cityOptions);
            }
        });
    });
    
    $("#destinationStateId").on("change", function(e) {
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
                var cityOptions = "<option value=''>-All Destination Cities-</option>";
                var i = 0;
                
                $.each(cityList, function(key, value) {
                    cityOptions += "<option value='" + key + "' " + ((0 === i++) ? "selected='selected'" : "") + ">" + value + "</option>";
                });
                
                $(element).parent().next(".col-md-3").find("#destinationCityId").empty().html(cityOptions);
            }
        });
    });
    
    $("#bulkRecordSelector").on("ifChecked", function() {
        $(".singleRecordSelector").iCheck("check");
    });
    $("#bulkRecordSelector").on("ifUnchecked", function() {
        $(".singleRecordSelector").iCheck("uncheck");
    });
    
    $("#bulkOperationSelector").on("change", function(e) {
        console.log(bulkStatusChangeUrl);
        
        var selectedOption = $(this).val();
        
        if ('' !== selectedOption) {
            var deliveryChargeIds = [];
    
            $(".singleRecordSelector").each(function() {
                if ($(this).parent('[class*="icheckbox"]').hasClass("checked")) {
                    deliveryChargeIds.push($(this).val());
                }
            });
    
            if (0 < deliveryChargeIds.length) {
                deliveryChargeIds = deliveryChargeIds.join("-");
        
                $.ajax({
                    url: bulkStatusChangeUrl,
                    type: "POST",
                    dataType: "json",
                    data: {
                        "deliveryChargeIds": deliveryChargeIds,
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
});