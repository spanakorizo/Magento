<?php
class TM_KnowledgeBase_Block_List extends TM_KnowledgeBase_Block_Abstract
{
    protected function _getPreparedCollection()
    {
        $collection = Mage::getModel('knowledgebase/faq')->getCollection()
            ->addEnableFilter()
            ->addStoreFilter()
            ->addCategoriesData()
            ->setRateOrder()
        ;

        $categoryIdentifier = $this->getCategoryIdentifier();
        if (empty($categoryIdentifier)) {
            $categoryIdentifier = $this->getRequest()->getParam('category', null);
        }
        if (!empty($categoryIdentifier)) {
            $category = Mage::getModel('knowledgebase/category')->loadByIdentifier($categoryIdentifier);
            if ($category) {
                $this->setCategory($category);
                $collection->addCategoryIdentifierFilter($categoryIdentifier);
            }
        }

//        $this->setCollection($collection);
        return $collection;
    }

    protected function _beforeToHtml()
    {
        $collection = $this->_getPreparedCollection();
        $this->setCollection($collection);
        return true;
    }
}