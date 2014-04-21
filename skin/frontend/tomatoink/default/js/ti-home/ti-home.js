/*****************************************************************/
/*                                                               */
/* Description: Home functions: slider banner                    */
/* Author: Megan Prior-Pfeifer                                   */
/* Version: 0.0.1                                                */
/*****************************************************************/
$(document).ready(function(){

	//Slider banner
	$(window).load(function() {
		$('.flexslider').flexslider();
	});



/*****************************************************************/
/*    Below this line belongs on global sheet                    */

	//toggles visibility of support menu
	$("#ti_header_help").click(function(){
		$("#ti_header_support").slideToggle("slow");
	});

	//Toggle coupon code
	$("#ti_main_coupon_arrow").click(function(){
		$("#ti_header_click_coupon").slideToggle("fast");
		$("#ti_header_coupon_desc").slideToggle("fast");
	});

	//Drop down coupon div
	//Source:  http://jsfiddle.net/forresto/ShUgD/
	var el = "#ti_main_coupon_arrow";

    	//Modernizr.csstransitions = false;

    if (Modernizr.csstransitions) {
      $(el).css({
        "transition": "all 500ms ease-in-out"
      });
    }

    var rot = 0;

    $(el).click(function(){
      if (rot===0) {
        rot = 180;
      } else {
        rot = 0;
      }
      if (Modernizr.csstransitions) {
        $(el).css({"transform": "rotate("+rot+"deg)"});
      } else {
        $(el).stop().animate(
          {rotation: rot},
          {
            duration: 500,
            step: function(now, fx) {
              $(this).css({"transform": "rotate("+now+"deg)"});
            }
          }
        );
      }
    });

});
