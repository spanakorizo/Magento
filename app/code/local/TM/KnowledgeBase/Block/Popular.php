<?php
class TM_KnowledgeBase_Block_Popular extends TM_KnowledgeBase_Block_List
{
    protected function _beforeToHtml()
    {
        $limit = $this->getLimit();
        if (empty($limit)) {
            $limit = 6;
        }
        $collection = $this->_getPreparedCollection()
            ->addLimit($limit)
//            ->getSelect()->limit(6)
            ;
        $this->setCollection($collection);
        return true;
    }
}