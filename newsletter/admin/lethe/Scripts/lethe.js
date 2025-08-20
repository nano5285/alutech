// Javascript

// Preview Content
    $(".fancybox2").fancybox({
		autoSize : true,
		type     : 'inline',
        beforeLoad : function() {
			this.content = tinyMCE.get('details').getContent();
            this.width  = 1000;  
            this.height = 800;
        }
    });	
	
// Standart Content
    $(".fancybox").fancybox({
		autoSize : true,
    });	
	
// Ajax
function getAjax(div,urls,txt){
	$(div).html(txt);
	$.ajax({
	  url: urls,
	  cache: false,
	  success: function(html){
		$(div).html(html);
	  }
	});
}

// Validate Number
function validateNumber(evt) {
    var e = evt || window.event;
    var key = e.keyCode || e.which;

    if (!e.shiftKey && !e.altKey && !e.ctrlKey &&
    // numbers   
    key >= 48 && key <= 57 ||
    // Numeric keypad
    key >= 96 && key <= 105 ||
    // Backspace and Tab and Enter
    key == 8 || key == 9 || key == 13 ||
    // Home and End
    key == 35 || key == 36 ||
    // left and right arrows
    key == 37 || key == 39 ||
    // Del and Ins
    key == 46 || key == 45) {
        // input is VALID
    }
    else {
        // input is INVALID
        e.returnValue = false;
        if (e.preventDefault) e.preventDefault();
    }
}

// Subscribe Form Designer
$(document).ready(function(){
	 $( "#sortable1, #sortable2" ).sortable({
			connectWith: ".connectedSortable",
		}) // .disableSelection();
});

// Code Generate
$(document).ready(function() {
		// Select Form Codes
		$("#sub_forms_1").focus(function() { $(this).select(); } );
		
		// Required Option
		$('.form_req_area').change(function() {
			if($(this).is(":checked")) {
				$(this).closest('li').attr('data-form-req',' required');
			}else{
				$(this).closest('li').attr('data-form-req','');
			}
		});
		
		// Tabs
		$('#form_code_gen1').click(function (e) {
			var site_url = $("#sub_forms_url").val();
			if(site_url.charAt(site_url.length - 1)!='/'){site_url+='/';}
			var form_action = site_url+"newsletter/lethe.newsletter.php?pos=1"; // Add by Form = 1
			var form_unique = $("#sub_form_code1").val();
			var form_style = '';
			if($("#form_alert_mode").is(":checked")) {
				var ajax_code = '<script>$(function(){$("#'+ form_unique +'").submit(function(e){e.preventDefault();dataString=$("#'+ form_unique +'").serialize();$.ajax({type:"POST",url:"'+ form_action +'",data:dataString,dataType:"html",success:function(e){$(".modal-body").html(e);$("#lethe-alert-modal").modal("show");eval(document.getElementById("callback_lethe").innerHTML);},error:function(e){$(".modal-body").html(e);$("#lethe-alert-modal").modal("show");}})})})</script>';
				ajax_code += '<div id="lethe-alert-modal" aria-hidden="true" class="modal fade" style="float:none;"><div class="modal-dialog" style="float:none;"><div class="modal-content"><div class="modal-header" style="float:none;"><strong>Lethe Newsletter</strong><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button></div><div class="modal-body" style="float:none;"></div></div></div></div>';
			}else{
				var ajax_code = '<script>$(function(){$("#'+ form_unique +'").submit(function(e){e.preventDefault();dataString=$("#'+ form_unique +'").serialize();$.ajax({type:"POST",url:"'+ form_action +'",data:dataString,dataType:"html",success:function(e){$("#lethe_results").html("<div>"+e+"</div>");eval(document.getElementById("callback_lethe").innerHTML);},error:function(e){$("#lethe_results").html("<div>Error</div>")}})})})</script>';
			}
			var subscribe_group = '<input type="hidden" name="lethe_subscribe_group" value="0">';
			subscribe_group = subscribe_group.replace('value="0"','value="'+ $( "#form_group option:selected" ).val() +'"');
			
			// Change Style
			if($("input[name='form_model']:radio:checked").val()==0){
				form_style = '<!-- Lethe Newsletter Style -->\n<style>#'+ form_unique +'-form div{ margin-bottom:10px;} #recaptcha_image img{width:90%;}</style>\n<!-- Lethe Newsletter Style -->';
			}
			else if($("input[name='form_model']:radio:checked").val()==1){
				form_style = '<!-- Lethe Newsletter Style -->\n<style>#'+ form_unique +'-form div{display:inline-block; width:auto;} #'+ form_unique +'-form input{display:inline-block; width:auto;} #recaptcha_image img{width:100px;}</style>\n<!-- Lethe Newsletter Style -->';
			}
			else if($("input[name='form_model']:radio:checked").val()==3){
				form_style = '<!-- Lethe Newsletter Style -->\n<style>#'+ form_unique +'-form div{ margin-bottom:10px;} #recaptcha_image img{width:90%;}</style>\n<!-- Lethe Newsletter Style -->';
				form_action = form_action;
			}

			var arr = new Array();
			var arr2 = new Array();
			var arr3 = new Array();

			$('#sortable2 li').each(function() { // Catch Form Sets
			  arr.push($(this).attr('data-form-var')); 
			  arr2.push($(this).attr('data-form-req'));
			  arr3.push($(this).attr('data-form-field'));
			})


			
			var form_codes1 = '';
			var form_item_str = '';
			
			if($("input[name='form_model']:radio:checked").val()!=3){ // Load without table
			$("#sub_form_opt1").val(''); // Reset Options
			form_codes1 += form_style+'\n';
			form_codes1 += '<div id="'+ form_unique +'-form"><form role="form" name="'+ form_unique +'" id="'+ form_unique +'" action="' + form_action + '" method="post">\n\n';
			form_codes1 += '<input type="hidden" name="lkey" id="lkey" value="'+ form_unique +'">'; // Unique Form Code
			form_codes1 += '<div id="lethe_results" style="display:block;"></div>';
			for (var i = 0; i < arr.length; i++) {
			
			if(arr3[i]=='lethe_listbox'){
				form_item_str = htmlDecode(arr[i]);
			}else{
				form_item_str = arr[i];
			}
				form_item_str = form_item_str.replace('type',arr2[i]+' type');
				
				// Patterns
				if($("#"+ arr3[i] +"_pattern").val()){
					form_item_str = form_item_str.replace('input','input pattern="'+ $("#"+ arr3[i] +"_pattern").val() +'"');
					}
				// Date Area
				if(arr3[i]=='lethe_date'){
					var datePickerLinks = '<script src="//code.jquery.com/jquery-1.10.2.js"></script>\n';
					datePickerLinks += '<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">\n';
					datePickerLinks += '<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>\n';
					datePickerLinks += '<script> $(function() {$( "#lethe_date" ).datepicker({ dateFormat: "'+ $("#lethe_date_model").val() +'" });});</script>\n';
					form_item_str = form_item_str.replace('(dateScript)',datePickerLinks);
					
				}
				// Options
				if(arr2[i]==' required'){ // Add Options
					$("#sub_form_opt1").val($("#sub_form_opt1").val()+'['+ arr3[i] +'@required:yes],');
					}else{
						$("#sub_form_opt1").val($("#sub_form_opt1").val()+'['+ arr3[i] +'@required:no],');
						}
				form_codes1 += '	<div>' + form_item_str + '</div>\n';
			}
			form_codes1 += subscribe_group + '\n';
			form_codes1 += '<div><button type="submit" name="letheAddSubscriber" value="letheAddSubscriber" class="btn btn-primary">' + $(this).attr('data-submit-val') + '</button></div>';
			form_codes1 += '\n</form></div><span style="clear:both;"></span>' + ajax_code;
			
			}else{ // Table version
			
			form_codes1 += form_style+'\n';
			form_codes1 += '<div id="'+ form_unique +'-form"><form role="form" name="'+ form_unique +'" id="'+ form_unique +'" action="' + form_action + '" method="post">\n\n';
			form_codes1 += '<input type="hidden" name="lkey" id="lkey" value="'+ form_unique +'">'; // Unique Form Code
			form_codes1 += '<table width="100%" border="0" cellspacing="0" cellpadding="0">';
			form_codes1 += '<tr><td><div id="lethe_results" style="display:block;"></div></td></tr>';
			for (var i = 0; i < arr.length; i++) {
				form_item_str = arr[i];
				form_item_str = form_item_str.replace('type',arr2[i]+' type');
				
				// Patterns
				if($("#"+ arr3[i] +"_pattern").val()){
					form_item_str = form_item_str.replace('input','input pattern="'+ $("#"+ arr3[i] +"_pattern").val() +'"');
					}
				// Date Area
				if(arr3[i]=='lethe_date'){
					var datePickerLinks = '<script src="//code.jquery.com/jquery-1.10.2.js"></script>\n';
					datePickerLinks += '<link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">\n';
					datePickerLinks += '<script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>\n';
					datePickerLinks += '<script> $(function() {$( "#lethe_date" ).datepicker({ dateFormat: "'+ $("#lethe_date_model").val() +'" });});</script>\n';
					form_item_str = form_item_str.replace('(dateScript)',datePickerLinks);
					
				}
				// Options
				
				if(arr2[i]==' required'){ // Add Options
					$("#sub_form_opt1").val($("#sub_form_opt1").val()+'['+ arr3[i] +'@required:yes],');
					}else{
						$("#sub_form_opt1").val($("#sub_form_opt1").val()+'['+ arr3[i] +'@required:no],');
						}
				form_codes1 += '<tr><td><div>' + form_item_str + '</div></td></tr>\n';
			}
			form_codes1 += '<tr><td>'+ subscribe_group +'<div><button type="submit" name="letheAddSubscriber" value="letheAddSubscriber" class="btn btn-primary">' + $(this).attr('data-submit-val') + '</button></div></td></tr>';
			form_codes1 += '\n</table></form></div><span style="clear:both;"></span>' + ajax_code;
			
			}
			
			$($(this).attr('data-submit-form-code')).val(form_codes1);
			$($(this).attr('data-submit-prev-code')).html(form_codes1);
			$('.recapt_area').html('{reCAPTCHA}');
			
		});
		
		// **** Search Panel
		$('#search-opener').click(function() {
			$('#search_panel').toggle('slideDown');
		});
		
});

// ** Create Subscribe Link
$('#addSubscriberLink').click(function (e) {
			var site_url = $("#sub_forms_url").val();
			if(site_url.charAt(site_url.length - 1)!='/'){site_url+='/';}
			var form_action = site_url+"newsletter/lethe.newsletter.php?pos=1"; // Add by Form = 1
			var form_unique = $("#sub_form_code2").val();
			var linkModel = '&lethe_pos=0&lkey=' + $("#sub_form_code2").val();
			
			var els = add_subscrLink.elements;
			for (var i = 0, len = els.length; i < len; ++i) {
				if (els[i].tagName == "INPUT") {
					if (els[i].type == "checkbox" || els[i].type == "radio") {
						linkModel += '&'+ $('#'+els[i].id).data('field-name') +'='+els[i].value; // Checkbox ~ Radio
					} 
					else {
						linkModel += '&'+ $('#'+els[i].id).data('field-name') +'='+els[i].value; // Text
					}
				}
				else if(els[i].tagName == "SELECT"){
					linkModel += '&'+ $('#'+els[i].id).data('field-name') +'='+els[i].options[els[i].selectedIndex].value; // Select
					}
				else if(els[i].tagName == "TEXTAREA"){
					linkModel += '&'+ $('#'+els[i].id).data('field-name') +'='+els[i].value; // Textarea
					}
			}
			
			//if($("#sub_name_cl").val()){linkModel += '&lethe_name='+$("#sub_name_cl").val();}
			//if($("#sub_mail_cl").val()){linkModel += '&lethe_email='+$("#sub_mail_cl").val();}
			//if($("#sub_group_cl").val()){linkModel += '&lethe_subscribe_group='+$( "#sub_group_cl option:selected" ).val();}
			//if($("#sub_comp_cl").val()){linkModel += '&lethe_company='+$("#sub_comp_cl").val();}
			//if($("#sub_phone_cl").val()){linkModel += '&lethe_phone='+$("#sub_phone_cl").val();}
			
			
			linkModel = form_action+linkModel;
			var replaced = '';
			$('#subscr_links2').val(linkModel);
			$('#subscr_links1').val('<a href="'+ linkModel.replace('&','&amp;') +'">'+ linkModel +'</a>');
	});
		
// ** Preview Custom Form
$('#prevCustForms').click(function (e) {
		var chk = '';
		if($("#form_chk").is(":checked")) {chk=' checked';}else{chk='';}
		var form_codes = '<input type="hidden" name="lkey" id="lkey" value="'+ $("#sub_form_code3").val() +'">\n';
		form_codes += '<input type="hidden" name="lethe_subscribe_group" value="'+ $("#sub_group_cc").val() +'">\n';
		form_codes += '<input type="checkbox" name="lethe_newsletter_add" id="lethe_newsletter_add" value="YES"'+ chk +'> <label for="lethe_newsletter_add">Sign Up for Newsletter</label>';
		$('#sub_forms_3').val(form_codes);	
		$('#sub_forms_prev_3').html($('#sub_forms_3').val());		
	});

// ** Short Code Action
$(document).ready(function() {
		$('#short_code_list div').click(function() {
			var myField = tinyMCE.get($(this).attr('data-lethe-code-field'));
			var fieldName = $(this).attr('data-lethe-codes');
			
			if (document.selection) {
				myField.focus();
				sel = document.selection.createRange();
				sel.text = fieldName;
			}
			else if (document.getSelection) {
				tinyMCE.activeEditor.selection.setContent(fieldName);
				myField.focus();
			}
			
		});
});

// ** Run Tooltips and Slicknav
$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();
	$('[data-toggle="tooltip"]').css('cursor','pointer');
	$('#top-navigation-bar-small').slicknav({
		duplicate:false,
		prependTo:'#top-navigation-bar2'
	});
});

// ** Multi Select List
function listbox_moveacross(sourceID, destID) {
    var src = document.getElementById(sourceID);
    var dest = document.getElementById(destID);
 
    for(var count=0; count < src.options.length; count++) {
 
        if(src.options[count].selected == true) {
                var option = src.options[count];
 
                var newOption = document.createElement("option");
                newOption.value = option.value;
                newOption.text = option.text;
                newOption.selected = true;
                try {
                         dest.add(newOption, null); //Standard
                         src.remove(count, null);
                 }catch(error) {
                         dest.add(newOption); // IE only
                         src.remove(count);
                 }
                count--;
        }
    }
}

// Move Listbox Item
function listbox_move(listID, direction) {
 
    var listbox = document.getElementById(listID);
    var selIndex = listbox.selectedIndex;
 
    if(-1 == selIndex) {
        alert("Please select an option to move.");
        return;
    }
 
    var increment = -1;
    if(direction == 'up')
        increment = -1;
    else
        increment = 1;
 
    if((selIndex + increment) < 0 ||
        (selIndex + increment) > (listbox.options.length-1)) {
        return;
    }
 
    var selValue = listbox.options[selIndex].value;
    var selText = listbox.options[selIndex].text;
    listbox.options[selIndex].value = listbox.options[selIndex + increment].value
    listbox.options[selIndex].text = listbox.options[selIndex + increment].text
 
    listbox.options[selIndex + increment].value = selValue;
    listbox.options[selIndex + increment].text = selText;
 
    listbox.selectedIndex = selIndex + increment;
}

/* Remove Listbox Item */
function listbox_remove(sourceID) {
 
    //get the listbox object from id.
    var src = document.getElementById(sourceID);
  
    //iterate through each option of the listbox
    for(var count= src.options.length-1; count >= 0; count--) {
 
         //if the option is selected, delete the option
        if(src.options[count].selected == true) {
  
                try {
                         src.remove(count, null);
                         
                 } catch(error) {
                         
                         src.remove(count);
                }
        }
    }
}

// ** Select All Listbox
    function listbox_selectall(listID, isSelect) {
        var listbox = document.getElementById(listID);
        for(var count=0; count < listbox.options.length; count++) {
            listbox.options[count].selected = isSelect;
    }
}

function htmlEncode(value){
    if (value) {
        return jQuery('<div />').text(value).html();
    } else {
        return '';
    }
}
 
function htmlDecode(value) {
    if (value) {
        return $('<div />').html(value).text();
    } else {
        return '';
    }
}