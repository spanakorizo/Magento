/*********************************************/
/*  Function for add to cart and show popup */
/*****   Added by : Zahed               ****/
/*****  Date : 16 June 2014           *****/
/*****************************************/

function add_to_box(id1,id2){

    var firstId = jQuery('#' + id1 );
    var secondId = jQuery('#' + id2 );
    //var secondId = jQuery('#' + id2 );
    var screenTop = jQuery(document).scrollTop();
    var Top = (screen.height / 3);
    Top = screenTop + Top;
    screenTop = screenTop + 50;
	var  url = firstId.attr('action');
    var data = firstId.serialize();
    var qty_check = false;
    jQuery('.qty').each(function() {
      if (jQuery(this).val() != '') qty_check = true;
    });
	if (qty_check) {
		jQuery('#imageLoading img').hide();
		jQuery('#ajax_loader').show();

		try {
			jQuery.ajax({
				url: url,
				dataType: 'text',
				type : 'post',
				data: data,
				success: function(text){



					jQuery.post( ti_global_url + 'productselector/popupcart/header',function(response){
						var obj = response.evalJSON();
						jQuery('#ti_header_cartcount').html(obj.totalnumber);
            jQuery('#ti_popup_cart_num').html(obj.totalnumber);
						jQuery('#ti_header_cartDrop').html(obj.alltext);
            if (obj.totalitem > 1) {
              jQuery('#ti_header_cartDrop').contentcarouselhd();
              jQuery('.ti_miniCart_carousel-nav').show();
            }


                    //clean qty, show pop up cart, hide loading image
          jQuery('#show_cart_content').html(text);
          secondId.css('top', screenTop);
          secondId.show("fast");
          jQuery('#ti_hide_body_div').show();
          jQuery('.qty').val("");
          jQuery('#ajax_loader').hide();


          //pop up cart
          jQuery('#ti_continue_shopping').click(function(e){
            e.preventDefault();
            jQuery('#show_cart').hide();
            jQuery('#ti_hide_body_div').hide();
            e.stopPropagation();
           });

           //pop up cart
          jQuery('#ti-popupcart-close').click(function(e){
            e.preventDefault();
            jQuery('#show_cart').hide();
            jQuery('#ti_hide_body_div').hide();
            e.stopPropagation();
           });

					});
					//jQuery.post( ti_global_url + 'productselector/popupcart/index',function(data){
					//	secondId.html(data);
					//	secondId.css('top', screenTop);
					//	secondId.show("fast");
					//	jQuery('#ti_hide_body_div').show();
					//	jQuery('.qty').val("");
          //  jQuery('#ajax_loader').hide();
					//});

				}, 
        error: function(XMLHttpRequest, textStatus, errorThrown) {
                  //alert(textStatus);
                  console.log(errorThrown);
              }

			});

		} 
		catch (e) {
		
		}
	}
	else{
			
		secondId.html("<h2 style='padding:15px 5px'><strong class='altTxt' style='padding-left:10px'>Please select at least one product</strong></h2><span class='ti_close_popup'><a href='#' class='ti_checkoutStep_close altTxt' onclick =' hide_pop_up() '>X</a></span>");
		secondId.show("slow");
		jQuery('#ti_hide_body_div').show();
		secondId.css('top', Top);
			
	}
    
    //jQuery('#ti_group_multiple_msg').show();

}
function hide_pop_up(){
    jQuery('#ti_hide_body_div').hide();
    jQuery('#show_cart').hide("slow");
}
/************* function for auto qty set to 1 if not set ******/
function addSimpleToCart( id ) {
	
    var ti = jQuery('#' + id );
	var qty = ti.val();
    
	if ( qty == '' || qty == '0') ti.val(1);
    
}
/******************* function for get parms from url **********/
function getParameterByName(name) {
    name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
    var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
        results = regex.exec(location.search);
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}


/***************function validate Email ***************/
function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
} 
/*******************************************************/
/* */
/* Description: URL, COOKIE FUNCTIONS*/
/* Author: Yiyang */
/* Version: 0.0.1 */
/*******************************************************/

function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}


function setCookie(c_name,value,exdays)
{
var exdate=new Date();
exdate.setDate(exdate.getDate() + exdays);
var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
document.cookie=c_name + "=" + c_value + "; path=/";
}


function getCookie(c_name)
{
var i,x,y,ARRcookies=document.cookie.split(";");
for (i=0;i<ARRcookies.length;i++)
{
  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  x=x.replace(/^\s+|\s+$/g,"");
  if (x==c_name)
    {
    return unescape(y);
    }
  }
}

//var sasref = getUrlVars()["ref"];

/*******************************************************/
/* */
/* Description: Smooth Scroll for TI*/
/* makes scrolling to anchor links smooth */
/* Author: Megan Prior-Pfeifer, reference from http://www.paulund.co.uk/smooth-scroll-to-internal-links-with-jQuery */
/* Version: 0.0.1 */
/*******************************************************/

jQuery(document).ready(function($){



	
	jQuery(document).keyup(function(e) {
        e.preventDefault();
        if (e.keyCode == 27) { // esc keycode
            jQuery('#ti_hide_body_div').hide();
            jQuery('#show_cart').hide("slow");
        }
    });
    //code for top link
    // hide #ti_back_top first
    jQuery("#ti_back_top").hide();

    // fade in #ti_back_top
    jQuery(function () {
        jQuery(window).scroll(function () {
            if (jQuery(this).scrollTop() > 100) {
                jQuery('#ti_back_top').fadeIn();
            } else {
                jQuery('#ti_back_top').fadeOut();
            }
        });
        jQuery('#ti_back_top a').bind("mouseover", function(){
            var color  = jQuery('#ti_back_top').css("background-color");

            jQuery('#ti_back_top').css("background", "rgba(113, 182, 47,1)");

            jQuery(this).bind("mouseout", function(){
                jQuery('#ti_back_top').css("background", color);
            })
        })
        // scroll body to 0px on click
        jQuery('#ti_back_top a').click(function () {
            jQuery('body,html').animate({
                scrollTop: 0
            }, 800);
            return false;
        });
    });
	jQuery( '.ti_cms_home_block' ).mouseenter(function() {
		//jQuery(this).children('.ti-hide').fadeTo('slow', 0.3, function(){
			jQuery(this).children('.ti-more').siblings().animate({opacity: "0.2"},"slow");
			jQuery(this).children('.ti-more').show();
			jQuery(this).children('.ti-more').animate({opacity: "1.0"},"slow");
			//jQuery(this).children('.ti-more').show();
			//jQuery(this).children('.ti-more').css("opacity", 1).siblings().css("opacity", 0.3);
			
			
		//});
		
	});
	jQuery( '.ti_cms_home_block' ).mouseleave(function() {
		//jQuery(this).fadeTo('fast', 1, function(){
			jQuery(this).children('.ti-more').siblings().animate({opacity: "1.0"},"slow");
			jQuery(this).children('.ti-more').hide();
			
			//jQuery(this).children('.ti-more').css("opacity", 1).siblings().css("opacity", 1.0);
		//});
		//jQuery( this ).toggle( "highlight" );
	});
	


/*****************************************************************/
/*                                                               */
/* Description: JS for article pages                             */
/* Expand text sections on site with more/less text              */
/* Re: http://jsfiddle.net/gDvyR/121/                            */
/* Author: TomatoInk.com                                         */
/* Version: 0.0.1                                                */
/*****************************************************************/


	var moreText = "Read more",
    	lessText = "Read less",
    	moreButton = $(".readmorebtn");

	moreButton.click(function () {
    	var $this = $(this);
    	$this.text($this.text() == moreText ? lessText : moreText).nextAll(".more").slideToggle("fast");
	});
//change the word on readmorebtn
function change_readmore(_more, _less) {
	moreText = _more;
	lessText = _less;
}





/*************************************/
/*  coupon code slide  & top links   */
/* Megan                             */
/*************************************/

    var ti_header_arrowup = ti_global_url + "skin/frontend/tomatoink/default/images/ti-assets/header-coupon-arrow.png"; 
    var ti_header_arrowdown = ti_global_url + "skin/frontend/tomatoink/default/images/ti-assets/header-coupon-arrow-2.png"; 

/***********************************************************************/
/*  top links/coupon code/search help/need more slidetoggle function   */
/*    Author: Yiyang     5-9-2014                                      */
/***********************************************************************/
  $(".ti_cms_dropContent").click(function(e) {
    e.stopPropagation();
  });


	$("#ti_header_help").click(function(e){
		jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
		if (!$("#ti_header_helpDrop").is(":visible")) 
			$("#ti_header_helpDrop").stop(true, true).slideToggle("fast");



//    jQuery(".ti_cms_dropContent:visible").hide();
//if (!$("#ti_header_helpDrop").is(":visible"))  
//    $("#ti_header_helpDrop").show();

		coupon_slideback();
		e.stopPropagation();
	});

  $("#ti_header_cart").click(function(e){
  	jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
  	if (!$("#ti_header_cartDrop").is(":visible")) 
    $("#ti_header_cartDrop").stop(true, true).slideToggle("fast");
  //  jQuery(".ti_cms_dropContent:visible").hide();
 //if (!$("#ti_header_cartDrop").is(":visible")) 
   //   $("#ti_header_cartDrop").show();

  coupon_slideback();
    e.stopPropagation();
  });

  $("#ti_header_account").click(function(e){
  	jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
  	if (!$("#ti_header_accountDrop").is(":visible")) 
    $("#ti_header_accountDrop").stop(true, true).slideToggle("fast");

    //jQuery(".ti_cms_dropContent:visible").hide();
   //if (!$("#ti_header_accountDrop").is(":visible"))
   //   $("#ti_header_accountDrop").show();
    coupon_slideback();
    e.stopPropagation();
  });

	//Toggle coupon code
	jQuery("#ti_main_coupon_arrow, #ti_main_coupon").click(function(e){
  jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
	jQuery("#ti_header_click_coupon").slideToggle("fast", function() {
			//if (jQuery('ti_header_click_coupon').css("display") == "none") alert("close");//
		//else alert("open");//


		if (document.getElementById("ti_header_click_coupon").style.display == "none") jQuery("#ti_main_coupon_arrow").css("background-image", "url('" + ti_header_arrowdown + "')");
		else {jQuery("#ti_main_coupon_arrow").css("background-image", "url('" + ti_header_arrowup + "')");}
		});//url("http://www.866ink.com/magentoEE/images/ti-assets/header-coupon-arrow.png")
		jQuery("#ti_header_coupon_desc").slideToggle("fast");
		e.stopPropagation();
	});


//hide drop content when clicking on anywhere else
jQuery(document).click(function (e) {

  jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
  if (e.target.id != "ti_header_coupon_code")
  coupon_slideback();

});



/****************************************/
/******   small shopping cart   *******/
/* Yiyang 9-16-2014         */
/******************************/
//jQuery('#ti_header_cartDrop').contentcarousel();

 //$("#owl-demo").owlCarousel({
 
//autoPlay: 3000, //Set AutoPlay to 3 seconds
 
//items : 4,
//itemsDesktop : [1199,3],
//itemsDesktopSmall : [979,3]
 
//});
/****************************************/
/* qty number input limit  */
/* Yiyang 5-1-2014         */
/******************************/
/*
//click qty input -> show empty
jQuery(".qty").focus(function() {
  if (jQuery(this).val() == 0) jQuery(this).val('');
});
//click other input-> show zero
jQuery(".qty").blur(function() {
  if (jQuery(this).val() == '') jQuery(this).val('0');
});
//input char, input-> show empty
var ti_qty_previous="";
  jQuery(".qty").keypress(function() {
    ti_qty_previous = jQuery(this).val();
    if (ti_qty_previous.match(/\D/)) ti_qty_previous = "";
  });

  jQuery(".qty").keyup(function() {
    if (jQuery(this).val().match(/\D/)) jQuery(this).val(ti_qty_previous);
  });
*/

/***************************************/
/*   Display Small Navigation          */
/* Author: Yiyang     Date: 5/5/2014   */
/* Version 2: clickable     5/9/2014   */
/***************************************/

var ti_breadcrumb_home = "<p><a href='" + ti_global_url + "'>Home</a> &gt; "

var ti_header_smallnav = document.getElementById('ti_header_breadcrumbs');
if (typeof ti_global_pagetype != "undefined" && ti_global_pagetype == "brand") {
  ti_header_smallnav.innerHTML = ti_breadcrumb_home + ti_global_pagename + "</p> "; 
}
else if (typeof ti_global_pagetype != "undefined" && ti_global_pagetype == "grouped") {
  ti_header_smallnav.innerHTML = ti_breadcrumb_home + "<a href='" + ti_global_url + ti_global_printerbrand_url + ".html'>" + ti_global_printerbrand + "</a> &gt; " + ti_global_pagename + "</p> "; 
}
else if (typeof ti_global_pagetype != "undefined" && (ti_global_pagetype == "simple" || ti_global_pagetype == "bundle")) {
  var ti_header_sku_arr = ti_global_productcode.split("-");
  if (getUrlVars()["printer"] != undefined)
    {ti_header_smallnav.innerHTML = ti_breadcrumb_home + "<a href='" + ti_global_url + ti_global_printerbrand_url + ".html'>" + ti_header_sku_arr[1] + "</a> &gt; <a href='" + ti_global_url + "catalog/product/view/id/" + getUrlVars()["printer"] + "'>" + ti_global_prev_printer + "</a> &gt; " + ti_global_pagename + "</p> ";}
  else 
    ti_header_smallnav.innerHTML = ti_breadcrumb_home + "<a href='" + ti_global_url + ti_global_printerbrand_url + ".html'>" + ti_header_sku_arr[1] + "</a> &gt; " + ti_global_pagename + "</p> ";
}




/*******************************************************/
/* */
/* Description: Flex box fallback*/
/* Author: Megan */
/* Version: 0.0.1 */
/* Found at: http://osvaldas.info/flexbox-based-responsive-equal-height-blocks-with-javascript-fallback */
/*******************************************************/

;( function( $, window, document, undefined )
{
    'use strict';
 
    var s = document.body || document.documentElement, s = s.style;
    if( s.webkitFlexWrap == '' || s.msFlexWrap == '' || s.flexWrap == '' ) return true;
 
    var $list       = $( '.ti_cms_flex_gridContain' ),
        $items      = $list.find( '.ti_cms_flex_grid' ),
        setHeights  = function()
        {
            $items.css( 'height', 'auto' );
 
            var perRow = Math.floor( $list.width() / $items.width() );
            if( perRow == null || perRow < 2 ) return true;
 
            for( var i = 0, j = $items.length; i < j; i += perRow )
            {
                var maxHeight   = 0,
                    $row        = $items.slice( i, i + perRow );
 
                $row.each( function()
                {
                    var itemHeight = parseInt( $( this ).outerHeight() );
                    if ( itemHeight > maxHeight ) maxHeight = itemHeight;
                });
                $row.css( 'height', maxHeight );
            }
        };
 
    setHeights();
    $( window ).on( 'resize', setHeights );
    $list.find( 'img' ).on( 'load', setHeights );
 
})( jQuery, window, document );






}); //end of document ready





function coupon_slideback() {
  var ti_header_arrowup = ti_global_url + "skin/frontend/tomatoink/default/images/ti-assets/header-coupon-arrow.png"; 
      if (document.getElementById("ti_header_click_coupon").style.display == "none") {
  jQuery("#ti_header_click_coupon").slideDown("fast");
  jQuery("#ti_header_coupon_desc").slideUp("fast");
  jQuery("#ti_main_coupon_arrow").css("background-image", "url('" + ti_header_arrowup + "')");
  }
}

function checout_url(){
	window.location.href = ti_global_url + 'index.php/firecheckout/';
}

//**************************************************************************//
//* Description:                                                            */
//* Author: added by Megan Prior-Pfeifer                                    */ 
//* http://www.caincode.com/html5-placeholder-fallback-crappy-old-browsers/ */            
//* Version: 0.0.1                                                          */
//**************************************************************************//

/* Modernizr 2.8.3 (Custom Build) | MIT & BSD
 * Build: http://modernizr.com/download/#-input
 */
;window.Modernizr=function(a,b,c){function t(a){i.cssText=a}function u(a,b){return t(prefixes.join(a+";")+(b||""))}function v(a,b){return typeof a===b}function w(a,b){return!!~(""+a).indexOf(b)}function x(a,b,d){for(var e in a){var f=b[a[e]];if(f!==c)return d===!1?a[e]:v(f,"function")?f.bind(d||b):f}return!1}function y(){e.input=function(c){for(var d=0,e=c.length;d<e;d++)n[c[d]]=c[d]in j;return n.list&&(n.list=!!b.createElement("datalist")&&!!a.HTMLDataListElement),n}("autocomplete autofocus list placeholder max min multiple pattern required step".split(" "))}var d="2.8.3",e={},f=b.documentElement,g="modernizr",h=b.createElement(g),i=h.style,j=b.createElement("input"),k={}.toString,l={},m={},n={},o=[],p=o.slice,q,r={}.hasOwnProperty,s;!v(r,"undefined")&&!v(r.call,"undefined")?s=function(a,b){return r.call(a,b)}:s=function(a,b){return b in a&&v(a.constructor.prototype[b],"undefined")},Function.prototype.bind||(Function.prototype.bind=function(b){var c=this;if(typeof c!="function")throw new TypeError;var d=p.call(arguments,1),e=function(){if(this instanceof e){var a=function(){};a.prototype=c.prototype;var f=new a,g=c.apply(f,d.concat(p.call(arguments)));return Object(g)===g?g:f}return c.apply(b,d.concat(p.call(arguments)))};return e});for(var z in l)s(l,z)&&(q=z.toLowerCase(),e[q]=l[z](),o.push((e[q]?"":"no-")+q));return e.input||y(),e.addTest=function(a,b){if(typeof a=="object")for(var d in a)s(a,d)&&e.addTest(d,a[d]);else{a=a.toLowerCase();if(e[a]!==c)return e;b=typeof b=="function"?b():b,typeof enableClasses!="undefined"&&enableClasses&&(f.className+=" "+(b?"":"no-")+a),e[a]=b}return e},t(""),h=j=null,e._version=d,e}(this,this.document);

jQuery( document ).ready(function($) {
// If placeholder isn't supported.
if (!Modernizr.input.placeholder) {
    // For every element that has a placeholder attribute
    $('[placeholder]').each(function() {
        var $this = $(this),
            placeholderValue = $this.attr('placeholder'); // Save the value of the placeholder for later
 
        if ($this.val() == '') { // if field is empty, put the placeholder in it
            $this.val( placeholderValue );
            $this.addClass('ti-cms-hasPlaceholderText');
        }
        // Add/remove placeholder on focus/blur
        $this.focus(function() {
            // Hide the placeholder so the user can enter their own text
            if ($this.val() == placeholderValue) {
                $this.val('');
                $this.removeClass('ti-cms-hasPlaceholderText');
            }
        }).blur(function() {
      // If the user didn't enter any text, show the placeholder text again.
            if ($this.val() == '' || $this.val() == placeholderValue) {
                $this.val(placeholderValue);
                $this.addClass('ti-cms-hasPlaceholderText');
            }
        });
 
        // If the user submits the form, remove the placeholder if it is still there.
        $this.closest('form').submit(function() {
            if ($this.val() == $this.attr('placeholder')) {
                $this.val('');
            }
        });
    });
}

});

