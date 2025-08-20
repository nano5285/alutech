jQuery(function($) {
	
	//Content carousel - naslovnica
	$(function(){
		$('#main-slider.carousel').carousel({
			interval: 7000,
			pause: false
		});
	});
	$(function() {	

	// fancybox
	$("a.fancybox").fancybox({
		'speedIn'		:	600, 
		'speedOut'		:	200, 
		'overlayShow'	:	false,
		'centerOnScroll':	true
	});
});
	//Anchor linkovi + offset zbog header-a
	$('[data-toggle="anchor-elementi"]').bind('click', function(e) {
		e.preventDefault();
		var target = $(this).attr("href");
		var top_offset = 160;
			if ( $('.site-header').css('position') == 'fixed' ) {
			top_offset = $('.site-header').height();
		}
    
	$('html, body').stop().animate({ scrollTop: $(target).offset().top - top_offset }, 1500, function() {
	});
		return false;
	});
	
	//Anchor link NaVrh
	$('.naVrh').click(function(){
		$('html, body').animate({scrollTop : 0},800);
		return false;
	});

	//Header pozadina na scroll
	$(window).scroll(function() {
		if($(this).scrollTop() > 20) {
			$('#header').addClass('opaque');
		} else {
			$('#header').removeClass('opaque');
		}
	});
	
	//Scrollspy
	$('[data-spy="scroll"]').each(function () {
		var $spy = $(this).scrollspy('refresh')
	})

	//Novosti matematickom funkcijom
    size_li = $(".row .col-md-6").size();
    x=6;
    $('.row .col-md-6:lt('+x+')').show();
    $('.vise-vijesti').click(function () {
        x= (x+2 <= size_li) ? x+2 : size_li;
        $('.row .col-md-6:lt('+x+')').show();
         $('.manje-vijesti').show();
        if(x == size_li){
            $('.vise-vijesti').hide();
        }
    });
    $('.manje-vijesti').click(function () {
        x=(x-2<0) ? 2 : x-2;
        $('.row .col-md-6').not(':lt('+x+')').hide();
        $('.vise-vijesti').show();
         $('.manje-vijesti').show();
        if(x == 2){
            $('.manje-vijesti').hide();
        }
    });
	
	// Animinirani logo znak
	function loop() {
		$('.logo-znak').animate({'top': '30'}, 1000)
			.delay(1000)
			.animate({top: 0}, 1000, function() {
				setTimeout(loop, 1000);
		});
	}
	loop();

	$('.trigger').click(function(){
	//get collapse content selector
	var collapse_content_selector = $(this).attr('href');
	//make the collapse content to be shown or hide
	var toggle_switch = $(this);
		$(collapse_content_selector).toggle(function(){
			if($(this).css('display')=='none'){
		//change the button label to be 'Show'
			toggle_switch.html(' (+) ');
			}else{
		//change the button label to be 'Hide'
			toggle_switch.html(' (-) ');
			}
		});
	});

});

/*!
 * Fokus 0.5
 * http://lab.hakim.se/fokus
 * MIT licensed
 *
 * Copyright (C) 2012 Hakim El Hattab, http://hakim.se
 */
(function(){function u(){var m=s();l.clearRect(0,0,a.width,a.height);l.fillStyle="rgba( 0, 0, 0, "+d+" )";l.fillRect(0,0,a.width,a.height);m&&(0.1>d?b=h:(b.left+=0.18*(h.left-b.left),b.top+=0.18*(h.top-b.top),b.right+=0.18*(h.right-b.right),b.bottom+=0.18*(h.bottom-b.bottom)));l.clearRect(b.left-window.scrollX-q,b.top-window.scrollY-q,b.right-b.left+2*q,b.bottom-b.top+2*q);d=m?d+0.08*(B-d):Math.max(0.85*d-0.02,0);cancelAnimationFrame(v);m||0<d?(a.parentNode||document.body.appendChild(a),v=requestAnimationFrame(u)):
document.body.removeChild(a)}function n(m){var a,e,k={left:Number.MAX_VALUE,top:Number.MAX_VALUE,right:0,bottom:0},g;a:{if(window.getSelection&&(g=window.getSelection(),!g.isCollapsed)){g=g.getRangeAt(0);var c=g.startContainer,r=g.endContainer;if(c==r)g="#text"===c.nodeName?[c.parentNode]:[c];else{for(var f=[];c&&c!=r;)f.push(c=C(c));for(c=g.startContainer;c&&c!=g.commonAncestorContainer;)f.unshift(c),c=c.parentNode;g=f}break a}g=[]}c=0;for(r=g.length;c<r;c++){f=g[c];"#text"===f.nodeName&&f.nodeValue.trim()&&
(f=f.parentNode);a=f;e=document.documentElement.offsetLeft;var d=document.documentElement.offsetTop;if(a.offsetParent){do e+=a.offsetLeft,d+=a.offsetTop;while(a=a.offsetParent)}a=e;e=d;d=a;a=e;e=f.offsetWidth;var l=f.offsetHeight;f&&("number"===typeof d&&"number"===typeof e&&(0<e||0<l)&&!f.nodeName.match(/^br$/gi))&&(k.left=Math.min(k.left,d),k.top=Math.min(k.top,a),k.right=Math.max(k.right,d+e),k.bottom=Math.max(k.bottom,a+l))}if(!t||"none"===t||p[t]||s())h=k;m&&(b=h);s()&&u()}function s(){return h.left<
h.right&&h.top<h.bottom}function D(a){3!==a.which&&(document.addEventListener("mousemove",w,!1),document.addEventListener("mouseup",x,!1),n())}function w(a){n()}function x(a){document.removeEventListener("mousemove",w,!1);document.removeEventListener("mouseup",x,!1);setTimeout(n,1)}function y(a){p.alt=a.altKey||a.altGraphKey;p.ctrl=a.ctrlKey;p.shift=a.shiftKey;p.meta=a.metaKey;n()}function z(a){n(!0)}function A(b){a.width=window.innerWidth;a.height=window.innerHeight}function C(a){if(a.hasChildNodes())return a.firstChild;
for(;a&&!a.nextSibling;)a=a.parentNode;return a?a.nextSibling:null}var q=5,B=0.75,t=null,a,l,d=0,v,h={left:0,top:0,right:0,bottom:0},b={left:0,top:0,right:0,bottom:0},p={ctrl:!1,shift:!1,alt:!1,cmd:!1};(function(){for(var a=0,b=["ms","moz","webkit","o"],e=0;e<b.length&&!window.requestAnimationFrame;++e)window.requestAnimationFrame=window[b[e]+"RequestAnimationFrame"],window.cancelAnimationFrame=window[b[e]+"CancelAnimationFrame"]||window[b[e]+"CancelRequestAnimationFrame"];window.requestAnimationFrame||
(window.requestAnimationFrame=function(b,e){var c=(new Date).getTime(),d=Math.max(0,16-(c-a)),f=window.setTimeout(function(){b(c+d)},d);a=c+d;return f});window.cancelAnimationFrame||(window.cancelAnimationFrame=function(a){clearTimeout(a)})})();"addEventListener"in document&&"pointerEvents"in document.body.style&&!window.__fokused&&(window.__fokused=!0,a=document.createElement("canvas"),l=a.getContext("2d"),a.style.position="fixed",a.style.left=0,a.style.top=0,a.style.zIndex=2147483647,a.style.pointerEvents=
"none",a.style.background="transparent",document.addEventListener("mousedown",D,!1),document.addEventListener("keyup",y,!1),document.addEventListener("keydown",y,!1),document.addEventListener("scroll",z,!1),document.addEventListener("DOMMouseScroll",z,!1),window.addEventListener("resize",A,!1),A())})();

$(function() {	
	// Kontakt forma
	$("input[name='secure_code']").val('siteform').hide();
});