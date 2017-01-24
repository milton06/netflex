$(document).ready(function() {
    "use strict";
    
    /**
     * Initializes custom checkboxes.
     */
    if (0 < $('input[type="checkbox"]').length) {
        $('input[type="checkbox"].square-black').iCheck({
            checkboxClass: 'icheckbox_square'
        });
    };
    
    /**
     * Toggles record selectors simultaneously.
     */
    $("#bulkRecordSelector").on("ifChecked", function(event) {
        $(".recordSelectors").iCheck("check");
    });
    $("#bulkRecordSelector").on("ifUnchecked", function(event) {
        $(".recordSelectors").iCheck("uncheck");
    });
    
    /**
     * Handles related date-range picker.
     */
    $("#searchFromDate").datetimepicker({
        format: 'DD/MM/YYYY'
    });
    $("#searchToDate").datetimepicker({
        format: 'DD/MM/YYYY',
        useCurrent: false
    });
    $("#searchFromDate").on("dp.change", function (e) {
        $("#searchToDate").data("DateTimePicker").minDate(e.date);
    });
    $("#searchToDate").on("dp.change", function (e) {
        $("#searchFromDate").data("DateTimePicker").maxDate(e.date);
    });
    
    var xhr;
    
    /**
     * Autocompleted prompt by title.
     */
    $("#searchTitle").autoComplete({
        minChars: 1,
        source: function(term, response) {
            try {
                xhr.abort();
            } catch(e) {
                //
            }
            
            xhr = $.ajax({
                url: autocompleteUrl,
                type: "GET",
                dataType: "json",
                data: {
                    "search": "title",
                    "term": term
                },
                beforeSend: function(jqXHR, settings) {
                    //
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //
                },
                success: function(data) {
                    response(data.autocompleteData);
                },
                complete: function(jqXHR, textStatus) {
                    //
                }
            });
        },
        onSelect: function(e, term, item){
            e.preventDefault();
        }
    });
    
    /**
     * Autocompleted prompt by slug.
     */
    $("#searchSlug").autoComplete({
        minChars: 1,
        source: function(term, response) {
            try {
                xhr.abort();
            } catch(e) {
                //
            }
            
            xhr = $.ajax({
                url: autocompleteUrl,
                type: "GET",
                dataType: "json",
                data: {
                    "search": "slug",
                    "term": term
                },
                beforeSend: function(jqXHR, settings) {
                    //
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    //
                },
                success: function(data) {
                    response(data.autocompleteData);
                },
                complete: function(jqXHR, textStatus) {
                    //
                }
            });
        },
        onSelect: function(e, term, item) {
            e.preventDefault();
        }
    });
});
