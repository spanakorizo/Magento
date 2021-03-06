<?php

class TM_Helpmate_Model_Mysql4_Department_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/department');
    }

    public function addActiveFilter($active = 1)
    {
        $this->getSelect()->where('main_table.active=?', $active);
        return $this;
    }

    public function addGatewayIdFilter($gatewayId)
    {
        $this->getSelect()->where('main_table.gateway_id=?', $gatewayId);
        return $this;
    }

    public function addGatewayData()
    {
        $modelGateway = Mage::getModel('tm_email/gateway_storage');
        foreach ($this as $row) {
            $gateway = $modelGateway->load(
                $row->getGatewayId()
            );
            $row->setData('gateway', $gateway);
        }
        return $this;
    }
}