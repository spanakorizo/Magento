<?php
class TM_KnowledgeBase_Block_Result extends TM_KnowledgeBase_Block_List
{
    protected function _beforeToHtml()
    {
        $query = $this->getRequest()->getParam('q', null);
        $this->setQuery(strip_tags($query));

        if (empty($query)) {
            return false;
        }

        $collection = $this->_getPreparedCollection()
            ->addMatchSearchQuery($query)
        ;
        if (!$collection->count()) {
            $collection = $this->_getPreparedCollection()
                ->addSearchQuery($query)
            ;
        }

        $this->setCollection($collection);
        return true;
    }
}