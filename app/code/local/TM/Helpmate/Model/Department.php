<?php
class TM_Helpmate_Model_Department extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/department');
    }

    public function getOptionArray($status = null, $store = true)
    {
        return $this->_getResource()->getOptionArray($status, $store);
    }

    public function getGateway()
    {
        return Mage::getSingleton('helpmate/gateway')->load(
            $this->getGatewayId()
        );
    }

}