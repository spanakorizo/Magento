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
			
				$typeId = Mage_Catalog_Model_Product_Type::TYPE_BUNDLE;
				
				$sku = $importData['sku'];
				$update_product = Mage::getModel('catalog/product')->setStoreId($store_code)->loadbyAttribute('sku',$sku);
				
				//$image = mysql_real_escape_string($importData['image']);
				//$small_image = mysql_real_escape_string($importData['small_image']);
				//$thumbnail = mysql_real_escape_string($importData['thumbnail']);
				$image = $importData['image'];
				$small_image = $importData['small_image'];
				$thumbnail = $importData['thumbnail'];

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



				//$selectionCollection = $update_product->getTypeInstance(true)->getSelectionsCollection(
				//$update_product->getTypeInstance(true)->getOptionsIds($update_product), $update_product
				//);
 
				//$bundled_items = array();
				//foreach($selectionCollection as $option) 
				//{
				//	$item = Mage::getModel('catalog/product')->load($option->product_id);
				//	$item_sku = $item->getSku();
				//	$item_images[] = $item->getSku() . ".jpg";
					//$item_images[] = strtoupper($item->getSku()) . ".jpg";

				//}
 
				//print_r($item_images);


					//=======================update product=======================//
				/*
					if($store_code != ''){
						$update_product->setStoreId($store_code)
									->setWebsiteIds(array(Mage::app()->getStore($store_code)->getWebsite()->getId()));
					}*/
					//change static store code to dynamic
					
					$bundle_product_id = $update_product->getId();	
							//->setCategoryIds(array($root_id,$child_cat_id)) //cat id will be array

					//echo $sku . $importData['image'] . $image . $thumbnail . $small_image;
					//for image update
					if ($image != '' || $thumbnail != '' || $small_image != '') {
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
										Mage::throwException(Mage::helper('compandsave_bundle')->__('Image path is not correct, Path was: {$filePath}'.$filePath));
									}
								}
							}
/*
							foreach($item_images as $item_image) {
								if($item_image != ''){
									$filePath = $importDir.$item_image;
									if ( file_exists($filePath) ) {
										try {
											$update_product->getTypeInstance(true)->setStoreFilter($store_code, $update_product);
											$update_product->addImageToMediaGallery($filePath, null, false, false);
										} catch (Exception $e) {
											echo $e->getMessage();
										}
									} else {
										Mage::throwException(Mage::helper('compandsave_bundle')->__('Image path is not correct, Path was: {$filePath}'));
									}
								}
							}*/
							
						}
						
					}

					$update_product->save();

					/*if($url_key == ''){
						foreach($url as $urlkey){
							$conn_write->delete($tablename, array('entity_id = ?' => array($urlkey['entity_id']), 'store_id = ?' => $store_code,'attribute_id = ?' => $urlkey['attribute_id'] ,'entity_type_id = ?'=> $entityTypeId ));
							
							$conn_write->insert($tablename, array('value' => $urlkey['value'], 'store_id' => $store_code, 'attribute_id' => $urlkey['attribute_id'],'entity_id' => $bundle_product_id,'entity_type_id'=> $entityTypeId ));
						}
					}*/
					unset($update_product);
					return true;

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