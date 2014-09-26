<?php
class TM_Helpmate_Model_Theard extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/theard');
    }

    public function getTicket()
    {
        return Mage::getModel('helpmate/ticket')->load(
            $this->getTicketId()
        );
    }
}