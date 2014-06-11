<?php

class TM_Helpmate_Model_Mysql4_Gateway extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the id refers to the key field in your database table.
        $this->_init('helpmate/gateway', 'id');
    }

    public function getOptionArray()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ;

        $rowset = array();
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row) {
            $rowset[$row['id']] = $row['name'];
        }

        return $rowset;
    }
}