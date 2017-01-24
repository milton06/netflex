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
var StaticPageNew = function () {
    var wizardContent = $("#wizard");
    
    /**
     * Initializes the form wizard.
     */
    var initWizard = function () {
        wizardContent.smartWizard({
            selected: 0,
            keyNavigation: false,
            onLeaveStep: leaveAStepCallback,
            onShowStep: onShowStep
        });
        var numberOfSteps = 0;
        animateBar();
    };
    
    /**
     * Animates the form wizard progress bar.
     */
    var animateBar = function (val) {
        if ((typeof val == 'undefined') || val == "") {
            val = 1;
        };
        
        numberOfSteps = $('.swMain > ul > li').length;
        
        var valueNow = Math.floor(100 / numberOfSteps * val);
        $('.step-bar').css('width', valueNow + '%');
    };
    
    /**
     * Handles moving back and forth the form wizard steps and finishing form editing.
     */
    var onShowStep = function (obj, context) {
        $(".next-step").unbind("click").click(function (e) {
            e.preventDefault();
            wizardContent.smartWizard("goForward");
        });
        
        $(".back-step").unbind("click").click(function (e) {
            e.preventDefault();
            wizardContent.smartWizard("goBackward");
        });
        
        $(".finish-step").unbind("click").click(function (e) {
            e.preventDefault();
            onFinish(obj, context);
        });
    };
    
    /**
     * Handles leaving the current form wizard step event.
     */
    var leaveAStepCallback = function (obj, context) {
        return validateStep(context.fromStep, context.toStep);
    };
    
    /**
     * Handles finishing of form editing.
     */
    var onFinish = function (obj, context) {
        /**
         * 1. validate all steps.
         * 2. If validation fails:
         *     a) show errors.
         *    Else:
         *     a) proceed with step 3.
         * 3. Make ajax request to save static page data.
         * 4. Handle server response.
         */
        if (validateAllSteps()) {
            $.ajax({
                url: staticPageNewUrl,
                type: "POST",
                dataType: "json",
                data: $("#staticPageNewForm").serialize(),
                beforeSend: function(jqXHR, settings) {
                    /**
                     * Remove all existing error and server messages.
                     */
                    removeExistingErrorMessages("#wizard");
                    toggleMessage("", ".serverError", "OFF");
                    
                    /**
                     * Show the loader as page overlay.
                     */
                    $("#ajaxLoader").show();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    toggleMessage(" Server error encountered.", ".serverError", "ON", "#pageContentContainer");
                    // TODO: Send a mail to developer.
                },
                success: function(data, textStatus, jqXHR) {
                    if ("validationError" === data.status) {
                        /**
                         * Handle server side validation errors.
                         */
                        $.each(data.errorMessages, function(key, value) {
                            "use strict";
    
                            displayErrorMessage("#" + key, value);
                        });
                        
                        toggleMessage(" Static page couldn't be saved. Please check under each tab for errors.", ".serverError", "ON", "#pageContentContainer");
                    } else {
                        var id = data.id;
                        staticPageEditUrl = staticPageEditUrl.replace("9999999999", id);
    
                        self.location.href = staticPageEditUrl;
                    }
                },
                complete: function(jqXHR, textStatus) {
                    /**
                     * Hide the page overlay loader.
                     */
                    $("#ajaxLoader").hide();
                }
            });
        }
    };
    
    /**
     * Validates a form wizard step.
     */
    var validateStep = function (stepnumber, nextstep) {
        /**
         * Call the respective validation function. Proceed on success; else show error in current step and don't proceed.
         */
        var isValid = true;
        
        switch(stepnumber) {
            case 1:
                isValid = validateStepOne();
                
                break;
                
            case 3:
                isValid = validateStepThree();
                
                break;
        }
        
        if (isValid) {
            animateBar(nextstep);
        }
        
        return isValid;
    };
    
    /**
     * Validates all form wizard steps at a go.
     */
    var validateAllSteps = function () {
        /**
         * Call functions to validate steps sequentially. If any of them fails, return false; else return true.
         */
        return (validateStepOne() && validateStepThree());
    };
    
    /**
     * Initializes CKEDITOR.
     */
    var runCKEditor = function () {
        var editor = $("textarea.editor").ckeditor().editor;
        CKFinder.setupCKEditor(editor);
    };
    
    return {
        init: function () {
            initWizard();
            runCKEditor();
        }
    };
}();
