jQuery(function () {
    "use strict";
    
    jQuery("#fileupload").fileupload({
        url: multiMediaUploadUrl,
        minFileSize: 5000,
        maxFileSize: 100000000,
        acceptFileTypes: /\.(jpe?g|png|mp4|webm|mp3|doc|docx|pdf|xlsx|zip)$/i,
        previewMaxWidth: 100,
        previewMaxHeight: 100,
        prependFiles: true,
        singleFileUploads: false,
        imagePreviewName: "imagePreview",
        audioPreviewName: "audioPreview",
        videoPreviewName: "videoPreview",
    })
    .bind("fileuploadprocessstart", function(e) {
	    jQuery(".fileinput-button").addClass("disabled");
	    jQuery(".start").addClass("disabled");
	    jQuery(".cancel-upload").addClass("disabled");
	    jQuery(".back-to-list").addClass("disabled");
    })
    .bind("fileuploadprocessdone", function(e, data) {
	    jQuery(".cancel-upload").removeClass("disabled");
	    jQuery(".back-to-list").removeClass("disabled");
	    
	    var fileRows = jQuery(".file-upload-container tbody .template-upload");
	    var fileCount = fileRows.length;
	    var i = 0;
	    
	    if (10 < fileCount) {
		    while(10 < fileCount) {
			    jQuery(fileRows[i]).find(".error").html("Maximum number of files exceeded");
			
			    i++;
			    fileCount--;
		    }
	    } else {
		    jQuery(".fileinput-button").removeClass("disabled");
		    jQuery(".start").removeClass("disabled");
	    }
    })
    .bind("fileuploadprocessfail", function(e) {
	    jQuery(".cancel-upload").removeClass("disabled");
	    jQuery(".back-to-list").removeClass("disabled");
    })
    .bind("fileuploadstart", function(e) {
	    jQuery(".fileinput-button").addClass("disabled");
	    jQuery(".start").addClass("disabled");
	    jQuery(".cancel-upload").addClass("disabled");
	    jQuery(".back-to-list").addClass("disabled");
    })
    .bind("fileuploadalways", function(e) {
	    jQuery(".fileinput-button").removeClass("disabled");
	    jQuery(".start").removeClass("disabled");
	    jQuery(".cancel-upload").removeClass("disabled");
	    jQuery(".back-to-list").removeClass("disabled");
    });
    
    jQuery("#fileupload").addClass("fileupload-processing");
    
    jQuery.ajax({
        url: jQuery("#fileupload").fileupload("option", "url"),
        dataType: "json",
        context: jQuery("#fileupload")[0]
    }).always(function () {
        jQuery(this).removeClass("fileupload-processing");
    }).done(function (result) {
        jQuery(this).fileupload("option", "done").call(this, jQuery.Event("done"), {result: result});
    });
});
