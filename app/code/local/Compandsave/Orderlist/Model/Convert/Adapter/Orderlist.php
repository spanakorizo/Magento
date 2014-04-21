<?php
ini_set('max_execution_time', 180000);
class Compandsave_Orderlist_Model_Convert_Adapter_Orderlist
    extends Mage_Eav_Model_Convert_Adapter_Entity
{
 	
    protected $_stores;
	protected $_first_name = '';
	protected $_last_name = '';
	protected $old_order_id = '';
	protected $_emailAddress = '';
	protected $_customer_id = 0;
	
    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            //print '<pre>'.memory_get_usage().'</pre>';
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    public function saveRow(array $importData)
    {
        
       	if (empty($importData['Store'])) {
            if (!is_null($this->getBatchParams('Store'))) {
                $store = $this->getStoreById($this->getBatchParams('Store'));
            } 
        } else {
            $store = $this->getStoreByCode($importData['Store']);
        }
		//============================ GET CUSTOMER INFORMATION ============================//
		$this->old_order_id = trim($importData['orderid']);
		$customer_collection = Mage::getResourceModel('customer/customer_collection')->addAttributeToFilter('customerid',$importData['customerid'])->getFirstItem()->load();
		if($customer_collection != ''){
			$this->_customer_id = $customer_collection->getId();
			$this->_first_name = $customer_collection->getFirstname();
			$this->_last_name = $customer_collection->getLastname();
			$this->_emailAddress = $customer_collection->getEmail();
			unset($customer_collection);
		}
		else{
			$this->_customer_id = '';
			$this->_first_name = '';
			$this->_last_name = '';
			$this->_emailAddress = '';
		}
		//========================== Billing Address ==========================================//
		$data['billingcompanyname'] = $importData['billingcompanyname'];
		$data['billingfirstname'] = $importData['billingfirstname'];
		$data['billinglastname'] = $importData['billinglastname'];
		$data['billingaddress1'] = $importData['billingaddress1'];
		$data['billingaddress2'] = $importData['billingaddress2'];
		$data['billingcity'] = $importData['billingcity'];
		$data['billingpostalcode'] = $importData['billingpostalcode'];
		$data['billingphonenumber'] = $importData['billingphonenumber'];
		$data['billingstate'] = $importData['billingstate'];
		$data['billingfaxnumber'] = $importData['billingfaxnumber'];
		$data['billingcountry'] = $importData['billingcountry'];
		$regionModel = Mage::getModel('directory/region')->loadByCode($data['billingstate'], $data['billingcountry']);
		$data['billingstate'] = $regionModel->getName();
		unset($regionModel);
		//========================= Billing Address =========================================//
		$data['shipcompanyname'] = $importData['shipcompanyname'];
		$data['shipfirstname'] = $importData['shipfirstname'];
		$data['shiplastname'] = $importData['shiplastname'];
		$data['shipaddress1'] = $importData['shipaddress1'];
		$data['shipaddress2'] = $importData['shipaddress2'];
		$data['shipcity'] = $importData['shipcity'];
		$data['shippostalcode'] = $importData['shippostalcode'];
		$data['shipphonenumber'] = $importData['shipphonenumber'];
		$data['shipfaxnumber'] = $importData['shipfaxnumber'];
		$data['shipstate'] = $importData['shipstate'];
		$data['shipcountry'] = $importData['shipcountry'];
		$regionModel_ship = Mage::getModel('directory/region')->loadByCode($data['shipstate'], $data['shipcountry']);
		$data['shipstate'] = $regionModel_ship->getName();
		unset($regionModel_ship);
		
		//============================== PAYMENT DETAILS ===============================//
		$data['shippingmethodid'] = $importData['shippingmethodid'];
		$data['totalShippingFees'] = $importData['totalshippingcost'];
		$data['salestaxrate'] = $importData['salestaxrate'];
		$data['salestax1'] = $importData['salestax1'];
		$data['total_payment_authorized'] = $importData['total_payment_authorized'];
		
		//payment declined 
		$data['paymentamount'] = $importData['paymentamount'];
		$data['total_payment_received'] = $importData['total_payment_received'];
		
		$data['paymentdeclined'] = $importData['paymentdeclined'];
		
		if($data['paymentdeclined'] == 'Y')
			$data['total_refunded'] = 0;
		elseif($data['total_payment_received'] == 0 && $data['total_payment_authorized'] == 0)
			$data['total_refunded'] = 0;
		else
			$data['total_refunded'] = $data['paymentamount'] - $data['total_payment_received'];
		
		$data['dateOrdered'] = $importData['orderdate'];
		$data['discount_amount'] = 0;
		
		$data['affiliate_commissionable_value'] = $importData['affiliate_commissionable_value'];
		$data['remote_ip'] = trim($importData['customer_ipaddress']);
		$data['totalItemQty'] = 3;
		$data['cc_last4'] = $importData['cc_last4'];
		$data['creditcardtransactionid'] = $importData['creditcardtransactionid'];
		$data['cardholdersname'] = $importData['cardholdersname'];
		// Payment Method
		$data['paymentmethodid'] = $importData['paymentmethodid'];
		if($data['paymentmethodid'] == 1){
			$data['payment_method'] = 'free';
			$data['cc_type'] = '';
		}
		elseif($data['paymentmethodid'] == 3){
			$data['payment_method'] = 'checkmo';
			$data['cc_type'] = '';
		}
		elseif($data['paymentmethodid'] == 5){
			$data['payment_method'] = 'ccsave';
			$data['cc_type'] = 'VI';
		}
		elseif($data['paymentmethodid'] == 6){
			$data['payment_method'] = 'ccsave';
			$data['cc_type'] = 'MC';
		}
		elseif($data['paymentmethodid'] == 7){
			$data['payment_method'] = 'ccsave';
			$data['cc_type'] = 'AE';
		}
		elseif($data['paymentmethodid'] == 8){
			$data['payment_method'] = 'ccsave';
			$data['cc_type'] = 'DI';
		}
		elseif($data['paymentmethodid'] == 9){
			$data['payment_method'] = 'ccsave';
			$data['cc_type'] = 'DI';
		}
		elseif($data['paymentmethodid'] == 12){
			$data['payment_method'] = 'ccsave';
			$data['cc_type'] = 'JCB';
		}
		elseif($data['paymentmethodid'] == 21){
			$data['payment_method'] = 'ccsave';
			$data['cc_type'] = 'SO';
		}
		elseif($data['paymentmethodid'] == 22){
			$data['payment_method'] = 'ccsave';
			$data['cc_type'] = 'SM';
		}
		elseif($data['paymentmethodid'] == 18){
			$data['payment_method'] = 'paypal_express';
			$data['cc_type'] = '';
			$data['last_trans_id'] = $importData['creditcardtransactionid'];
			$data['creditcardtransactionid'] = '';
		}
		else{
			$data['payment_method'] = 'free';
			$data['cc_type'] = '';
		}
		//order status
		$data['orderstatus'] = $importData['orderstatus'];
		if($data['orderstatus'] == 'Shipped'){
			$data['status'] = 'complete';
			$data['state'] = Mage_Sales_Model_Order::STATE_COMPLETE;
		}
		elseif($data['orderstatus'] == 'Cancelled'){
			$data['status'] = 'canceled';
			$data['state'] = Mage_Sales_Model_Order::STATE_CANCELED;
		}
		elseif($data['orderstatus'] == 'Awaiting Payment'){
			$data['status'] = 'pending_payment';
			$data['state'] = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
		}
		elseif($data['orderstatus'] == 'Pending'){
			$data['status'] = 'Pending';
			$data['state'] = Mage_Sales_Model_Order::STATE_NEW;
		}
		elseif($data['orderstatus'] == 'Processing'){
			$data['status'] = 'Processing';
			$data['state'] = Mage_Sales_Model_Order::STATE_PROCESSING;
		}
		elseif($data['orderstatus'] == 'Returned'){
			$data['status'] = 'returned';
			$data['state'] = Mage_Sales_Model_Order::STATE_CLOSED;
		}
		elseif($data['orderstatus'] == 'Partially Returned'){
			$data['status'] = 'partially_returned';
			$data['state'] = Mage_Sales_Model_Order::STATE_CLOSED;
		}
		elseif($data['orderstatus'] == 'Partially Shipped'){
			$data['status'] = 'partially_shipped';
			$data['state'] = Mage_Sales_Model_Order::STATE_PROCESSING;
		}
		elseif($data['orderstatus'] == 'Backordered'){
			$data['status'] = 'backordered';
			$data['state'] = Mage_Sales_Model_Order::STATE_PROCESSING;
		}
		elseif($data['orderstatus'] == 'Pending Shipment'){
			$data['status'] = 'pending_shipment';
			$data['state'] = Mage_Sales_Model_Order::STATE_PROCESSING;
		}
		else{
			$data['status'] = 'Pending';
			$data['state'] = Mage_Sales_Model_Order::STATE_NEW;
		}
		$order = Mage::getModel('sales/order');

		$order->setOldOrderId($this->old_order_id)
			->setStoreId($store->getId())
			->setCustomerEmail($this->_emailAddress)
			->setCustomerFirstname($this->_first_name)
			->setCustomerLastname($this->_last_name)
			->setCustomerId($this->_customer_id)
			->setCustomerIsGuest(0)
			->setCustomerGroupId(Mage_Customer_Model_Group::NOT_LOGGED_IN_ID)
			->setData('state', $data['state']) //due to complete can not set manually
			->setStatus($data['status'])
			->setShippingDescription('Flat Rate - Fixed')
			->setCreatedAt($this->getOrderDate($data))
			->setGrandTotal($data['paymentamount']) //Total payment in order (grand total
			->setBaseGrandTotal($data['paymentamount'])
			->setBaseTotalPaid($data['total_payment_received'])
			->setTotalPaid($data['total_payment_received'])
			->setPaymentAuthorizationAmount($data['total_payment_authorized'])
			->setBaseCurrencyCode('USD')
			->setGlobalCurrencyCode('USD')
			->setStoreCurrencyCode('USD')
			->setOrderCurrencyCode('USD')
			->setBaseTotalRefunded($data['total_refunded'])
			->setTotalRefunded($data['total_refunded'])
			->setBaseTotalDue($data['paymentamount'] - $data['total_payment_received'])
			->setTotalDue($data['paymentamount'] - $data['total_payment_received'])
			->setTaxAmount($data['salestax1'])
			->setBaseDiscountAmount($data['discount_amount'])
			->setDiscountAmount($data['discount_amount'])
			->setBaseTaxAmount($data['salestax1'])
			->setAffiliateCommissionableValue($data['affiliate_commissionable_value'])
			->setRemoteIp($data['remote_ip'])
			->setShippingAmount($data['totalShippingFees'])
			->setBaseShippingAmount($data['totalShippingFees'])
			->setSubtotal($data['paymentamount'] - $data['totalShippingFees'] - $data['salestax1'] - $data['discount_amount'])
			->setBaseSubtotal($data['paymentamount'] - $data['totalShippingFees'] - $data['salestax1'] - $data['discount_amount'])
			->setSubtotalInclTax((float) $data['paymentamount'] - $data['totalShippingFees'] - $data['discount_amount'] + $data['salestax1'])
			->setBaseSubtotalInclTax((float) $data['paymentamount'] - $data['totalShippingFees'] - $data['discount_amount'] + $data['salestax1'])
			->setTotalQtyOrdered($data['totalItemQty'])
			->setBillingAddress($this->getBillingAddress($data))
			->setShippingAddress($this->getShippingAddress($data))
			->setPayment($this->getPayment($data));
			
			// base_total_due = $this->getGrandTotal()-$this->getTotalPaid();
			//====================== add Order Items ===============================//
			//$order->addItem($this->getItem());
		
		try {
			$order->save();
		} catch (Exception $e) {
			throw $e;
		}
		
		
	}
	public function getOrderDate($data)
	{
		$date  = new Zend_Date($data['dateOrdered'],Varien_Date::DATETIME_INTERNAL_FORMAT);
		$date->setTimezone('UTC')->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		return $date;
	}
	
	public function getBillingAddress($data)
	{
		return Mage::getModel('sales/order_address')
			->setCompany($data['billingcompanyname'])
			->setEmail($this->_emailAddress)
			->setCustomerId($this->_customer_id)
			->setRegion($data['billingstate'])
			->setTelephone($data['billingphonenumber'])
			->setCountryId($data['billingcountry'])
			->setFirstname($$data['billingfirstname'])
			->setLastname($data['billinglastname'])
			->setCity($data['billingcity'])
			->setStreet($data['billingaddress1'].$data['billingaddress2'])
			->setFax($data['billingfaxnumber'])
			->setPostcode($data['billingpostalcode']);
	}
	
	public function getShippingAddress($data)
	{
		return Mage::getModel('sales/order_address')
			->setCompany($data['shipcompanyname'])
			->setEmail($this->_emailAddress)
			->setCustomerId($this->_customer_id)
			->setRegion($data['shipstate'])
			->setTelephone($data['shipphonenumber'])
			->setCountryId($data['shipcountry'])
			->setFirstname($$data['shipfirstname'])
			->setLastname($data['shiplastname'])
			->setCity($data['shipcity'])
			->setStreet($data['shipaddress1'].$data['shipaddress2'])
			->setFax($data['shipfaxnumber'])
			->setPostcode($data['shippostalcode']);
	}
	
	public function getPayment($data)
	{
		return Mage::getModel('sales/order_payment')
			->setMethod($data['payment_method'])
			->setAmount($data['paymentamount'])
			->setCcLast4($data['cc_last4'])
			->setCcType($data['cc_type'])
			->setCurrency('USD')
			->setCcTransId($data['creditcardtransactionid'])
			//->setCcOwner($data['cardholdersname'])
			->setBaseAmountPaid($data['total_payment_received'])
			->setAmountPaid($data['total_payment_received'])
			->setBaseAmountOrdered($data['paymentamount'])
			->setAmountOrdered($data['paymentamount'])
			->setBaseAmountAuthorized($data['total_payment_authorized'])
			->setAmountAuthorized($data['total_payment_authorized'])
			->setBaseShippingAmount($data['totalShippingFees'])
			->setShippingAmount($data['totalShippingFees'])
			->setLastTransId($data['last_trans_id']);
	}
	
	/*public function getItem()
	{
		return $orderItem = Mage::getModel('sales/order_item')
				->setStoreId(1)
				//->setProductId()
				->setProductType('simple')
				->setSku('SKU-123123123-TEST')
				->setPrice('122')
				->setWeight('1000')
				->setIsVirtual(0)
				->setName('My non existing product')
				->setBaseOriginalPrice('123.99')
			;
	}
    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return Mage_Core_Model_Store
     */
    public function getStoreByCode($store)
    {
        $this->_initStores();
        if (isset($this->_stores[$store])) {
            return $this->_stores[$store];
        }
        return false;
    }

    /**
     *  Init stores
     *
     *  @param    none
     *  @return      void
     */
    protected function _initStores ()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
            foreach ($this->_stores as $code => $store) {
                $this->_storesIdCode[$store->getId()] = $code;
            }
        }
    }
	
}

?> 