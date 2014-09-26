<?php
class TM_KnowledgeBase_Model_Faq_Store extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('knowledgebase/faq_store');
    }
}