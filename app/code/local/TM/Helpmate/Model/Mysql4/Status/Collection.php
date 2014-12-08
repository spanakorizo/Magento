<?php

class TM_Helpmate_Model_Mysql4_Status_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/status');
    }

    public function addEnabledFilter($status = true)
    {
        $this->getSelect()->where('main_table.status = ?', $status ? 1 : 0);
        return $this;
    }

    /**
     * @param strine $sortOrder [ASC|DESC]
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function setSortOrderOrder($sortOrder = self::SORT_ORDER_ASC)
    {
        $this->getSelect()->order('main_table.sort_order ' . $sortOrder);
        return $this;
    }
}