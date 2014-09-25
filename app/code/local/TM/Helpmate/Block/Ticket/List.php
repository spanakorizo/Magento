<?php
class TM_Helpmate_Block_Ticket_List extends Mage_Customer_Block_Account_Dashboard
{
    protected $_collection;

    public function count()
    {
        return $this->getCollection()->getSize();
    }

    public function getItems()
    {
        return $this->getCollection()->getItems();
    }

    public function getCollection()
    {
        return $this->_getCollection();
    }

    protected function _getCollection()
    {
        $customerId = (int) Mage::getSingleton('customer/session')->getCustomerId();
        if(!$this->_collection /*&& $this->getProductId() */) {
            $this->_collection = Mage::getModel('helpmate/ticket')
                ->getCollection()
                ->addCustomerFilter($customerId)
                ->addStoreFilter(array(0, Mage::app()->getStore()->getId()))
                ->addDepartmentName()
                ->addPriorityName()
                ->addStatusName()
//                ->setorder('created_at','DESC')
//                ->load()
                ;

//            $departments = Mage::getModel('helpmate/department')->getOptionArray();
//            $priorities  = Mage::getModel('helpmate/priority')->getOptionArray();
//            $statusses   = Mage::getModel('helpmate/status')->getOptionArray();
//
//            foreach ($this->_collection as &$ticket) {
//                $ticket->setDepartmentName($departments[$ticket->getDepartmentId()]);
//                $ticket->setPriority($priorities[$ticket->getPriority()]);
//                $ticket->setStatus($statusses[$ticket->getStatus()]);
//            }

        }
        return $this->_collection;
    }

    public function dateFormat($date)
    {
        return $this->formatDate($date, Mage_Core_Model_Locale::FORMAT_TYPE_SHORT);
    }

    /**
     * Prepare layout
     *
     * @return TM_Helpmate_Block_Ticket_List
     */
    protected function _prepareLayout()
    {
        $collection = $this->getCollection();
        if ($collection) {
            /** @var $toolbar Mage_Page_Block_Html_Pager */
            $toolbar = $this->getLayout()->createBlock(
                'page/html_pager', 'helpmate_ticket_list.toolbar'
            );
            $toolbar->setCollection($collection);
            $this->setChild('toolbar', $toolbar);
        }
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Get toolbar html
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }
}
