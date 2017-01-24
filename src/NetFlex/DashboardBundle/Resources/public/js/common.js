/**
 * Highlights a corresponding page menu as per current ajax request.
 */
var highlightPageMenu = function(source) {
    "use strict";
    
    $(".main-navigation-menu").find("li.active").removeClass("active");
    $(".main-navigation-menu").find("li.open").not($(source).parents("li")).removeClass("open").children("ul.sub-menu").slideUp(200);
    $(source).addClass("active").parents("li").addClass("active");
}

/**
 * Updates the page title as per current ajax request.
 */
var changePageTitle = function(pageTitle) {
    "use strict";
    
    document.title = dashboardPageTitlePrefix + pageTitle;
}

/**
 * Saves the current ajax request state in browser history.
 */
var saveCurrentRequestState = function(requestUrl, source, target, pageTitle) {
    "use strict";
    
    /**
     * State for the current request to set.
     */
    var requestState = {
        'source': source,
        'target': target,
        'pageTitle': pageTitle
    };
    
    /**
     * Push current request state to browser history.
     */
    history.pushState(requestState, null, requestUrl);
};

/**
 * Makes an ajax request upon clicking a resource link.
 */
var forwardRequest = function(event, element, source, target, pageTitle) {
    "use strict";
    
    /**
     * Stop default handling of this event.
     */
    event.preventDefault();
    
    /**
     * URL to request for page content.
     */
    var requestUrl = $(element).attr("href");
    
    /**
     * Make ajax request for page content.
     */
    forward("push", requestUrl, source, target, pageTitle);
};

/**
 * Make ajax request for page content.
 */
var forward = function(mode, requestUrl, source, target, pageTitle) {
    "use strict";
    
    $.ajax({
        url: requestUrl,
        type: "GET",
        dataType: "html",
        beforeSend: function(jqXHR, settings) {
            /**
             * Show the loader as page overlay.
             */
            $("#ajaxLoader").show();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // TODO: Send a mail to developer.
        },
        success: function(data, textStatus, jqXHR) {
            /**
             * Save current resquest state.
             */
            if ("push" === mode) {
                saveCurrentRequestState(requestUrl, source, target, pageTitle);
            }
            
            /**
             * Change the page title.
             */
            if (pageTitle) {
                changePageTitle(pageTitle);
            }
            
            /**
             * Highlight the respective page menu.
             */
            if (source) {
                highlightPageMenu(source);
            }
            
            /**
             * Render the page content.
             */
            $(target).empty().html(data);
            
            /**
             * Remove all previously existing page specific scripts
             */
            $("#pageSpecificScripts").empty();
        },
        complete: function(jqXHR, textStatus) {
            /**
             * Hide the page overlay loader.
             */
            $("#ajaxLoader").hide();
        }
    });
};

/**
 * Make an ajax request on browser history navigation.
 */
window.onpopstate = function(event) {
    "use strict";
    
    /**
     * URL to request for page content.
     */
    var requestUrl = document.location;
    var source = event.state.source;
    var target = event.state.target;
    var pageTitle = event.state.pageTitle;
    
    /**
     * Make ajax request for page content.
     */
    forward("pop", requestUrl, source, target, pageTitle);
};

/**
 * Removes existing error messages.
 */
var removeExistingErrorMessages = function(container) {
    "use strict";
    
    $(container).find(".help-block").remove();
    $(container).find(".form-group").removeClass("has-error");
};

/**
 * Displays error message.
 */
var displayErrorMessage = function(target, message) {
    "use strict";
    
    $(target).after("<span class='help-block'>" + message + "</span>").closest(".form-group").addClass("has-error");
};

/**
 * Toggles page-specific server message.
 */
var toggleMessage = function(message, target, toggleMode, scrollTo) {
    "use strict";
    
    if ("ON" === toggleMode) {
        if (scrollTo) {
            $("html, body").animate({
                scrollTop: $(scrollTo).offset().top
            }, "slow");
        }
        
        $(target + ">.message").empty().html(message);
        $(target).show();
    } else {
        $(target + ">.message").empty();
        $(target).hide();
    }
};

/**
 * Publishes multiple records at one go.
 */
var bulkPublishRecords = function(event, element, redirectUrl, recordSelectors) {
    "use strict";
    
    event.preventDefault();
    
    var selectedRecords = [];
    
    $(recordSelectors).each(function() {
        if ($(this).parent().is(".checked")) {
            selectedRecords.push($(this).val());
        }
    });
    
    if (selectedRecords) {
        var bulkPublishUrl = $(element).attr("href");
        var ids = selectedRecords.join("-");
    
        bulkPublishUrl = bulkPublishUrl.replace("0-0", ids);
        
        $.ajax({
            url: bulkPublishUrl,
            type: "POST",
            dataType: "json",
            beforeSend: function(jqXHR, settings) {
                /**
                 * Remove all existing server messages.
                 */
                toggleMessage("", ".serverError", "OFF");
                toggleMessage("", ".serverWarning", "OFF");
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
                if ("publishError" === data.status) {
                    /**
                     * Handle server side error.
                     */
                    toggleMessage(data.message, ".serverError", "ON", "#pageContentContainer");
                } else if ("publishWarning" === data.status) {
                    /**
                     * Handle server side warning.
                     */
                    toggleMessage(data.message, ".serverWarning", "ON", "#pageContentContainer");
                    
                    /**
                     * Reload the page.
                     */
                    setTimeout(function() {
                        "use strict";
                        
                        self.location.href = redirectUrl;
                    }, 3000);
                } else {
                    toggleMessage(data.message, ".serverSuccess", "ON", "#pageContentContainer");
                    
                    /**
                     * Reload the page.
                     */
                    setTimeout(function() {
                        "use strict";

                        self.location.href = redirectUrl;
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
 * Publishes a record.
 */
var publishRecord = function(event, element) {
    "use strict";
    
    event.preventDefault();
    
    var publishUrl = $(element).attr("href");
    
    $.ajax({
        url: publishUrl,
        type: "POST",
        dataType: "json",
        beforeSend: function(jqXHR, settings) {
            /**
             * Remove all existing server messages.
             */
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
            if ("trashError" === data.status) {
                /**
                 * Handle server side error.
                 */
                toggleMessage(data.message, ".serverError", "ON", "#pageContentContainer");
            } else {
                toggleMessage(data.message, ".serverSuccess", "ON", "#pageContentContainer");
                
                /**
                 * Reload the page.
                 */
                setTimeout(function() {
                    "use strict";
                    
                    location.reload();
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
};

/**
 * Trashes multiple records at one go.
 */
var bulkTrashRecords = function(event, element, redirectUrl, recordSelectors) {
    "use strict";
    
    event.preventDefault();
    
    var selectedRecords = [];
    
    $(recordSelectors).each(function() {
        if ($(this).parent().is(".checked")) {
            selectedRecords.push($(this).val());
        }
    });
    
    if (selectedRecords) {
        var bulkTrashUrl = $(element).attr("href");
        var ids = selectedRecords.join("-");
    
        bulkTrashUrl = bulkTrashUrl.replace("0-0", ids);
        
        $.ajax({
            url: bulkTrashUrl,
            type: "POST",
            dataType: "json",
            beforeSend: function(jqXHR, settings) {
                /**
                 * Remove all existing server messages.
                 */
                toggleMessage("", ".serverError", "OFF");
                toggleMessage("", ".serverWarning", "OFF");
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
                if ("trashError" === data.status) {
                    /**
                     * Handle server side error.
                     */
                    toggleMessage(data.message, ".serverError", "ON", "#pageContentContainer");
                } else if ("trashWarning" === data.status) {
                    /**
                     * Handle server side warning.
                     */
                    toggleMessage(data.message, ".serverWarning", "ON", "#pageContentContainer");
                    
                    /**
                     * Reload the page.
                     */
                    setTimeout(function() {
                        "use strict";
                        
                        self.location.href = redirectUrl;
                    }, 3000);
                } else {
                    toggleMessage(data.message, ".serverSuccess", "ON", "#pageContentContainer");
                    
                    /**
                     * Reload the page.
                     */
                    setTimeout(function() {
                        "use strict";

                        self.location.href = redirectUrl;
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
 * Trashes a record.
 */
var trashRecord = function(event, element) {
    "use strict";
    
    event.preventDefault();
    
    var trashUrl = $(element).attr("href");
    
    $.ajax({
        url: trashUrl,
        type: "POST",
        dataType: "json",
        beforeSend: function(jqXHR, settings) {
            /**
             * Remove all existing server messages.
             */
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
            if ("trashError" === data.status) {
                /**
                 * Handle server side error.
                 */
                toggleMessage(data.message, ".serverError", "ON", "#pageContentContainer");
            } else {
                toggleMessage(data.message, ".serverSuccess", "ON", "#pageContentContainer");
                
                /**
                 * Reload the page.
                 */
                setTimeout(function() {
                    "use strict";
                    
                    location.reload();
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
};

/**
 * Deletes multiple records at one go.
 */
var bulkDeleteRecords = function(event, element, redirectUrl, recordSelectors) {
    "use strict";
    
    event.preventDefault();
    
    var selectedRecords = [];
    
    $(recordSelectors).each(function() {
        if ($(this).parent().is(".checked")) {
            selectedRecords.push($(this).val());
        }
    });
    
    if (selectedRecords) {
        var bulkDeleteUrl = $(element).attr("href");
        var ids = selectedRecords.join("-");
        var selectedRecordCount = selectedRecords.length;
        
        bulkDeleteUrl = bulkDeleteUrl.replace("0-0", ids);
        bulkDeleteUrl = bulkDeleteUrl.replace(9999999999, selectedRecordCount);
        
        $.ajax({
            url: bulkDeleteUrl,
            type: "POST",
            dataType: "json",
            beforeSend: function(jqXHR, settings) {
                /**
                 * Remove all existing server messages.
                 */
                toggleMessage("", ".serverError", "OFF");
                toggleMessage("", ".serverWarning", "OFF");
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
                if ("deleteError" === data.status) {
                    /**
                     * Handle server side error.
                     */
                    toggleMessage(data.message, ".serverError", "ON", "#pageContentContainer");
                } else if ("deleteWarning" === data.status) {
                    /**
                     * Handle server side warning.
                     */
                    toggleMessage(data.message, ".serverWarning", "ON", "#pageContentContainer");
                    
                    redirectUrl = redirectUrl.replace(9999999999, data.page);
                    
                    /**
                     * Reload the page.
                     */
                    setTimeout(function() {
                        "use strict";
                        
                        self.location.href = redirectUrl;
                    }, 3000);
                } else {
                    toggleMessage(data.message, ".serverSuccess", "ON", "#pageContentContainer");
                    
                    redirectUrl = redirectUrl.replace(9999999999, data.page);
                    
                    /**
                     * Reload the page.
                     */
                    setTimeout(function() {
                        "use strict";

                        self.location.href = redirectUrl;
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
 * Deletes a record.
 */
var deleteRecord = function(event, element, redirectUrl) {
    "use strict";
    
    event.preventDefault();
    
    var deleteUrl = $(element).attr("href");
    
    $.ajax({
        url: deleteUrl,
        type: "POST",
        dataType: "json",
        beforeSend: function(jqXHR, settings) {
            /**
             * Remove all existing server messages.
             */
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
            if ("deleteError" === data.status) {
                /**
                 * Handle server side error.
                 */
                toggleMessage(data.message, ".serverError", "ON", "#pageContentContainer");
            } else {
                toggleMessage(data.message, ".serverSuccess", "ON", "#pageContentContainer");
    
                redirectUrl = redirectUrl.replace(9999999999, data.page);
                
                /**
                 * Redirect.
                 */
                setTimeout(function() {
                    "use strict";
                    
                    self.location.href = redirectUrl;
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
};

/**
 * Initiates search.
 */
var search = function(event, form) {
    "use strict";
    
    event.preventDefault();
    
    var searchUrl = $(form).attr("action");
    var searchData = $(form).serialize();
    
    $.ajax({
        url: searchUrl,
        type: "POST",
        data: searchData,
        dataType: "json",
        beforeSend: function(jqXHR, settings) {
            /**
             * Show the loader as page overlay.
             */
            $("#ajaxLoader").show();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // TODO: Send a mail to developer.
        },
        success: function(data, textStatus, jqXHR) {
            if ("success" === data.status) {
                var redirectUrl = data.redirectUrl;
    
                self.location.href = redirectUrl;
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

/**
 * Exits from current search mode.
 */
var exitSearch = function(event, element) {
    "use strict";
    
    event.preventDefault();
    
    var exitSearchUrl = $(element).attr("href");
    
    $.ajax({
        url: exitSearchUrl,
        type: "POST",
        dataType: "json",
        beforeSend: function(jqXHR, settings) {
            /**
             * Show the loader as page overlay.
             */
            $("#ajaxLoader").show();
        },
        error: function(jqXHR, textStatus, errorThrown) {
            // TODO: Send a mail to developer.
        },
        success: function(data, textStatus, jqXHR) {
            if ("success" === data.status) {
                var redirectUrl = data.redirectUrl;
                
                self.location.href = redirectUrl;
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