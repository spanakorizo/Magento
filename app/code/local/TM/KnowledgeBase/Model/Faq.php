<?php
class TM_KnowledgeBase_Model_Faq extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('knowledgebase/faq');
    }

    /**
     *
     * @return TM_KnowledgeBase_Model_Faq
     */
    protected function _beforeDelete()
    {
        Mage::dispatchEvent(
            'knowledgebase_faq_delete_before', $this->_getEventData()
        );
        return parent::_beforeDelete();
    }

    /**
     *
     * @param   string $identifier
     * @return TM_Helpmate_Model_Ticket
     */
    public function loadByIdentifier($identifier)
    {
        $this->_getResource()->loadByIdentifier($this, $identifier);
        return $this;
    }
    
    public function getStores() 
    {
        // add stores 
        $stores = array();
        $rowset = Mage::getModel('knowledgebase/faq_store')
            ->getCollection()
            ->addFaqFilter($this->getId());
        foreach ($rowset as $row) {
            $stores[] = $row->getStoreId();
        }
        $this->setStores($stores);
        
        return $this->getData('stores');
    }
}