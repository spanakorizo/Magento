<?php

class TM_Helpmate_Block_Ticket_Theard_New extends Mage_Customer_Block_Account_Dashboard // Mage_Core_Block_Template
{
    protected function _beforeToHtml()
    {
        $ticketNumber = $this->getRequest()->getParam('ticket', 0);
        $ticket = Mage::getModel('helpmate/ticket')->loadByNumber($ticketNumber);
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if ($ticket->getCustomerId() !== $customerId) {
            return false;
        }
        $this->setTicketNumber($ticketNumber);
    }

    public function getAction()
    {
        return $this->getUrl('*/*/saveTheard');
    }
}
