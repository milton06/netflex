$(document).ready(function() {
	var fullscreen = function() {
		$('.banner').css({
			width: $(window).width(),
			height: $(window).height()
		});
	};
	fullscreen();
	$(window).resize(function() {
		fullscreen();
	});
	
	$("#login-trigger").click(function() {
		$('#login-content').slideToggle();
		$(this).toggleClass("active");
		if ($(this).hasClass("active")) {
			$(this).find("span").html("&#x25b2;");
		}
		$("#signup-content").slideUp();
	});
	
	$(":input[data-watermark]").each(function () {
		$(this).val($(this).attr("data-watermark"));
		$(this).css("color", "#a0a0a0");
		$(this).bind("focus", function () {
			if ($(this).val() ===$(this).attr("data-watermark")) {
				$(this).val("");
			}
			$(this).css("color","#000000");
		});
		$(this).bind("blur", function () {
			if ("" === $(this).val()) {
				$(this).val($(this).attr("data-watermark"));
				$(this).css("color", "#a0a0a0");
			} else {
				$(this).css("color", "#000000");
			}
		});
	});
	
	$(".fancybox").fancybox();
});
$(window).load(function() {
	if ($("#loginError").val()) {
		$("#login-trigger").trigger("click");
	}
});
