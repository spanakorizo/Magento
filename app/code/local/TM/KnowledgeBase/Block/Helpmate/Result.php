<?php
class TM_KnowledgeBase_Block_Helpmate_Result extends Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        $query = Mage::registry('knowledgebase_helpmate_result_query');
        if (null !== $query) {
            $this->setQuery(substr($query, 0 , 50) . '...');
        }
        $collection = Mage::registry('knowledgebase_helpmate_result_collection');
        if ($collection instanceof TM_KnowledgeBase_Model_Mysql4_Faq_Collection) {
            $this->setCollection($collection);
            return true;
        }
        return false;
    }
}