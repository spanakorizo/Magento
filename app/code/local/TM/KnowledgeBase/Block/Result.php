<?php
class TM_KnowledgeBase_Block_Result extends Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        $query = $this->getRequest()->getParam('q', null);
        $this->setQuery(strip_tags($query));
        if (null !== $query) {
            $collection = Mage::getModel('knowledgebase/faq')->getCollection()
                ->addMatchSearchQuery($query);

            $category = $this->getRequest()->getParam('category', null);

            if (null !== $category) {
                $collection->addCategoryIdentifierFilter($category);
            }
            $collection->addEnableFilter()
                ->addStoreFilter()
                ->addCategoriesData()
                ->setRateOrder()
                ;
            if (!$collection->count()) {
                $collection = Mage::getModel('knowledgebase/faq')->getCollection()
                    ->addSearchQuery($query);

                $category = $this->getRequest()->getParam('category', null);

                if (null !== $category) {
                    $collection->addCategoryIdentifierFilter($category);
                }
                $collection->addEnableFilter()
                    ->addStoreFilter()
                    ->addCategoriesData()
                    ->setRateOrder()
                    ;
            }
            
            $this->setCollection($collection);
            return true;
        }
        return false;
    }
}