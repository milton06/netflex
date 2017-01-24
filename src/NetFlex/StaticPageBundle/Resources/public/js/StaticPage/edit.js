var StaticPageEdit = function () {
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
                url: staticPageEditUrl,
                type: "POST",
                dataType: "json",
                data: $("#staticPageEditForm").serialize(),
                beforeSend: function(jqXHR, settings) {
                    /**
                     * Remove all existing error and server messages.
                     */
                    removeExistingErrorMessages("#wizard");
                    toggleMessage("", ".serverError", "OFF");
                    toggleMessage("", ".serverSuccess", "OFF");
                    
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
                        
                        toggleMessage(" Static page couldn't be updated. Please check under each tab for errors.", ".serverError", "ON", "#pageContentContainer");
                    } else {
                        toggleMessage(" Static page was updated successfully.", ".serverSuccess", "ON", "#pageContentContainer");
    
                        /**
                         * Reload the page.
                         */
                        setTimeout(function() {
                            "use strict";
    
                            self.location.href = staticPageEditUrl;
                        }, 3000);
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
    
    /**
     * Initializes iCheck.
     */
    var runCustomCheck = function () {
        if (0 < $('input[type="checkbox"]').length) {
            $('input[type="checkbox"].square-black').iCheck({
                checkboxClass: 'icheckbox_square'
            });
        };
    };
    
    return {
        init: function () {
            initWizard();
            runCKEditor();
            runCustomCheck();
        }
    };
}();
