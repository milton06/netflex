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
