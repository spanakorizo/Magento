<?php

class TM_Email_Model_Resource_Gateway_Transport extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the id refers to the key field in your database table.
        $this->_init('tm_email/gateway_transport', 'id');
    }

    public function getOptionArray()
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ;

        $rowset = array();
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row)
        {
            $rowset[$row['id']] = $row['name'];
        }

        return $rowset;
    }
}