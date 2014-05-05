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
	$('a[href^="#"]').on('click',function (e) {
	    e.preventDefault();

	    var target = this.hash,
	    $target = $(target);

	    $('html, body').stop().animate({
	        'scrollTop': $target.offset().top
	    }, 900, 'swing', function () {
	        window.location.hash = target;
	    });
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
    	moreButton = $("a.readmorebtn");

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


	//toggles visibility of quicklinks menu
	/*
	$("#ti_header_help").click(function(){
		$("#ti_header_helpDrop").slideToggle("fast");
	});

  $("#ti_header_cart").click(function(){
    $("#ti_header_cartDrop").slideToggle("fast");
  });

  $("#ti_header_account").click(function(){
    $("#ti_header_accountDrop").slideToggle("fast");
  });
*/
	$("#ti_header_help").click(function(e){
		jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
		if (!$("#ti_header_helpDrop").is(":visible")) 
			$("#ti_header_helpDrop").stop(true, true).slideDown("fast");
		
		e.stopPropagation();
	});

  $("#ti_header_cart").click(function(e){
  	jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
  	if (!$("#ti_header_cartDrop").is(":visible")) 
    $("#ti_header_cartDrop").stop(true, true).slideToggle("fast");
    e.stopPropagation();
  });

  $("#ti_header_account").click(function(e){
  	jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
  	if (!$("#ti_header_accountDrop").is(":visible")) 
    $("#ti_header_accountDrop").stop(true, true).slideToggle("fast");
    e.stopPropagation();
  });

   $("#ti_header_searchHelp").click(function(e){
  	jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
  	if (!$("#ti_header_searchHelpDrop").is(":visible")) 
    $("#ti_header_searchHelpDrop").stop(true, true).slideToggle("fast");
    e.stopPropagation();
  });
	//Toggle coupon code
	jQuery("#ti_main_coupon_arrow").click(function(){
		jQuery("#ti_header_click_coupon").slideToggle("fast", function() {
			//if (jQuery('ti_header_click_coupon').css("display") == "none") alert("close");//
		//else alert("open");//


		if (document.getElementById("ti_header_click_coupon").style.display == "none") jQuery("#ti_main_coupon_arrow").css("background-image", "url('" + ti_header_arrowdown + "')");
		else {jQuery("#ti_main_coupon_arrow").css("background-image", "url('" + ti_header_arrowup + "')");}
		});//url("http://www.866ink.com/magentoEE/images/ti-assets/header-coupon-arrow.png")
		jQuery("#ti_header_coupon_desc").slideToggle("fast");
		
	});



/****************************************/
/* qty number input limit  */
/* Yiyang 5-1-2014         */
/******************************/
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


/***************************************/
/*   Display Small Navigation          */
/* Author: Yiyang     Date: 5/5/2014   */
/***************************************/
var ti_header_smallnav = document.getElementById('ti_header_breadcrumbs');
if (typeof ti_global_pagetype != "undefined" && ti_global_pagetype == "brand") {
  ti_header_smallnav.innerHTML = " Home > " + ti_global_pagename; 
}
else if (typeof ti_global_pagetype != "undefined" && ti_global_pagetype == "grouped") {
  ti_header_smallnav.innerHTML = " Home > " + ti_global_printerbrand + " > " + ti_global_pagename; 
}
else if (typeof ti_global_pagetype != "undefined" && (ti_global_pagetype == "simple" || ti_global_pagetype == "bundle")) {
  var ti_header_sku_arr = ti_global_productcode.split("-");
  if (getUrlVars()["printer"] != undefined)
    {ti_header_smallnav.innerHTML = " Home > " + ti_header_sku_arr[1] + " > " + ti_global_prev_printer + " > " + ti_global_pagename;}
  else 
    ti_header_smallnav.innerHTML = " Home > " + ti_header_sku_arr[1] + " > " + ti_global_pagename;
}








}); //end of document ready






//hide drop content when clicking on anywhere else
jQuery(document).click(function () {
  jQuery(".ti_cms_dropContent:visible").stop(true, true).slideUp("fast");
});