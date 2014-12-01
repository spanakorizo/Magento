//*************************************************************//
//* Description:  Printer Model Search function                */
//* Author: added by Shuoran Zhang                             */             
//* Version: 0.0.1                                             */
//*************************************************************//

jQuery(document).ready(function(){
	// only for search the printer model, begins
	jQuery('#printer_model_input').keyup(function() {
		if (jQuery('#printer_model_input').val() == "") {
			jQuery('#display-search-results').html("").hide();
		}
		var searchValue = jQuery(this).val().replace(" ", "+");
		if (searchValue != '') {
			//$('#display-search-results').css('display','inline');
			jQuery.ajax({
				type: "POST",
				url: "//secure.compandsave.com/sz/test/autocomp/search_14Nov_Survey.php",
				data: { searchValue: searchValue },
				cache: false,
				success: function(response) { 
					jQuery('#display-search-results').html(response).show();
				}
			})
			.done(function() {
			});
			return false;
		}
	});
	jQuery('.search').blur(function(){
		//$('#display-search-results').html("").hide();
		//alert("aaa");
	});
	// only for search the printer model, ends
});

/*
$('input[name="product-type"]').change(function() {
	if ($('input[name="product-type"]:checked').val() == "no") {
		$('#product_image').attr('src', "/v/newsletter/promotion-june-2014/images/ink-group.jpg");
		$('#product_name').html("Save 25% on your next ink purchase!");
		
	}
	else if ($('input[name="product-type"]:checked').val() == "yes") {
		$('#product_image').attr('src', "<% Response.write(image_url) %>");
		$('#product_name').html("<% Response.write(product_text) %>");
	}
});
*/

//*************************************************************//
//* Description: Survey function                               */
//* Author: added by Shuoran Zhang                             */             
//* Version: 0.0.1                                             */
//*************************************************************//

if (window.location.protocol == "https:") {
	var href = window.location.href;
	href = href.replace('https:', 'http:');
	window.location = href;	
}

function validateEmail(email) { 
    var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(email);
}
function goChangePrinter(fromID) {
	var name = document.getElementById('ti-re-name').value;
	var email = document.getElementById('ti-re-email').value;
	var printer = document.getElementById('val').value;
	var printer1 = document.getElementById('brand').value;
	var printer2 = document.getElementById('series').value;
	var printer3 = document.getElementById('cartridge').value;
	
	if (name=="") {
		document.getElementById('ti_sur_re_alert').innerHTML = "Please type your name";
		return false;
	}
	else if (email=="" || email=="Email" || validateEmail(email)==false) {
		document.getElementById('ti_sur_re_alert').innerHTML = "Please input a valid email."
		return false;
	}
	else if (printer1=="") {
		document.getElementById('ti_sur_re_alert').innerHTML = "Please select a brand";
		return false;
	}
	else if (printer2=="") {
		document.getElementById('ti_sur_re_alert').innerHTML = "Please select a series";
		return false;
	}
	else {
		document.getElementById('ti_sur_re_alert').innerHTML = "";
	}

	name = jQuery.trim( name );
	email = jQuery.trim( email );
	printer = jQuery.trim( printer );
	printer1 = jQuery.trim( printer1 );
	printer2 = jQuery.trim( printer2 );
	printer3 = jQuery.trim( printer3 );
	var post_url = "http://www.casinkadmin.com/shuoranz/20141117_re_Survey/storeForm.php";
	jQuery.browser={};(function(){jQuery.browser.msie=false;
	jQuery.browser.version=0;if(navigator.userAgent.match(/MSIE ([0-9]+)\./)){
	jQuery.browser.msie=true;jQuery.browser.version=RegExp.$1;}})();
	if (jQuery.browser.msie && window.XDomainRequest) {
				// Use Microsoft XDR
				var xdr = new XDomainRequest();
				name = encodeURIComponent(name);
				email = encodeURIComponent(email);
				printer = encodeURIComponent(printer);
				post_url += "?name="+name+"&email="+email+"&printer="+printer+"&fid="+formID+"&printer1="+printer1+"&printer2="+printer2+"&printer3="+printer3;
				xdr.open("GET", post_url);
				xdr.send();
				xdr.onload = function() {
					// XDomainRequest doesn't provide responseXml, so if you need it:
					var dom = new ActiveXObject("Microsoft.XMLDOM");
					dom.async = false;
					window.location = "http://www.tomatoink.com/v/newsletter/2014-11-sur/ti-sur-re.asp?searchprinter=1&printer="+ printer;
					//window.location.assign("http://search.tomatoink.com/search?keywords="+printer)
					/*
					document.getElementById('ti_sur_non_form_display').style.display="none";
					document.getElementById('ti-cms-thankyou').style.display="block";
					document.getElementById('ti-cms-second-search').style.display="block";
					$('html,body').animate({
						scrollTop: 0},
						'slow'); 
					*/
				};
					
	} 
	else {
		jQuery.get(post_url,
				{name:name, email:email, printer:printer, fid:fromID, printer1:printer1, printer2:printer2, printer3:printer3}, 
				function(data){
					window.location = "http://www.tomatoink.com/v/newsletter/2014-11-sur/ti-sur-re.asp?searchprinter=1&printer="+ printer;
					//window.location.assign("http://search.tomatoink.com/search?keywords="+printer)
					/*
					document.getElementById('ti_sur_non_form_display').style.display="none";
					document.getElementById('ti-cms-thankyou').style.display="block";
					document.getElementById('ti-cms-second-search').style.display="block";
					$('html,body').animate({
						scrollTop: 0},
						'slow'); 
					*/
				}
		);
	}

}