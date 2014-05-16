<?php
class Compandsave_Catalog_Model_Product_Type_Grouped extends Mage_Catalog_Model_Product_Type_Grouped
{
    public function getAssociatedProducts($product = null)
    {
        if (!$this->getProduct($product)->hasData($this->_keyAssociatedProducts)) {
            $associatedProducts = array();

            if (!Mage::app()->getStore()->isAdmin()) {
                $this->setSaleableStatus($product);
            }

            $collection = $this->getAssociatedProductCollection($product)
                ->addAttributeToSelect('*')
                //->addFilterByRequiredOptions()
                ->setPositionOrder()
                ->addStoreFilter($this->getStoreFilter($product))
                ->addAttributeToFilter('status', array('in' => $this->getStatusFilters($product)));

            foreach ($collection as $item) {
                $associatedProducts[] = $item;
            }

            $this->getProduct($product)->setData($this->_keyAssociatedProducts, $associatedProducts);
        }
        return $this->getProduct($product)->getData($this->_keyAssociatedProducts);
    }

    
}