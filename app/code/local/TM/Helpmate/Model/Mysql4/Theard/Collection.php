<?php

class TM_Helpmate_Model_Mysql4_Theard_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/theard');
    }

    public function addTicketFilter($ticketId)
    {
        $this->getSelect()->where('main_table.ticket_id=?', $ticketId);
        return $this;
    }

    public function addMessageIdFilter($messageId)
    {
        $this->getSelect()->where('main_table.message_id=?', $messageId);
        return $this;
    }
    
    public function addEnabledFilter($status = 1)
    {
        $this->getSelect()->where('main_table.enabled = ?', $status);
        return $this;
    }
}