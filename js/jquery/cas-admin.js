jQuery('document').ready(function() {

if (window.location.href.match("/sales_order/index/")) {
	//create div
	var admin_order_filter = document.createElement('div');
	admin_order_filter.id = "admin_order_filter";
	admin_order_filter.innerHTML = "<br><input type='button' class='form-button' value='Auto Ship' id='order_filter_autoship'><br>";
	admin_order_filter.innerHTML += "<br><input type='button' class='form-button' value='Small Order' id='order_filter_small'><br>";
	admin_order_filter.innerHTML += "<br><input type='button' class='form-button' value='Large Order' id='order_filter_large'><br>";
	admin_order_filter.innerHTML += "<br><input type='button' class='form-button' value='Ready To Ship' id='order_filter_readytoship'><br>";
//position,fixed top,300px; 
	admin_order_filter.style = "display:none;width:100px; height:500px; background-color:#669;position:absolute;";
	var admin_filter_block  = document.createElement('div');
	var admin_filter_header = document.createElement('div');
	admin_filter_header.innerHTML = "<input type='button' class='form-button' value='Order Filter'>";
	admin_filter_header.style = "margin-top:10px;";

	admin_filter_block.id = "admin_filter_block";
	//admin_filter_block.className = "form-button";
	//admin_filter_block.style = "position:fixed;top:300px";
	admin_filter_block.appendChild(admin_filter_header);
	admin_filter_block.appendChild(admin_order_filter);




	//document.getElementById('messages').appendChild(admin_filter_block);
	

	//batch button

	var admin_batch_button = document.createElement('input');
	admin_batch_button.id = "admin_batch";
	admin_batch_button.type = "button";
	admin_batch_button.value = "Create Batch";
	admin_batch_button.size = "16";
	admin_batch_button.className = "form-button";
	admin_batch_button.style = "margin-top:10px;"


	//document.getElementById('messages').appendChild(admin_batch_button);

	var elements = document.getElementsByClassName('pager');
	elements[0].appendChild(admin_filter_block);
	elements[0].appendChild(admin_batch_button);



	jQuery('#admin_filter_block').click(function() {
		jQuery("#admin_order_filter").toggle();
	});

//sales_order_grid_filter_status

	jQuery('#order_filter_small').click(function() {
		jQuery('#sales_order_grid_filter_order_type').val("Small");
		jQuery('#sales_order_grid_filter_status option[value="processing"]').attr("selected", "selected");
		sales_order_gridJsObject.doFilter();
	});

	jQuery('#order_filter_autoship').click(function() {
		jQuery('#sales_order_grid_filter_order_type').val("Autoship");
		jQuery('#sales_order_grid_filter_status option[value="processing"]').attr("selected", "selected");
		sales_order_gridJsObject.doFilter();
	});

	jQuery('#order_filter_large').click(function() {
		jQuery('#sales_order_grid_filter_order_type').val("Large");
		jQuery('#sales_order_grid_filter_status option[value="processing"]').attr("selected", "selected");
		sales_order_gridJsObject.doFilter();
	});
	//order_filter_readytoship
	jQuery('#order_filter_readytoship').click(function() {
		//jQuery('#sales_order_grid_filter_order_type').val("Large");
		jQuery('#sales_order_grid_filter_status option[value="readytoship"]').attr("selected", "selected");
		sales_order_gridJsObject.doFilter();
	});

//$("#"+divname).show("slide", { direction: "left" }, 1000);

} //end if


//start single order print button
	if (window.location.href.match("/admin/sales_order/view/order_id/")) {
		jQuery('#order_print').show();

	} //end of order print


	jQuery('#admin_batch').click(function() {
		var batch_orders = "";
		var batch_count = 0;
		jQuery('.massaction-checkbox:checked').each(function() {
			batch_orders += jQuery(this).val() + ",";
			batch_count++;

		});

		if (batch_orders == "") {alert("Not Select Any Order!");}
		else {
			if (confirm("You've selected " + batch_count + " orders, continue? ")) {
				jQuery.get(batch_url, {orders: batch_orders}, function(data) {
					alert(data);
					location.reload();
				});
			}


		}


	});



});

