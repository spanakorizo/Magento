<?php

class TM_Helpmate_Model_Mysql4_Department extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the id refers to the key field in your database table.
        $this->_init('helpmate/department', 'id');
    }

    /**
     *
     * @param bool $status
     * @return type 
     */
    public function getOptionArray($status = null, $store = true)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ;
        
        if (null !== $status) {
            $select->where('active = ?', (int) $status);
        }
        
        if (true === $store) {
            $store = (int) Mage::app()->getStore()->getId();
        }
        
        if (null !== $store) {
            $select->where('store_id = ?  OR store_id = 0', $store);
        }

        $rowset = array();
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $rowset[$row['id']] = $row['name'];
        }
        
        return $rowset;
    }   
}