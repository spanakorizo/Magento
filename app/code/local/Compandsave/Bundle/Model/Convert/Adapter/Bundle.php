<?php
ini_set('max_execution_time', 180000);
class Compandsave_Bundle_Model_Convert_Adapter_Bundle
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
		
		$product = Mage::getModel('catalog/product');
		$typeId = Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;

		
		$sku = $importData['sku'];
		$vendor_partno = mysql_escape_string($importData['vendor_partno']);
		$name = mysql_escape_string($importData['name']);
		$status = mysql_escape_string($importData['status']);
		$photos_cloned_from = mysql_escape_string($importData['photos_cloned_from']);
		$short_description = mysql_escape_string($importData['shortdescription']);
		$description = mysql_escape_string($importData['description']);
		$upc_code = mysql_escape_string($importData['upc_code']);
		$url_key =  mysql_escape_string($importData['url_key']);//'bundle-'.$sku;
		$is_weight_by_warehouse = mysql_escape_string($importData['is_weight_by_warehouse']);
		$freeshippingitem = mysql_escape_string($importData['freeshippingitem']);
		$price =  mysql_escape_string($importData['price']);
		$msrp = mysql_escape_string($importData['msrp']);
		$tax_class = mysql_escape_string($importData['tax_class']);
		$accessories = mysql_escape_string($importData['accessories']);
		$availability = mysql_escape_string($importData['availability']);
		$meta_title = mysql_escape_string($importData['meta_title']);
		$meta_description = mysql_escape_string($importData['meta_description']);
		$photo_alttext = mysql_escape_string($importData['photo_alttext']);
		$affiliate_commissionable_value = mysql_escape_string($importData['affiliate_commissionable_value']);
		$mpn = mysql_escape_string($importData['mpn']);
		$productcondition = mysql_escape_string($importData['productcondition']);
		$nextag_category = mysql_escape_string($importData['nextag_category']);
		$yahoo_category = mysql_escape_string($importData['yahoo_category']);
		$shopzilla_category = mysql_escape_string($importData['shopzilla_category']);
		$productmanufacturer = mysql_escape_string($importData['productmanufacturer']);
		$discount_profile = mysql_escape_string($importData['discount_profile']);
		$google_product_type = mysql_escape_string($importData['google_product_type']);
		$meta_keywords = mysql_escape_string($importData['meta_keywords']);
		$description_above = mysql_escape_string($importData['description_above']);
		$item = mysql_escape_string($importData['bundle_items']);
		
		$title = $sku; //change Bundle title name
		
		$warehouse_location = mysql_escape_string($importData['warehouse_location']);
		$image = mysql_escape_string($importData['image']);
		$small_image = mysql_escape_string($importData['small_image']);
		$thumbnail = mysql_escape_string($importData['thumbnail']);
		
		/*======================= FIND THE ROOT AND SUBCATERGORY ======================//
		$cat = explode('/',mysql_escape_string($importData['category']);
		$cat_model = Mage::getModel('catalog/category')->loadByAttribute('name',$cat[0]); 
		$root_id = $cat_model->getId();
		$child_array = $cat_model->getChildren();
		$child_split = explode(',',$child_array);
		foreach($child_split as $child){
			$get_child = Mage::getModel('catalog/category')->load($child);
			if ($cat[1] == $get_child->getName())
				$child_cat_id = $get_child->getId();
			unset($get_child);
		}
		unset($cat_model);
		//========================  CALCULATE TOTAL WEIGHT =========================*/
		$product_grand_total_weight = 0;
		
		if($is_weight_by_warehouse == 0 || $is_weight_by_warehouse == ''){
			$items = explode(',',$importData['bundle_items']);
			foreach( $items as $item_list ){
				
				$item_sku_qty = explode('/',$item_list);
				
				$get_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$item_sku_qty[0]);
				if($get_product != '') //if sku exits
					$product_weight = $get_product->getWeight();
				else
					$product_weight = 1;

				if($item_sku_qty[1] != '' || $item_sku_qty[1] != 0 || isset($item_sku_qty[1]))
					$product_qty = $item_sku_qty[1];
				else
					$product_qty = 1;
					
				$product_total_weight = $product_weight * $product_qty;
				
				$product_grand_total_weight = $product_grand_total_weight + $product_total_weight;
				unset($get_product);
			}
			
		}
		else {
			$product_grand_total_weight = mysql_escape_string($importData['weight']);
		}
		//======================== SET ATTRIBUTE SET ID ========================//
		
		if(mysql_escape_string($importData['attribute_set']) != ''){
		
			$attributeSetId = $this->attributeid(mysql_escape_string($importData['attribute_set']));
			
		}
		else{
			$attributeSetId = $product->getResource()->getEntityType()->getDefaultAttributeSetId();
		}
		//=========================   GET STORE CODE ==============================
		//$get_store = Mage::getResourceModel('core/store_collection')->addFieldToFilter('code',mysql_escape_string($importData['Store'])->getFirstItem()->load();

		if($store->getId())
			$store_code = $store->getId();
		else{
			
			$store_code = 1;
		}//$get_store->getStoreId();
		//============================== SET RELATED PRODUCTS ========================*/
		$related = explode(',',$accessories);
		$i=0;
		foreach($related as $relateditem){
			$related_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $relateditem);
			if($related_product != ''){
				$getRelatedID = $related_product->getId();
				$param[$getRelatedID]['position'] = $i+1;
				$i++;
			}
			
		}
		unset($related_product);
		//============================================================================//
		
		$product->setStoreId($store_code) //change static store code to dynamic
				->setTypeId($typeId)
				->setAttributeSetId($attributeSetId)
				->setWebsiteIds(array(Mage::app()->getStore($store_code)->getWebsite()->getId()))
				//->setCategoryIds(array($root_id,$child_cat_id)) //cat id will be array
				->setName($name)
				->setDescription($description)
				->setShortDescription($short_description)
				->setStatus($status)
				->setVisibility(Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH)
				->setSku($sku)
				->setStockData(array('is_in_stock' => 1, 'qty' => 0))
				->setPriceView(0)
				->setPriceType(Mage_Bundle_Model_Product_Price::PRICE_TYPE_FIXED)
				->setSkuType(1)
				->setWeightType(1)
				->setTaxClassId($tax_class)
				->setWeight($product_grand_total_weight)
				->setShipmentType(Mage_Bundle_Model_Product_Type::SHIPMENT_TOGETHER)
				->setPrice($price)
				->setIsWeightByWarehouse($is_weight_by_warehouse)
				->setVendorPartno($vendor_partno)
				->setUrlKey($url_key)
				->setMetaTitle($meta_title)
				->setPhotosClonedFrom($photos_cloned_from)
				->setUpcCode($upc_code)
				->setWarehouseLocation($warehouse_location)
				->setMsrp($msrp)
				->setImage($image)
				->setSmallImage($small_image)
				->setThumbnail($thumbnail)
				->setFreeshippingItem($freeshippingitem)
				->setAvailability($availability)
				->setMetaDescription($meta_description)
				->setPhotoAlttext($photo_alttext)
				->setAffiliateCommissionableValue($affiliate_commissionable_value)
				->setMpn($mpn)
				->setProductCondition($productcondition)
				->setNextagCategory($nextag_category)
				->setYahooCategory($yahoo_category)
				->setShopzillaCategory($shopzilla_category)
				->setProductManufacturer($productmanufacturer)
				->setDiscountProfile($discount_profile)
				->setGoogleProductType($google_product_type)
				->setMetaKeywords($meta_keywords)
				->setDescriptionAbove($description_above)
				->setRelatedLinkData($param);
		
		Mage::register('product', $product);
		
		$optionId = 0;
		$optionRawData[$optionId] = array(
			'required' => 1,
			'position' => 0,
			'type' => 'multi',
			'title' => $title,
			'delete' => ''
		);
		$product->setBundleOptionsData($optionRawData);
		
		$item_list_array = explode(',',$importData['bundle_items']);
		// create first option
		
		
		foreach($item_list_array as $item_list){
		
			$list = explode('/',$item_list);
			
			$product_menu = Mage::getModel('catalog/product')->loadByAttribute('sku',$list[0]); 
			//print_r($product);
			$item_sku = $product_menu['entity_id'];
			
			if($list[1] != '' || $list[1] != 0 || isset($list[1]))
					$product_qty = $list[1];
				else
					$product_qty = 1;
		
			$selectionRawData[$optionId][] = array(
				'product_id' => $item_sku,
				'position' => 0,
				'is_default' => 1,
				'selection_price_type' => 0,
				'selection_price_value' => 0,
				'selection_qty' => $product_qty,
				'selection_can_change_qty' => 0,
				'delete' => ''
			);
			
			$product->setBundleSelectionsData($selectionRawData)
			->setCanSaveBundleSelections(true);
		}
		$product->save();
        return true;
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
	
	protected function getDefaultAttributeSetId(){
		if (!isset($this->defaultAttributeSetId)){
			$categoryModel = Mage::getModel("catalog/category");            
			$this->defaultAttributeSetId = $categoryModel->getDefaultAttributeSetId();
		}
		return $this->defaultAttributeSetId;
	}
	
	public function attributeid(string $attribute_name){
		
		//@Model to get entity attribute collection
		$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter(4)
			->addFieldToFilter("attribute_set_name", $attribute_name)->getFirstItem()
			->load();
		
		return $attributeSetCollection->getAttributeSetId();
	}
	
	
}

?> 