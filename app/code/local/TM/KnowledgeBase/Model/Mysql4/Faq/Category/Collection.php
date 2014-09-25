<?php

class TM_KnowledgeBase_Model_Mysql4_Faq_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('knowledgebase/faq_category');
    }

    public function addFaqFilter($faqId)
    {
        if (!is_array($faqId)) {
            $faqId = array($faqId);
        }
        $this->getSelect()->where('main_table.faq_id IN (?)', $faqId);
        return $this;
    }
}