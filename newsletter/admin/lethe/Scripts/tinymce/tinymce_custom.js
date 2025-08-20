// Custom Settings
var custom_button_style3 = "background: #cb60b3;background: -moz-linear-gradient(top,  #cb60b3 0%, #c146a1 50%, #a80077 51%, #db36a4 100%);background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#cb60b3), color-stop(50%,#c146a1), color-stop(51%,#a80077), color-stop(100%,#db36a4));background: -webkit-linear-gradient(top,  #cb60b3 0%,#c146a1 50%,#a80077 51%,#db36a4 100%);background: -o-linear-gradient(top,  #cb60b3 0%,#c146a1 50%,#a80077 51%,#db36a4 100%);background: -ms-linear-gradient(top,  #cb60b3 0%,#c146a1 50%,#a80077 51%,#db36a4 100%);background: linear-gradient(to bottom,  #cb60b3 0%,#c146a1 50%,#a80077 51%,#db36a4 100%);filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#cb60b3', endColorstr='#db36a4',GradientType=0 );";
if(customMCEHeight==''){customMCEHeight='400';}else{customMCEHeight=customMCEHeight;}
if(customMCEWidth==''){customMCEWidth='400';}else{customMCEWidth=customMCEWidth;}
if(customButLETHE==true){addLetheButs = ' | letheweb lethemail lethesubscribername letheunscribelink lethesocial';}else{addLetheButs = '';}

tinymce.init({
	selector:'.mceEditor',
	language:customMCEchar,
	relative_urls: false,
	remove_script_host: false,
	menubar:false,
	plugins: 'advlist,textcolor,link,lists,image,charmap,code,table,emoticons,wordcount,fullpage,fullscreen,importcss,autolink,insertdatetime,contextmenu,visualblocks,preview,searchreplace',
	toolbar1: 'formatselect fontselect fontsizeselect forecolor backcolor',
	toolbar2: 'bold italic underline strikethrough alignleft aligncenter alignright alignjustify bullist numlist outdent indent blockquote',
	toolbar3: 'link unlink image | searchreplace charmap hr | table | preview fullscreen code | minipan' + addLetheButs,
	height: customMCEHeight,
	width:customMCEWidth,
	entity_encoding : "raw",
	
	
//this is how you will get your custom menu like in the above image
	
	    setup : function(ed) {
					
			if(customButPAN){
				// Add miniPAN Button
				ed.addButton('minipan', {
				addClass: 'minipan',
				title : 'miniPAN',
				image : 'Scripts/tinymce/skins/lightgray/buttons/artUploader.png',
				style: custom_button_style3,
				onclick : function(e) {
										// Fancybox Example
										$.fancybox({
											autoSize : true,
											type     : 'iframe',
											href     : 'miniPan/index.php?pf='+ ed.id +'&pm=default&pp=tinymce&o=fancybox'
										});
										// ******					
					},
				});
			}

			
    }

});