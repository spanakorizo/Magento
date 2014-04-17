
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





/*****************************************************************/
/*    coupon code slide                    */
/* Megan                                   */
	//toggles visibility of support menu
	jQuery("#ti_header_help").click(function(){
		jQuery("#ti_header_support").slideToggle("slow");
	});

	//Toggle coupon code
	jQuery("#ti_main_coupon_arrow").click(function(){
		jQuery("#ti_header_click_coupon").slideToggle("fast");
		jQuery("#ti_header_coupon_desc").slideToggle("fast");
	});








}); //end of document ready