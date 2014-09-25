<?php
ini_set('max_execution_time', 180000);
class Compandsave_Additem_Model_Convert_Adapter_Additem
    extends Mage_Eav_Model_Convert_Adapter_Entity
{
 
	protected $defaultAttributeSetId;
    protected $_stores;
	
	
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
		
		$orderid = $importData['orderid'];
		$productcode = $importData['productcode'];
		$productprice = $importData['productprice'];
		$quantity = $importData['quantity'];
		$totalprice = $importData['totalprice'];
		$productweight = $importData['productweight'];
		$affiliate_commissionable_value = $importData['affiliate_commissionable_value'];
		$couponcode = $importData['couponcode'];
		
		if($importData['shipdate'] != ''){
			$shipdate = $this->getOrderDate($importData['shipdate']);
		}
		else{
			$shipdate = '';
		}
		$shipflag = $importData['shipped'];
		
		$product_collection = Mage::getModel('catalog/product')->loadByAttribute('sku',$productcode);
		if( $product_collection != '' ){
			$product_id = $product_collection->getId();
			$product_name = $product_collection->getName();
			$product_type = $product_collection->getTypeID();
			unset($product_collection);
		}
		else{
			$product_id = '';
			$product_name = $importData['productname'];
			$product_type = '';
		}
		$order = Mage::getResourceModel('sales/order_collection')->addFieldToFilter('old_order_id',$orderid)->getFirstItem()->load();
		$new_order_id = $order->getId();
		
		
		$orderItem = Mage::getModel('sales/order_item')
				->setStoreId($store->getId())
				->setOrderId($new_order_id)
				->setProductId($product_id)
				->setProductType($product_type)
				->setSku($productcode)
				->setPrice($productprice)
				->setWeight($productweight)
				->setIsVirtual(0)
				->setName($product_name)
				->setBaseOriginalPrice($productprice)
				->setIsQtyDecimal(0)
				->setQtyOrdered($quantity)
				->setBasePrice($productprice)
				->setOriginalPrice($productprice)
				->setRowTotal($totalprice)
				->setPriceInclTax($productprice)
				->setBasePriceInclTax($productprice)
				->setRowTotalInclTax($totalprice)
				->setBaseRowTotalInclTax($totalprice)
				->setAffiliateCommissionableValue($affiliate_commissionable_value)
				->setShipdate($shipdate)
				->setShipflag($shipflag)
				->setCouponcode($couponcode)
				->save();
    }
	public function getOrderDate($shipdate)
	{
		$date  = new Zend_Date($shipdate,Varien_Date::DATETIME_INTERNAL_FORMAT);
		$date->setTimezone('UTC')->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		return $date;
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