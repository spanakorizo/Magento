<?php

class TM_Helpmate_Block_Ticket_New_Orders extends Mage_Core_Block_Html_Select
{

    public function __construct()
    {
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addFieldToSelect('*')
            ->addFieldToFilter('customer_id', Mage::getSingleton('customer/session')->getCustomer()->getId())
            ->addFieldToFilter('state', array('in' => Mage::getSingleton('sales/order_config')->getVisibleOnFrontStates()))
            ->setOrder('created_at', 'desc')
        ;
        if (0 === $collection->count()) {
            return;
        }
        $options = array(0 => '');
        foreach ($collection as $order) {
            $options[$order->getId()] = $this->__("Order") . ": " . $order->getRealOrderId() . " --- " . $this->__("Ordered") . ": " . $order->getCreatedAtFormated("%l") . " --- " . $this->__("Status") . ": " . $order->getStatusLabel();
        }

        $this->_options = $options;

        $this->setName('order_id')
            ->setId('order_id')
            ->setTitle(Mage::helper('directory')->__('Order'))
//            ->setClass('validate-select')
//            ->setValue(null)
            ;
    }

    protected function _beforeToHtml()
    {
        if (empty($this->_options)) {
            return false;
        }
        return parent::_beforeToHtml();
    }

    protected function _toHtml()
    {
        if (!Mage::getStoreConfig('helpmate/general/enabledOrder')) {
            return '';
        }
        $html = parent::_toHtml();
        return '<li class="wide">
            <label for="title" >' .
                Mage::helper('helpmate')->__('Choose related order if applicable') .
            '</label>
            <div class="input-box">' .
                $html .
            '</div>
        </li>';
    }
}
