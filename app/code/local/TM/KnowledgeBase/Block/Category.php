<?php
class TM_KnowledgeBase_Block_Category extends TM_KnowledgeBase_Block_List
{
    protected function _beforeToHtml()
    {
        $categoryIdentifier = $this->getRequest()->getParam('category', null);
        if (!empty($categoryIdentifier)) {
            $collection = $this->_getPreparedCollection();
            $this->setCollection($collection);
        }
        return true;
    }
}