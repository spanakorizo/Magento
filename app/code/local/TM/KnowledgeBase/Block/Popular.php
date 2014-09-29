<?php
class TM_KnowledgeBase_Block_Popular extends Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        $collection = Mage::getModel('knowledgebase/faq')->getCollection()
            ->addEnableFilter()
            ->addStoreFilter()
            ->addCategoriesData()
            ->setRateOrder()
            ->addLimit(6)
//            ->getSelect()->limit(6)
            ;
        $this->setCollection($collection);
        return true;
    }
}