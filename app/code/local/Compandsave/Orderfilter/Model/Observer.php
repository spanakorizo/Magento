<?php 
class Compandsave_Orderfilter_Model_Observer {
	public function orderfilter(Varien_Event_Observer $observer) {

		$order = $observer->getEvent()->getOrder();
		$addressid = $order->getShippingAddress()->getId();
		$address = Mage::getModel('sales/order_address')->load($addressid);

/***************************************************/
/* Description: check duplicate order              */
/* If there is any pending order with same address */
/* update this order type: duplicate               */
/* Yiyang 5/19/2014                                */
/***************************************************/


		//$order->setOrderType($address->getStreetFull());


 
	//$orderid = "11095";
	//$order = Mage::getModel('sales/order')->load($orderid);
	//$addressid = $order->getShippingAddress()->getId();
	//$address = Mage::getModel('sales/order_address')->load($addressid);
		$street = $address->getStreetFull();





	$collection_items = Mage::getModel('sales/order')->getCollection()
	->addAttributeToFilter('status', 'pending')
	->addAttributeToSelect('*');




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

			//if (is_null($single->getOrderTypeValue())) $single->setOrderTypeValue($duplicate_id);


			//if (is_null($single->getOrderType())) $single->setOrderType('duplicate');
			//$single->save();	

			$count++;
		} 
		$order->setOrderTypeValue($duplicate_id);
		$order->setOrderType('duplicate');
	}







		
	} //end of function
} //end of class
?>