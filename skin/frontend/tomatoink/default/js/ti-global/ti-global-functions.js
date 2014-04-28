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
	$("#ti_header_help").hover(function(){
		$("#ti_header_helpDrop").slideToggle("fast");
	});

  $("#ti_header_cart").hover(function(){
    $("#ti_header_cartDrop").slideToggle("fast");
  });

  $("#ti_header_account").hover(function(){
    $("#ti_header_accountDrop").slideToggle("fast");
  });
	//Toggle coupon code
	jQuery("#ti_main_coupon_arrow").click(function(){
		jQuery("#ti_header_click_coupon").slideToggle("fast", function() {
			//if (jQuery('ti_header_click_coupon').css("display") == "none") alert("close");//
		//else alert("open");//

		if (document.getElementById("ti_header_click_coupon").style.display == "none") jQuery("#ti_main_coupon_arrow").css("background-image", "url('../skin/frontend/tomatoink/default/images/ti-assets/header-coupon-arrow-2.png')");
		else {jQuery("#ti_main_coupon_arrow").css("background-image", "url('../skin/frontend/tomatoink/default/images/ti-assets/header-coupon-arrow.png')");}
		});//url("http://www.866ink.com/magentoEE/images/ti-assets/header-coupon-arrow.png")
		jQuery("#ti_header_coupon_desc").slideToggle("fast");
		
	});



}); //end of document ready