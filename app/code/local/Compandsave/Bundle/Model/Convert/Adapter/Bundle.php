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
        
       	$coreResource = Mage::getSingleton('core/resource');
		$conn = $coreResource->getConnection('core_read');
		$conn_write = $coreResource->getConnection('core_write');
		$StoreTable = $coreResource->getTableName('core/store');
		$select = $conn->select()
			->from($StoreTable, array('store_id'))
			->where('code = ?', $importData['Store']);

		$result = $conn->fetchCol($select);
		foreach($result as $list )
			$store_code = $list;

		if($store_code != ''){
			if($importData['sku'] != ''){
				$new_product = Mage::getModel('catalog/product');
				$typeId = Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
				
				$sku = $importData['sku'];
							
				$vendor_partno = mysql_real_escape_string($importData['vendor_partno']);
				$name = mysql_real_escape_string($importData['name']);
				
				if($importData['status'] === '' || !isset($importData['status']))
					$status = 1; 
				else
					$status = mysql_real_escape_string($importData['status']);
				
				$photos_cloned_from = mysql_real_escape_string($importData['photos_cloned_from']);
				$short_description = mysql_real_escape_string($importData['short_description']);
				$description = mysql_real_escape_string($importData['description']);
				$upc_code = mysql_real_escape_string($importData['upc_code']);
				$url_key =  mysql_real_escape_string($importData['url_key']);//'bundle-'.$sku;
				$is_weight_by_warehouse = mysql_real_escape_string($importData['is_weight_by_warehouse']);
				$freeshippingitem = mysql_real_escape_string($importData['freeshippingitem']);
				$price =  mysql_real_escape_string($importData['price']);
				$msrp = mysql_real_escape_string($importData['msrp']);
				$tax_class = mysql_real_escape_string($importData['tax_class']);
				$accessories = mysql_real_escape_string($importData['accessories']);
				$availability = mysql_real_escape_string($importData['availability']);
				$meta_title = mysql_real_escape_string($importData['meta_title']);
				$meta_description = mysql_real_escape_string($importData['meta_description']);
				$photo_alttext = mysql_real_escape_string($importData['photo_alttext']);
				$affiliate_commissionable_value = mysql_real_escape_string($importData['affiliate_commissionable_value']);
				$mpn = mysql_real_escape_string($importData['mpn']);
				$productcondition = mysql_real_escape_string($importData['productcondition']);
				$nextag_category = mysql_real_escape_string($importData['nextag_category']);
				$yahoo_category = mysql_real_escape_string($importData['yahoo_category']);
				$shopzilla_category = mysql_real_escape_string($importData['shopzilla_category']);
				$productmanufacturer = mysql_real_escape_string($importData['productmanufacturer']);
				$discount_profile = mysql_real_escape_string($importData['discount_profile']);
				$google_product_type = mysql_real_escape_string($importData['google_product_type']);
				$meta_keywords = mysql_real_escape_string($importData['meta_keywords']);
				$description_above = mysql_real_escape_string($importData['description_above']);
				$item = mysql_real_escape_string($importData['bundle_items']);
				
				$title = $sku; //change Bundle title name
				
				$warehouse_location = mysql_real_escape_string($importData['warehouse_location']);
				$image = mysql_real_escape_string($importData['image']);
				$small_image = mysql_real_escape_string($importData['small_image']);
				$thumbnail = mysql_real_escape_string($importData['thumbnail']);
				//===========================   add image coding   ============================//
				//if we add only one size 
				/* $product->setMediaGallery (array('images'=>array (), 'values'=>array ()));
				$product->addImageToMediaGallery ($fullImagePath, array ('image','small_image','thumbnail'), false, false);  */
				// Add three image sizes to media gallery
				$mediaArray = array(
					'thumbnail'   => $thumbnail,
					'small_image' => $small_image,
					'image'       => $image,
				);

				// Remove unset images, add image to gallery if exists
				$importDir = Mage::getBaseDir('media') . DS . 'import/';

				
				/*======================= FIND THE ROOT AND SUBCATERGORY ======================//
				$cat = explode('/',mysql_real_escape_string($importData['category']);
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
				if(isset($is_weight_by_warehouse)){
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
						$product_grand_total_weight = mysql_real_escape_string($importData['weight']);
					}
				}
				//======================== SET ATTRIBUTE SET ID ========================//
				
				if(mysql_real_escape_string($importData['attribute_set']) != ''){
				
					$attributeSetId = $this->attributeid(mysql_real_escape_string($importData['attribute_set']));
					
				}
				else{
					$attributeSetId = $new_product->getResource()->getEntityType()->getDefaultAttributeSetId();
				}
				//============================== SET RELATED PRODUCTS ========================*/
				if(isset($accessories)){
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
				}
						
				//============================================================================//
				$update_product = Mage::getModel('catalog/product')->loadbyAttribute('sku',$sku);
								
				if($update_product == ''){ //create new product
					unset($update_product);
														
					$new_product->setStoreId($store_code) //change static store code to dynamic
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
							//->setImage($image)
							//->setSmallImage($small_image)
							//->setThumbnail($thumbnail)
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
							->setMetaKeyword($meta_keywords)
							->setDescriptionAbove($description_above)
							->setRelatedLinkData($param);
									

					Mage::register('product', $new_product); //for selection data register the product
					
					$optionId = 0;
					$optionRawData[$optionId] = array(
						'required' => 1,
						'position' => 0,
						'type' => 'multi',
						'title' => $title,
						'delete' => ''
					);
					$new_product->setBundleOptionsData($optionRawData);
					
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
						
						$new_product->setBundleSelectionsData($selectionRawData)
						->setCanSaveBundleSelections(true);
					}
					if($image != '' && ($thumbnail == '' && $small_image == '')){
						$filePath = $importDir.$image;
						$new_product->addImageToMediaGallery ($filePath, array ('image','small_image','thumbnail'), false, false);
						
					}
					else{
						// =============== IF WE NEED TO SET IMAGE SEPARETELY THEN WILL USE BELOW CODEING =====================//
						/*->setMediaGallery(array('images'=>array (), 'values'=>array ()));;*/
						if($mediaArray != ''){				
							foreach($mediaArray as $imageType => $fileName) {
								if($fileName != ''){
									$filePath = $importDir.$fileName;
									if ( file_exists($filePath) ) {
										try {
											
											$new_product->addImageToMediaGallery($filePath, $imageType, false, false);
										} catch (Exception $e) {
											echo $e->getMessage();
										}
									} else {
										Mage::throwException(Mage::helper('compandsave_bundle')->__('Image path is not correct, Path was: {$filePath}'));
									}
								}
							}
							
						}
					}
					$new_product->save();
					unset($new_product);
					//=================== 
					if($store_code > 0){
						
						$alter_product = Mage::getModel('catalog/product')->loadbyAttribute('sku',$importData['sku']);
						$bundle_product_id = $alter_product->getId();
						
						$entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
						$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
						$thumbnailAttrId = $eavAttribute->getIdByCode('catalog_product', 'thumbnail');
						$smallImageAttrId = $eavAttribute->getIdByCode('catalog_product', 'small_image');
						$imageAttrId = $eavAttribute->getIdByCode('catalog_product', 'image');
						$galleryAttrId = $eavAttribute->getIdByCode('catalog_product', 'media_gallery');
						$select = $conn->select()
										->from($coreResource->getTableName('catalog_product_entity_media_gallery'), array('value','value_id'))
										->where('entity_id = ?',$bundle_product_id)
										->where('attribute_id = ?',$galleryAttrId);
										
						$images = $conn->fetchAll($select);
						
						foreach($images as $old_image) {
							
							$conn_write->insert($coreResource->getTableName('catalog_product_entity_media_gallery_value'), array('value_id' => $old_image['value_id'], 'store_id' => '0', 'position' => '1'));
							
						}
						
						$select = $conn->select()
								->from($coreResource->getTableName('catalog_product_entity_varchar'), array('value','attribute_id','value_id'))
								->where('attribute_id IN (?)',array($imageAttrId, $smallImageAttrId, $thumbnailAttrId))
								->where('entity_id = ?',$bundle_product_id)
								->where('entity_type_id = ?',$entityTypeId); 
						$images = $conn->fetchAll($select);
						
						foreach($images as $old_image) {
							
							$conn_write->insert($coreResource->getTableName('catalog_product_entity_varchar'), array('value' => $old_image['value'], 'store_id' => '0', 'attribute_id' => $old_image['attribute_id'],'entity_id' => $bundle_product_id,'entity_type_id'=> $entityTypeId ));
	
						}

					}
					
					return true;
				}
				else{
					//=======================update product=======================//
					
					if($store_code != ''){
						$update_product->setStoreId($store_code)
									->setWebsiteIds(array(Mage::app()->getStore($store_code)->getWebsite()->getId()));
					}
					//change static store code to dynamic
												
					if($attributeSetId != '')		
						$update_product->setAttributeSetId($attributeSetId);
					
					$bundle_product_id = $update_product->getId();	
							//->setCategoryIds(array($root_id,$child_cat_id)) //cat id will be array
					if('' != $name)	
						$update_product->setName($name);
					if('' != $description)	
						$update_product->setDescription($description);
					if('' != $short_description)	
						$update_product->setShortDescription($short_description);
					if('' != $status)	
						$update_product->setStatus($status);
					if('' != $tax_class)
						$update_product->setTaxClassId($tax_class);
					if('' != $product_grand_total_weight)
						$update_product->setWeight($product_grand_total_weight);
					if('' != $price)
						$update_product->setPrice($price);
					if('' != $is_weight_by_warehouse)
						$update_product->setIsWeightByWarehouse($is_weight_by_warehouse);
					if('' != $vendor_partno)
						$update_product->setVendorPartno($vendor_partno);
					if($url_key != '')
						$update_product->setUrlKey($url_key);
					if('' != $meta_title)
						$update_product->setMetaTitle($meta_title);
					if('' != $photos_cloned_from)
						$update_product->setPhotosClonedFrom($photos_cloned_from);
					if('' != $upc_code)
						$update_product->setUpcCode($upc_code);
					if('' != $warehouse_location)
						$update_product->setWarehouseLocation($warehouse_location);
					if('' != $msrp)
						$update_product->setMsrp($msrp);
					if('' != $freeshippingitem)
						$update_product->setFreeshippingItem($freeshippingitem);
					if('' != $availability)
						$update_product->setAvailability($availability);
					if('' != $meta_description)
						$update_product->setMetaDescription($meta_description);
					if('' != $photo_alttext)
						$update_product->setPhotoAlttext($photo_alttext);
					if('' != $affiliate_commissionable_value)
						$update_product->setAffiliateCommissionableValue($affiliate_commissionable_value);
					if('' != $mpn)
						$update_product->setMpn($mpn);
					if('' != $productcondition)
						$update_product->setProductCondition($productcondition);
					if('' != $nextag_category)
						$update_product->setNextagCategory($nextag_category);
					if('' != $yahoo_category)
						$update_product->setYahooCategory($yahoo_category);
					if('' != $shopzilla_category)
						$update_product->setShopzillaCategory($shopzilla_category);
					if('' != $productmanufacturer)
						$update_product->setProductManufacturer($productmanufacturer);
					if('' != $discount_profile)
						$update_product->setDiscountProfile($discount_profile);
					if('' != $google_product_type)
						$update_product->setGoogleProductType($google_product_type);
					if('' != $meta_keywords)
						$update_product->setMetaKeyword($meta_keywords);
					if($description_above != '')
						$update_product->setDescriptionAbove($description_above);
					if($param != '')
						$update_product->setRelatedLinkData($param);
					
					if($importData['bundle_items'] != ''){
						//delete existing items from products
						
						$products_links = Mage::getModel('catalog/product_link_api');
						$RelationTable = $coreResource->getTableName('catalog/product_relation'); //get table name for catalog_product_relation
						$RealtionLinkTable = $coreResource->getTableName('catalog/product_link'); //get table name for catalog_product_link
						$select = $conn->select()
							->from($RealtionLinkTable, array('product_id'))
							->where('linked_product_id = ?', $bundle_product_id)
							->where('link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);

						$result = $conn->fetchCol($select); //get group product id (product_id) for linked product id (simple/bundle) called child
						

						foreach($result as $item_list){
							$products_links->remove ("grouped",$item_list,$bundle_product_id);
							$conn_write->delete($RelationTable, array('child_id = ?' => $bundle_product_id, 'parent_id = ?' => $item_list));
						}
						
						
						$item_list_array = explode(',',$importData['bundle_items']);
						
						Mage::register('product', $update_product);	
						// create first option
						$update_product->getTypeInstance(true)->setStoreFilter($store_code, $update_product);
						$optionCollection = $update_product->getTypeInstance(true)->getOptionsCollection($update_product);
						
						
						$optionRawData = array();
						$i=0;
						foreach($optionCollection as $option){
							$optionRawData[$i] = array(
									'option_id' => $option->getOptionId(), //my addition. important otherwise, options going to be duplicated
									'required' => $option->getData('required'),
									'position' => $option->getData('position'),
									'type' => $option->getData('type'),
									'title' => $option->getData('title')?$option->getData('title'):$option->getData('default_title'),
									'delete' => ''
								);
								$i++;
						}
						
						$update_product->setBundleOptionsData($optionRawData);
						
						foreach($item_list_array as $item_list){
						
							$list = explode('/',$item_list);
							
							$product_menu = Mage::getModel('catalog/product')->loadByAttribute('sku',$list[0]); 
							//print_r($product);
							$item_sku = $product_menu['entity_id'];
							
							if($list[1] != '' || $list[1] != 0 || isset($list[1]))
									$product_qty = $list[1];
								else
									$product_qty = 1;
							unset($product_menu);
					
					
						
							$selectionRawData[0][] = array(
								'product_id' => $item_sku,
								'position' => 0,
								'is_default' => 1,
								'selection_price_type' => 0,
								'selection_price_value' => 0,
								'selection_qty' => $product_qty,
								'selection_can_change_qty' => 0,
								'delete' => ''
							);
							
							$update_product->setBundleSelectionsData($selectionRawData)
							->setCanSaveBundleSelections(true);
						}
					}
					//for image update
					if($image != '' || $thumbnail != '' || $small_image != ''){
						$entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
						$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
						if($thumbnail == '')
							$thumbnailAttrId = '';
						else
							$thumbnailAttrId = $eavAttribute->getIdByCode('catalog_product', 'thumbnail');
						if($small_image == '')
							$smallImageAttrId = '';
						else
							$smallImageAttrId = $eavAttribute->getIdByCode('catalog_product', 'small_image');
						if($image == '')
							$imageAttrId = '';
						else
							$imageAttrId = $eavAttribute->getIdByCode('catalog_product', 'image');
						
						$images = $conn->fetchAll("SELECT value,value_id FROM " . $coreResource->getTableName('catalog_product_entity_media_gallery')." where entity_id = ?",$bundle_product_id);
						foreach($images as $old_image) {
									
							$conn_write->delete($coreResource->getTableName('catalog_product_entity_media_gallery_value'), array('value_id = ?' => array($old_image['value_id']), 'store_id = ?' => $store_code));
							
						}
						$select = $conn->select()
									->from($coreResource->getTableName('catalog_product_entity_varchar'), array('value','value_id'))
									->where('attribute_id IN(?)',array($imageAttrId, $smallImageAttrId, $thumbnailAttrId))
									->where('entity_type_id = ?',$entityTypeId)
									->where('entity_id = ?',$bundle_product_id)
									->where('store_id = ?', $store_code);
						$images = $conn->fetchAll($select);
						
						foreach($images as $old_image) {
						
							$conn_write->delete($coreResource->getTableName('catalog_product_entity_varchar'), array('value_id = ?' => array($old_image['value_id']), 'store_id = ?' => $store_code, 'attribute_id = ?' => $old_image['attribute_id'] ,'entity_type_id = ?'=> $entityTypeId));
							
						}
						
						if($mediaArray != ''){				
							foreach($mediaArray as $imageType => $fileName) {
								if($fileName != ''){
									$filePath = $importDir.$fileName;
									if ( file_exists($filePath) ) {
										try {
											$update_product->getTypeInstance(true)->setStoreFilter($store_code, $update_product);
											$update_product->addImageToMediaGallery($filePath, $imageType, false, false);
										} catch (Exception $e) {
											echo $e->getMessage();
										}
									} else {
										Mage::throwException(Mage::helper('compandsave_bundle')->__('Image path is not correct, Path was: {$filePath}'));
									}
								}
							}
							
						}
						
					}
					if($url_key == ''){
						$entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
						$eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();
						$tablename = $coreResource->getTableName('catalog_product_entity_url_key');
						$urlKeyId = $eavAttribute->getIdByCode('catalog_product', 'url_key');
						$select = $conn->select()
									->from($tablename,array('value','attribute_id','entity_id'))
									->where('attribute_id = ?',$urlKeyId)
									->where('entity_type_id = ?',$entityTypeId)
									->where('entity_id = ?',$bundle_product_id)
									->where('store_id = ?', $store_code);
						$url = $conn->fetchAll($select);
					
					}
					
					$update_product->save();
					if($url_key == ''){
						foreach($url as $urlkey){
							$conn_write->delete($tablename, array('entity_id = ?' => array($urlkey['entity_id']), 'store_id = ?' => $store_code,'attribute_id = ?' => $urlkey['attribute_id'] ,'entity_type_id = ?'=> $entityTypeId ));
							
							$conn_write->insert($tablename, array('value' => $urlkey['value'], 'store_id' => $store_code, 'attribute_id' => $urlkey['attribute_id'],'entity_id' => $bundle_product_id,'entity_type_id'=> $entityTypeId ));
						}
					}
					unset($update_product);
					return true;
				}
			}
			else{
				Mage::throwException(Mage::helper('compandsave_bundle')->__('SKU Field Can not be Null'));
			}
		}
		else{
		
			Mage::throwException(Mage::helper('compandsave_bundle')->__('Store Field Can not be Null'));
		
		}
        return true;
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
		$entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
		$attributeSetCollection = Mage::getResourceModel('eav/entity_attribute_set_collection')
			->setEntityTypeFilter($entityTypeId)
			->addFieldToFilter("attribute_set_name", $attribute_name)->getFirstItem()
			->load();
		
		return $attributeSetCollection->getAttributeSetId();
	}
	
	
}

?> 