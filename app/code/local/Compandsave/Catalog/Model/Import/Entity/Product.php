<?php

class Compandsave_Catalog_Model_Import_Entity_Product extends Mage_ImportExport_Model_Import_Entity_Product{

    const COL_MAPPING_SKU      = 'mapping_sku';
    /**
     * Gather and save information about product links.
     * Must be called after ALL products saving done.
     *
     * @return Mage_ImportExport_Model_Import_Entity_Product
     */
    p
    protected function _saveSimpleLinks()
    {

        while ($bunch = $this->_dataSourceModel->getNextBunch()) {
            $productIds   = array();
            $linkRows     = array();
            $positionRows = array();

            foreach ($bunch as $rowNum => $rowData) {
                $this->_filterRowData($rowData);
                if (!$this->isRowAllowedToImport($rowData, $rowNum)) {
                    continue;
                }
                if (self::SCOPE_DEFAULT == $this->getRowScope($rowData)) {
                    $sku = $rowData[self::COL_SKU];
                    $product_in = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku);
                }


                if($product_in->getTypeId() === 'simple' ){// and getMappingSku() != ''
                    if($this->getBehavior() == Mage_ImportExport_Model_Import::BEHAVIOR_APPEND){
                        //Mage::register('product', $product);

                        $childId = $product_in->getId(); //get simple product ID

                        $products_links = Mage::getModel('catalog/product_link_api');

                        $coreResource = Mage::getSingleton('core/resource');
                        $conn = $coreResource->getConnection('core_read');
                        $select = $conn->select()
                            ->from($coreResource->getTableName('catalog/product_relation'), array('parent_id'))
                            ->where('child_id = ?', $childId);
                        $result = $conn->fetchCol($select);
                        foreach($result as $item_list){
                            $products_links->remove ("grouped",$item_list,$childId); //remove all link due to save or update
                        }


                        if( $rowData[self::COL_MAPPING_SKU] != ''){
                            $prd_id = explode(',',$rowData[self::COL_MAPPING_SKU]);
                            foreach($prd_id as $item){

                                $get_product = Mage::getModel('catalog/product')->loadByAttribute('sku',$item);
                                if(!empty($get_product)){
                                    $parentId = $get_product->getId(); //get group product ID

                                    $products_links->assign ("grouped",$parentId,$childId); //insert new link only

                                }
                            }

                        }
                    }
                    elseif{
                        if($this->getBehavior() == Mage_ImportExport_Model_Import::BEHAVIOR_DELETE){
                            $childId = $product_in->getId(); //get simple product ID

                            $products_links = Mage::getModel('catalog/product_link_api');

                            $coreResource = Mage::getSingleton('core/resource');
                            $conn = $coreResource->getConnection('core_read');
                            $select = $conn->select()
                                ->from($coreResource->getTableName('catalog/product_relation'), array('parent_id'))
                                ->where('child_id = ?', $childId);
                            $result = $conn->fetchCol($select);
                            foreach($result as $item_list){
                                $products_links->remove ("grouped",$item_list,$childId); //remove all link due to save or update
                            }
                        }
                    }

                }
            }

        }
        return $this;
    }

    /**
     * Create Product entity from raw data.
     *
     * @throws Exception
     * @return bool Result of operation.
     */
    protected function _importData()
    {
        if (Mage_ImportExport_Model_Import::BEHAVIOR_DELETE == $this->getBehavior()) {
            $this->_deleteProducts();
        } else {
            $this->_saveProducts();
            $this->_saveStockItem();
            $this->_saveLinks();
            $this->_saveSimpleLinks();
            $this->_saveCustomOptions();
            foreach ($this->_productTypeModels as $productType => $productTypeModel) {
                $productTypeModel->saveData();
            }
        }
        Mage::dispatchEvent('catalog_product_import_finish_before', array('adapter' => $this));
        return true;
    }

}