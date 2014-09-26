<?php
class TM_KnowledgeBase_Model_Category extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('knowledgebase/category');
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
}