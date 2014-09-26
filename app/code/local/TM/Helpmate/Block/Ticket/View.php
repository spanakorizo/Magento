<?php
class TM_Helpmate_Block_Ticket_View extends Mage_Customer_Block_Account_Dashboard
{
//    protected $_ticket;
    protected function _beforeToHtml()
    {
        $ticketNumber = $this->getRequest()->getParam('ticket', 0);
        $ticket = Mage::getModel('helpmate/ticket')->loadByNumber($ticketNumber);
        $customerId = Mage::getSingleton('customer/session')->getCustomerId();
        if ($ticket->getCustomerId() !== $customerId) {
//            Mage::getSingleton('core/session')->addError(
//                'Maybe your need login'
//            );
            return false;
        }

        $departments = Mage::getModel('helpmate/department')->getOptionArray();
        $priorities  = Mage::getModel('helpmate/priority')->getOptionArray();
        $statusses   = Mage::getModel('helpmate/status')->getOptionArray();

        $ticket->setDepartmentName($departments[$ticket->getDepartmentId()]);
        $ticket->setPriority($priorities[$ticket->getPriority()]);
        $ticket->setStatus($statusses[$ticket->getStatus()]);

        $rowset = Mage::getModel('helpmate/theard')->getCollection()
            ->addEnabledFilter()
            ->addTicketFilter($ticket->getId())
            ;
        $theards = array();
        foreach ($rowset as $row) {
            $theards[] = $row->toArray();
        }

        $ticket->setTheards($theards);
        $this->setTicket($ticket);
    }


    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardOwnerTitle(array $theard)
    {
        if (null === $theard['user_id']) {
            return $this->helper('helpmate')->__('User') . ' ' .
                Mage::getModel('customer/customer')
                    ->load($this->getTicket()->getCustomerId())
                    ->getName();
        }

        return $this->helper('helpmate')->__('Admin') . ' ' .
            Mage::getModel('admin/user')
                ->load($theard['user_id'])
                ->getName();
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardCreatedAt(array $theard, $dateType = 'date', $format = 'medium')
    {
        if (!isset($theard['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($theard['created_at'], $format);
        }
        return $this->helper('core')->formatTime($theard['created_at'], $format);
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardModifiedAt(array $theard, $dateType = 'date', $format = 'medium')
    {
        if (!isset($theard['created_at'])) {
            return '';
        }
        if ('date' === $dateType) {
            return $this->helper('core')->formatDate($theard['modified_at'], $format);
        }
        return $this->helper('core')->formatTime($theard['modified_at'], $format);
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardStatus(array $theard)
    {
        return (isset($theard['status']) ?
            (Mage::getModel('helpmate/status')->getOptionTitle($theard['status'])) : '');
    }

    /**
     *
     * @param array $theard
     * @return string
     */
    public function getTheardDepartment(array $theard)
    {
        return Mage::getModel('helpmate/department')
            ->load($theard['department_id'])
            ->getName();
    }

    public function getTheardPriority(array $theard)
    {
        return (isset($theard['priority']) ?
            (Mage::getModel('helpmate/priority')->getOptionTitle($theard['priority'])) : '');
    }

    public function getTheardText(array $theard)
    {
        return (isset($theard['text']) ? $theard['text'] : '');
    }

    public function getTheardFileUrl(array $theard)
    {
        $path = Mage::getBaseUrl('media') . 'helpmate' . DS;
        $files = array_filter(explode(';', $theard['file']));

        foreach ($files as &$file) {
            $file = $path . $file;
        }

        return $files;
    }

    public function getViewOrderUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $orderId));
    }
}