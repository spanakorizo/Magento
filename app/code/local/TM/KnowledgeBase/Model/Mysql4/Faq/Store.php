<?php

class TM_KnowledgeBase_Model_Mysql4_Faq_Store extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the id refers to the key field in your database table.
        $this->_init('knowledgebase/faq_store', 'id');
    }
}