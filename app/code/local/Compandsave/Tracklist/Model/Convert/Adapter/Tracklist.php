<?php
ini_set('max_execution_time', 180000);
class Compandsave_Tracklist_Model_Convert_Adapter_Tracklist
    extends Mage_Eav_Model_Convert_Adapter_Entity
{
 	
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
        
		if($importData['gateway'] == 'FEDEX'){
			$carrier_code = 'fedex';
			$carrier_title = 'Federal Express';
		}
		else if($importData['gateway'] == 'UPS'){
			$carrier_code = 'ups';
			$carrier_title = 'United Parcel Service';
		}
		else if($importData['gateway'] == 'USPS'){
			$carrier_code = 'usps';
			$carrier_title = 'United States Postal Service';
		}
		else{
			$carrier_code = 'usps';
			$carrier_title = 'United States Postal Service';
		}
		
		$order = Mage::getModel('sales/order')->getCollection()->addFieldToFilter('old_order_id',trim($importData['orderid']))->getFirstItem();
		
		if(	$order != ''){
			$IncrementId = $order['increment_id'];
			
			
			if($order->canShip()) {
				
				$shipmentid = Mage::getModel('sales/order_shipment_api')
								->create($IncrementId, array());
				
				$shipment = Mage::getModel('sales/order_shipment')->getCollection()->addFieldToFilter('order_id', $order['entity_id'])->getFirstItem();
				
				$track = Mage::getModel('sales/order_shipment_track')
					 ->setTrackNumber($importData['trackingnumber'])
					 ->setCarrierCode($carrier_code)
					 ->setTitle($carrier_title)
					 ->setOrderId($order['entity_id'])
					 ->setParentID($shipment['entity_id'])
					 ->setCreatedAt($this->getShipDate($importData['shipdate']))
					 ->save();
					
		  
			}
		}
	}
	public function getShipDate($data)
	{
		$date  = new Zend_Date($data,Varien_Date::DATETIME_INTERNAL_FORMAT);
		$date->setTimezone('UTC')->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
		return $date;
	}
	
}

?> 