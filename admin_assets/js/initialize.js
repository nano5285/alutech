$(function() {
	// set focus
	$('.focus').focus();
	
	// disable auto slug creation if updating existing page
	if ($("#action").val() != "edit") {
		$('#title').syncTranslit({
			destination:"slug"
		});
	}
	
	// fancybox initialization
	$("a[rel='lightbox'], .fancybox").fancybox();
		
	// date picker
	$( ".datepicker" ).datetimepicker({
		dateFormat: "yy-mm-dd",
		timeFormat: "HH:mm:ss"
	});

	// autohide input label
	$('.toggle-label').each(function() {
		var $this = $(this),
			label = $this.find('label'),
			input = $this.find('input, textarea');

		if(input.val()) {
			label.hide();
		}

		input.on('keyup keypress', function() {
			if(input.val().length > 0) {
				label.hide();
			} else {
				label.show();
			}
		});
	});

	// category position
	$('#position').on('keyup keypress', function() {
		$(this).val( $(this).val().replace(/[^\d.\..]/g, '') );
	});

	// identifier
	$('#identifier').on('keyup keypress', function() {
		$(this).val( $(this).val().replace(/ /g, '') );
	});	

	// hide info messages
	$(window).on('load', function() {
		setTimeout(function() {
			$('.notifications').slideUp();
		}, 4000);
	});

	// fixed sidebars
	var windowHeight = $(window).height(),
		sidebar = $('div.sidebar-content'),
		sidebarRight = $('aside.sidebar-right .container');

	if(sidebar.outerHeight() > windowHeight) {
		sidebar.removeClass('fixed');
	}	
	if(sidebarRight.outerHeight() > windowHeight - 40) {
		sidebarRight.removeClass('fixed');
	}

	// custom selectbox
	$("select").chosen({
		disable_search: true
	});	

	// confirm delete dialog
	$("#dialog").dialog({
		autoOpen: false,
		modal: true,
		width: 300
	});	
	$(".delete").click(function(e) {
		e.preventDefault();
		var targetUrl = $(this).attr("href");
		openDialog('#dialog', targetUrl);
	});

	// user delete dialog
	$("#user-dialog").dialog({
		autoOpen: false,
		modal: true,
		width: 350,
		height: 270
	});
	$(".user-delete").click(function(e) {
		e.preventDefault();
		var targetUrl = $(this).attr("href");
		var userId = $(this).data('userid');
		$('p.row-user').show();
		$('p.row-user-'+userId).hide();
		openUserDialog('#user-dialog', targetUrl);
	});
	
});