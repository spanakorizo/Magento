/*****************************************************************/
/*                                                               */
/* Description: JS for hover images                              */
/* Author: Yiyang     Version: 0.0.2    11/24/2014               */
/*****************************************************************/

jQuery(document).ready(function() {



//$( '#indexbox_1' ).scrollFollow({offset: 30, container: 'container'});
//$( '#indexbox_2' ).scrollFollow({offset: 30});

jQuery('.cas-cms-pictureFirst').hover(function() {
  var scroll_y = MouseWheelHandler();

  if (scroll_y < 350) jQuery(".span_img").css("margin-top", "-600px");
  else if (scroll_y >=350 && scroll_y <750) jQuery(".span_img").css("margin-top", "-200px");
  else if (scroll_y >= 750 && scroll_y < 850) jQuery(".span_img").css("margin-top", "50px");
  else jQuery(".span_img").css("margin-top", "0px");

});


});


function MouseWheelHandler(e) {

    var scroll_y = 0;
    if(typeof pageYOffset!= 'undefined'){
        //most browsers except IE before #9
        scroll_y = window.pageYOffset;
    }
    else{
        var B= document.body; //IE 'quirks'
        var D= document.documentElement; //IE with doctype
        D= (D.clientHeight)? D: B;
        scroll_y = D.scrollTop;        
    }

  return scroll_y;
}