<?php
/**
 * Magento Enterprise Edition
 * @category    Compandsave
 * @package     Compandsave_Catalog
 * @copyright   Copyright (c) 2014 Compandsave.com Inc. (http://www.compandsave.com)
 */

Class Compandsave_Catalog_Model_Relation_Backend extends Mage_Core_Model_Abstract{

	protected function _construct(){
        $this->_init('compandsave_catalog/relation_backend');
	}
	
	public function MappingAutoAfterProductCreate($product){

        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_read');
        $conn_write = $coreResource->getConnection('core_write');
        $RelationTable = $coreResource->getTableName('catalog/product_relation'); //get table name for catalog_product_relation
        $RealtionLinkTable = $coreResource->getTableName('catalog/product_link');

        $product_ids = null;

		$product_ids = $product->getCompatibleCatid(); //find the value of Mapping category ID
		
		
		if($product->getTypeId() === 'simple' and strlen($product_ids) ){// and getCompatibleCatid() != ''

            $childId = $product->getId(); //get simple product ID

            $products_links = Mage::getModel('catalog/product_link_api');


            //get table name for catalog_product_link

            $select = $conn->select()
                ->from($RealtionLinkTable, array('product_id'))
                ->where('linked_product_id = ?', $childId)
                ->where('link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);

            $result = $conn->fetchCol($select); //get group product id (product_id) for linked product id (simple/bundle) called child

            foreach($result as $item_list){

                $products_links->remove ("grouped",$item_list,$childId); //remove all link due to save or update
                //delete all link which has link for child product(simple) and parent product id ($item_list) group
                $conn_write->delete($RelationTable, array('child_id = ?' => $childId, 'parent_id = ?' => $item_list));

            }


            $map_prd_id_arr = explode(',',$product_ids);

            foreach($map_prd_id_arr as $item){

                $get_product = Mage::getModel('catalog/product')->loadByAttribute('categoryid',$item);
                if(!empty($get_product)){
                    $parentId = $get_product->getId(); //get group product ID

                    $products_links->assign("grouped",$parentId,$childId); //insert new link only

                }
            }

        }
        else if($product->getTypeId() === 'bundle' ){
            /**
             * do not add getCompatibleCatid() != '' cause we can create link based on bundle items if no CompatibleCatid
             */

            $eavAttribute = new Mage_Eav_Model_Mysql4_Entity_Attribute();

            $AttributeId = $eavAttribute->getIdByCode('catalog_product', 'compatible_catid');
            $groupAttributeId = $eavAttribute->getIdByCode('catalog_product', 'categoryid');

            $EntityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();

            $store_id = Mage::app()->getStore()->getId();


            $bundle_product_id = $product->getId(); //get bundle product ID
			
			$mapping_product_ids = $product->getCompatibleCatid();

            $attrbTable = $coreResource->getTableName('catalog_product_entity_text');
            $attrbvarTable = $coreResource->getTableName('catalog_product_entity_varchar');

			$products_links = Mage::getModel('catalog/product_link_api');
			
			$select = $conn->select()
					->from($RealtionLinkTable, array('product_id'))
					->where('linked_product_id = ?', $bundle_product_id)
					->where('link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);

			$result = $conn->fetchCol($select); //get group product id (product_id) for linked product id (simple/bundle) called child

			foreach($result as $item_list){
				//delete data from relation table
				$products_links->remove ("grouped",$item_list,$bundle_product_id); //remove all link due to save or update
				//delete all link which has link for child product(simple) and parent product id ($item_list) group
				$conn_write->delete($RelationTable, array('child_id = ?' => $bundle_product_id, 'parent_id = ?' => $item_list));

			}
			
			if($mapping_product_ids != null){// and getCompatibleCatid() != ''

				$map_prd_id_arr = explode(',',$mapping_product_ids);
				foreach($map_prd_id_arr as $item){

					$get_product = Mage::getModel('catalog/product')->loadByAttribute('categoryid',$item);
					if(!empty($get_product)){
						$parentId = $get_product->getId(); //get group product ID
						$products_links->assign("grouped",$parentId,$bundle_product_id); //insert new link only
					}
				}
			}
			else{
                /*
                 * Create Compatible category id and link if the no compatible category id is set
                 */
				///code start for delete existing link
                $compatible_cat_id = null;

				$bundled_items = array(); //items array
				
				$select = $conn->select()
							->from($coreResource->getTableName('bundle/selection'),array('product_id'))
							->where('parent_product_id =?',$bundle_product_id);

                $bundled_items = $conn->fetchCol($select);
				
				$count = 0;
				
				$count = count($bundled_items);

				$group_product = array();
				
				foreach($bundled_items as $simple_product){
					$select_group = $conn->select()
						->from($RealtionLinkTable, array('product_id'))
						->where('linked_product_id = ?', $simple_product)
						->where('link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);
					
					$group_product_fetch = $conn->fetchCol($select_group); //get group product array
					$group_product = array_merge((array)$group_product, (array)$group_product_fetch);
				}
				
				//find number of occurrence in array grp is key and count in value
				if($count > 1){
					$group_product_count = array_count_values($group_product);
					
					$maximum_occurance_group_product = max($group_product_count); //get maximum value of occurrence
					
					foreach( $group_product_count as $group_product_id => $times_number_occurred){

						if($times_number_occurred == $maximum_occurance_group_product){ //if maximum number is equal to count 
							if($maximum_occurance_group_product == $count){ //ensure maximum occurrence is equal to maximum counter
								$products_links->assign ("grouped",$group_product_id,$bundle_product_id);
                                $compatible_cat_id = $group_product_id.','.$compatible_cat_id;

							}
							else{
								//if not equal to count but maximum occurance
								$products_links->assign ("grouped",$group_product_id,$bundle_product_id);
                                $compatible_cat_id = $group_product_id.','.$compatible_cat_id;

							}
						}
					}
				}
				else{
					foreach($group_product as $group_product_id){
						$products_links->assign ("grouped",$group_product_id,$bundle_product_id);
                        $compatible_cat_id = $group_product_id.','.$compatible_cat_id;
					}
				}
                $check = null;

                $compatible_cat_id = rtrim( $compatible_cat_id ,',');
                $group_product_ids = explode(',',$compatible_cat_id);

                $prd_compatible_cat_id = null;

                foreach($group_product_ids as $cateids){

                    $select = $conn->select()
                        ->from($attrbvarTable,array('value'))
                        ->where('attribute_id =?',$groupAttributeId)
                        ->where('entity_type_id =?',$EntityTypeId)
                        ->where('store_id =?',$store_id)
                        ->where('entity_id =?',$cateids);

                    $getCompatibleCatid = $conn->fetchOne($select);


                    $prd_compatible_cat_id = $getCompatibleCatid.','.$prd_compatible_cat_id;
                }

                $prd_compatible_cat_id = rtrim($prd_compatible_cat_id, ',');

                $select = $conn->select()
                    ->from($attrbTable,array('value'))
                    ->where('attribute_id =?',$AttributeId)
                    ->where('entity_type_id =?',$EntityTypeId)
                    ->where('store_id =?',$store_id)
                    ->where('entity_id =?',$bundle_product_id);

                $check = $conn->fetchOne($select);

                if(empty($check))
                    $conn_write->insert($attrbTable, array('entity_type_id' => $EntityTypeId, 'attribute_id' => $AttributeId,'store_id'=> $store_id,'entity_id'=> $bundle_product_id,'value' => $prd_compatible_cat_id));
                else
			       $conn_write->update($attrbTable,array('value' => $prd_compatible_cat_id), array('entity_type_id =?' => $EntityTypeId, 'attribute_id =?' => $AttributeId,'store_id =?'=> $store_id,'entity_id =?'=> $bundle_product_id));
            }


        }

		return true;
	}
	
	/* Catalog Import handler
	//
	//
	*/
	public function MappingAutoAfterProductImport($affectedEntityIds){
	
		if (empty($affectedEntityIds)) {
            return;
        }
		foreach($affectedEntityIds as $id){

            $product_ids = null;

			$product = Mage::getModel('catalog/product')->load($id);
			
			$product_ids = $product->getCompatibleCatid();

			if($product->getTypeId() === 'simple' && strlen($product_ids)){// and getCompatibleCatid() != ''

                $childId = $product->getId(); //get simple product ID

                $products_links = Mage::getModel('catalog/product_link_api');

                $coreResource = Mage::getSingleton('core/resource');
                $conn = $coreResource->getConnection('core_read');
                $conn_write = $coreResource->getConnection('core_write');
                $RelationTable = $coreResource->getTableName('catalog/product_relation'); //get table name for catalog_product_relation
                $RealtionLinkTable = $coreResource->getTableName('catalog/product_link'); //get table name for catalog_product_link
                $select = $conn->select()
                    ->from($RealtionLinkTable, array('product_id'))
                    ->where('linked_product_id = ?', $childId)
                    ->where('link_type_id = ?', Mage_Catalog_Model_Product_Link::LINK_TYPE_GROUPED);

                $result = $conn->fetchCol($select); //get group product id (product_id) for linked product id (simple/bundle) called child

                foreach($result as $item_list){
                    $products_links->remove ("grouped",$item_list,$childId); //remove all link due to save or update
                    //delete all link which has link for child product(simple) and parent product id ($item_list) group
                    $conn_write->delete($RelationTable, array('child_id = ?' => $childId, 'parent_id = ?' => $item_list));

                }

				

                $map_prd_id_arr = explode(',',$product_ids);

                foreach($map_prd_id_arr as $item){

                    $get_product = Mage::getModel('catalog/product')->loadByAttribute('categoryid',$item);
                    if(!empty($get_product)){
                        $parentId = $get_product->getId(); //get group product ID

                        $products_links->assign ("grouped",$parentId,$childId); //insert new link only

                    }
                }


			}
			unset($product);
			
		}
		
		return true;

	}
}