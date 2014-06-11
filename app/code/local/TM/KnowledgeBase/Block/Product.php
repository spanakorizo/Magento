<?php
class TM_KnowledgeBase_Block_Product extends Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        $product = Mage::registry('current_product');

        if ($product instanceof Mage_Catalog_Model_Product) {

            $faqIds = $product->getData('knowledgebase_faq');

            $collection = Mage::getModel('knowledgebase/faq')->getCollection()
                ->addIdFilter($faqIds)
                ->addEnableFilter()
                ->addStoreFilter()
                ->addCategoriesData()
                ->setRateOrder()
            ;
            $this->setCollection($collection);
            return false;
        }

        return parent::_beforeToHtml();
    }
}