<?php 
class Compandsave_Orderfilter_Model_Observer {
	public function orderfilter(Varien_Event_Observer $observer) {

		$order = $observer->getEvent()->getOrder();
		$order->setOrderType('test');
		//echo "??";
	}
}
?>