<?php 
class Compandsave_Orderfilter_Model_Observer {
	public function orderfilter(Varien_Event_Observer $observer) {

		$filter_priority = true;

		$order = $observer->getEvent()->getOrder();
		$addressid = $order->getShippingAddress()->getId();
		$shippingaddress = Mage::getModel('sales/order_address')->load($addressid);
		$addressid = $order->getBillingAddress()->getId();
		$billingaddress  = Mage::getModel('sales/order_address')->load($addressid);




/***************************************************/
/* Description: Check Special Customer Order       */
/*  */

/* Yiyang 5/27/2014                               */
/***************************************************/


$fullname = $order->getCustomerName();
$billing_street = $billingaddress->getStreetFull();
$shipping_street = $shippingaddress->getStreetFull();

$telephone = $shippingaddress->getTelephone();
$telephone = filter_var($telephone, FILTER_SANITIZE_NUMBER_INT); //extract int numbers
$telephone = str_replace(array('+','-'), '', $telephone);

$order_text = $fullname . " " . $billingaddress->getCompany() . " " . $shippingaddress->getCompany() . " " . $billing_street . " " . $shipping_street;


//extract special customer/keywords from database: 

$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
$query = "SELECT * FROM `compandsave_functions_conditionfilter`; ";

$readresult=$write->query($query);

	while ($row = $readresult->fetch() ) {
		$conditions_arr = explode("*", $row['Condition']);
		$keys_arr = explode("*", $row['Key']);
		break;
	}
$items = $order->getAllItems();

	foreach ($items as $item) {

		for ($i=0; $i<count($conditions_arr); $i++) {
			if  (($conditions_arr[$i] != "") && (stripos($item->getSku(), $conditions_arr[$i]) !== FALSE)) {
				for ($j=0; $j<count($keys_arr); $j++) {
					if  (($keys_arr[$j] != "") && (stripos($order_text, $keys_arr[$j]) !== FALSE)) {
						$filter_priority=false; 
    					$order->setOrderTypeValue('Condition');
						$order->setOrderType('Special');					
					}
				}
			} 
		}

	}
	




//special customer check
//$write = Mage::getSingleton('core/resource')->getConnection('core_write');
		
$query = "SELECT * FROM `compandsave_functions_customerfilter`; ";
    
    $readresult=$write->query($query);

    while ($row = $readresult->fetch() ) {

    	//data from customer filter table
		$check_fullname = $row['FirstName'] . " " . $row['LastName'];
		$check_telephone = $row['Telephone'];

		$check_telephone = filter_var($check_telephone, FILTER_SANITIZE_NUMBER_INT); //extract int numbers
		$check_telephone = str_replace(array('+','-'), '', $check_telephone);

    	//check customer id
    	if (($row['CustomerID'] != "") && ($order->getCustomerId() == $row['CustomerID'])) {$filter_priority=false; $order->setOrderTypeValue('ID');
		$order->setOrderType('Special'); }
    	//check customername
    	
    	
    	else if (($check_fullname != "") && (similar_text(strtolower($check_fullname), strtolower($fullname)) > 90)) {
    		$filter_priority=false;
    		$order->setOrderTypeValue('Name');
			$order->setOrderType('Special');
    	}
    	//check customer email
    	else if (($row['EmailAddress'] != "") && (strtolower($order->getCustomerEmail()) == strtolower($row['EmailAddress']))) {
    		$filter_priority=false; 
    		$order->setOrderTypeValue('Email');
			$order->setOrderType('Special');
    	} 
    	
    	//if po box, eactly equal
    	else if ((stripos($shipping_street, 'po box') !== FALSE) || (stripos($shipping_street, 'po box') !== FALSE)) {
    		if ((strtolower($shipping_street) == strtolower($row['ShippingAddress'])) || (strtolower($shipping_street) == strtolower($row['BillingAddress'])) || (strtolower($billing_street) == strtolower($row['ShippingAddress'])) || (strtolower($billing_street) == strtolower($row['BillingAddress']))) {
    			$filter_priority=false; 
    			$order->setOrderTypeValue('POBOX');
				$order->setOrderType('Special');
    		}
    	}


    	//check customer shipping address with both of billing address and shipping address
    	else if (( similar_text(strtolower($shipping_street), strtolower($row['ShippingAddress'])) > 90 ) || ( similar_text(strtolower($shipping_street), strtolower($row['BillingAddress'])) > 90 )) {
    		$filter_priority=false; 
    		$order->setOrderTypeValue('ShippingAddress');
			$order->setOrderType('Special');
    	}

    	//check customer billing address with both of billing address and shipping address
    	else if (( similar_text(strtolower($billing_street), strtolower($row['ShippingAddress'])) > 90 ) || ( similar_text(strtolower($billing_street), strtolower($row['BillingAddress'])) > 90 )) {
    		$filter_priority=false; 
    		$order->setOrderTypeValue('BillingAddress');
			$order->setOrderType('Special');
    	}
    	
    	//check telephone
    	else if (($check_telephone != "") && ($check_telephone == $telephone)) {
    		$filter_priority=false; 
    		$order->setOrderTypeValue('Telephone');
			$order->setOrderType('Special');
    	}

	
    	//$customer_text .= "***Customers***" . $row['FirstName'] . "*" . $row['LastName'] . "*" . $row['CustomerID'] . "*" . $row['EmailAddress'] . "*" . $row['Company'] . "*" . $row['ShippingAddress'] . "*" . $row['BillingAddress'] . "*" . $row['Telephone'] . "*" . $row['entity_id'];
    }































/***************************************************/
/* Description: check duplicate order              */
/* If there is any pending order with same address */
/* update this order type: duplicate               */
/* Yiyang 5/19/2014                                */
/***************************************************/


		//$order->setOrderType($address->getStreetFull());

if ($filter_priority && $order->getOrderType() == "") {
 
	//$orderid = "11095";
	//$order = Mage::getModel('sales/order')->load($orderid);
	//$addressid = $order->getShippingAddress()->getId();
	//$address = Mage::getModel('sales/order_address')->load($addressid);
		$street = $shippingaddress->getStreetFull();





	$collection_items = Mage::getModel('sales/order')->getCollection()
	->addAttributeToFilter('status', 'pending');
	->addAttributeToSelect('*');
	//->addAttributeToFilter('status', array('IN'=>array('pending, processing')))




//extract all pending orders with the same address

	$collection_items->getSelect()->join(array('t2' => 'sales_flat_order_address'),'main_table.entity_id = t2.parent_id', array('t2.address_type'))->where("t2.address_type = 'shipping'")->where("t2.street = ?", $street);

//check if there is duplicate
	
	if ( count($collection_items) > 1 ) {

		$count=1;
		
		foreach ($collection_items as $single) {
		
			if ($count==1) {
				if ($single->getOrderTypeValue() != "") $duplicate_id =  $single->getOrderTypeValue();
				else {
					$duplicate_id = $single->getId();
				}
				break;
			}

			//set dupliate order order_type duplicate and order_type_value as one center order

			if (is_null($single->getOrderTypeValue())) $single->setOrderTypeValue($duplicate_id);


			if (is_null($single->getOrderType())) $single->setOrderType('duplicate');
			$single->save();	

			$count++;
		} 
		$order->setOrderTypeValue($duplicate_id);
		$order->setOrderType('duplicate');
		$filter_priority=false;
	}


}





/***************************************************/
/* Description: Order Filter-> USPS/Fedex          */
/*  Check Order Shipping Method */

/* Yiyang ???                                */
/***************************************************/





/***************************************************/
/* Description: Order Filter-> Army Check          */
/*  Check Order Shipping City: APO, DPO, FPO, Guam */
/* Yiyang   Date:5/20/2014                         */
/***************************************************/
$army_city = $shippingaddress->getCity();
$army_state = $shippingaddress->getState();
if (strtoupper($army_city) == 'APO' || strtoupper($army_city) == 'DPO' || strtoupper($army_city) == 'FPO' || strtoupper($army_state) == 'GUAM') {
	$order->setOrderType('Army');
	$filter_priority = false;
}


/***************************************************/
/* Description: Order Filter-> Large Order         */
/*  Check Order Shipping State                     */
/* Yiyang   Date:5/20/2014                         */
/***************************************************/

if ($filter_priority && ($order->getGrandTotal() >= 150) && $order->getOrderType() == '') {
	$shippingstate = strtoupper($shippingaddress->getRegion());
	$order->setOrderType('Large');
	$filter_priority = false;
	if ($shippingstate == "HAWAII" || $shippingstate == "ALASKA" || $shippingstate == "VIRGIN ISLANDS") {
		$order->setOrderTypeValue('Special');
	}
	else {
		$order->setOrderTypeValue('Regular');}
}



/****************************************************/
/* Description: Order Filter-> Small Order&AutoShip */
/*  Check Order Shipping State                      */
/* Yiyang   Date:5/20/2014                          */
/****************************************************/

if ($filter_priority && $order->getGrandTotal() < 150 && $order->getOrderType() == '') 	{
	$shippingstate = strtoupper($shippingaddress->getRegion());
	$orderweight = $order->getWeight();
	if ($orderweight < 0.75) {
		$order->setOrderType('Autoship');
		$order->setOrderTypeValue('Autoship');
	}

	else {
		$order->setOrderType('Small');
			if ($shippingstate == "HAWAII" || $shippingstate == "ALASKA" || $shippingstate == "VIRGIN ISLANDS") {
				$order->setOrderTypeValue('Special');
			}
			else 
				$order->setOrderTypeValue('Regular');

	}

}






		
	} //end of function
} //end of class
?>