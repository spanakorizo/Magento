<?php

    class TM_Email_Model_Resource_Queue_Queue extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the id refers to the key field in your database table.
        $this->_init('tm_email/queue_queue', 'queue_id');
    }

    public function getOptionArray()
    {

        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable())
            ;

        $rowset = array();
        foreach ($this->_getReadAdapter()->fetchAll($select) as $row)
        {
            $rowset[$row['queue_id']] = $row['queue_name'];
        }

        return $rowset;
    }
}