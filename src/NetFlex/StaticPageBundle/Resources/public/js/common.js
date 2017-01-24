/**
 * Adds a new page section.
 */
var addAnotherPageSection = function(event, container) {
    "use strict";
    
    event.preventDefault();
    
    var prototype = $(container).data("prototype");
    var newPageSection = prototype.replace(/__name__/g, existingPageSectionCount++);
    
    $(container).append(newPageSection);
    
    $(container).find("textarea.editor").each(function() {
        var editor = $(this).ckeditor().editor;
        CKFinder.setupCKEditor(editor);
    });
};

/**
 * Removes a page section.
 */
var removePageSection = function(event, element) {
    "use strict";
    
    event.preventDefault();
    
    $(element).parents(".pageSections").remove();
};

/**
 * Validates step one.
 */
var validateStepOne = function() {
    "use strict";
    
    var isValid = true;
    var title = $("#title").val();
    var slug = $("#slug").val();
    
    removeExistingErrorMessages("#step-1");
    
    if (! title) {
        isValid = false;
        
        displayErrorMessage("#title", "Title is required");
    } else if (! /^[a-z0-9 ]+$/i.test(title)) {
        isValid = false;
    
        displayErrorMessage("#title", "Title can contain only alphanumeric characters and spaces");
    } else {
        //
    }
    
    if (! slug) {
        isValid = false;
        
        displayErrorMessage("#slug", "Slug is required");
    } else if (! /^[a-z0-9\-]+$/i.test(slug)) {
        isValid = false;
        
        displayErrorMessage("#slug", "Slug can contain only alphanumeric characters and '-'s");
    } else {
        //
    }
    
    return isValid;
};

/**
 * Validates step three.
 */
var validateStepThree = function() {
    "use strict";
    
    var isValid = true;
    
    removeExistingErrorMessages("#step-3");
    
    $(".editor").each(function() {
        if (! $(this).val()) {
            isValid = false;
            
            displayErrorMessage("#" + $(this).attr("id"), "Content is required");
        }
        
        var contentPosition = $(this).closest(".form-group").next(".form-group").find(".contentPosition");
        if (! $(contentPosition).val()) {
            isValid = false;
    
            displayErrorMessage("#" + $(contentPosition).attr("id"), "Position is required");
        }
    });
    
    return isValid;
};

$(document).ready(function() {
    "use strict";
    
    /**
     * Formats title and slug.
     */
    $("#title").on("input", function() {
        var title = $("#title").val();
        var slug = "";
    
        /**
         * Remove consequtive spaces.
         */
        title = title.replace(/(\s[\s]+)/, " ");
        $("#title").val(title);
    
        /**
         * Change uppercase letters in title to lowercase.
         * Replace spaces in title with '-'.
         * Remove trailing '-'.
         */
        slug = title.toLowerCase().replace(/\s/g, "-");
        slug = ("-" === slug[(slug.length - 1)]) ? slug.slice(0, (slug.length - 1)) : slug;
        $("#slug").val(slug);
    });
});