<?php

class TM_Helpmate_Model_Mysql4_Ticket extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the id refers to the key field in your database table.
        $this->_init('helpmate/ticket', 'id');
    }

    /**
     * Load ticket by number
     *
     * @param TM_Helpmate_Model_Ticket $ticket
     * @param string $number
     * @return TM_Helpmate_Model_Mysql4_Ticket
     * @throws Mage_Core_Exception
     */
    public function loadByNumber(TM_Helpmate_Model_Ticket $ticket, $number)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array($this->getIdFieldName()))
            ->where('number=:number');

        if ($id = $this->_getReadAdapter()->fetchOne($select, array('number' => $number))) {
            $this->load($ticket, $id);
        } else {
            $ticket->setData(array());
        }
        return $this;
    }

    /**
     *
     * @param TM_Helpmate_Model_Ticket $ticket
     * @return array
     */
    public function getVisitorInfo(TM_Helpmate_Model_Ticket $ticket)
    {
        $visitorId = $ticket->getVisitorId();

        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('log/visitor_info'))
            ->where('visitor_id=?', $visitorId)
        ;
        $row = $this->_getReadAdapter()->fetchRow($select);
        $ticket->setData('visitor_info', $row);
        return $row;
    }
}