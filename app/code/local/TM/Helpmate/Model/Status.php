<?php

class TM_Helpmate_Model_Status extends Mage_Core_Model_Abstract
{
    const STATUS_OPEN     = 1;
    const STATUS_REPLIED  = 2;
    const STATUS_ONHOLD   = 3;
    const STATUS_CLOSE	  = 4;

    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/status');
    }

    static public function getOptionArray()
    {
        $collection = Mage::getModel('helpmate/status')->getCollection()
            ->setSortOrderOrder()
            ->addEnabledFilter();

        $options = array();
        foreach ($collection as $item) {
            $name = Mage::helper('helpmate')->__($item->getName());
//            if (0 == intval($item->getStatus())) {
//                continue;
//                //$name .= ' (' .  Mage::helper('helpmate')->__('Disabled') . ')';
//            }
            $options[$item->getId()] = $name;
        }
        return $options;
    }

    static public function getSystemOptionArray()
    {
        return array(
            self::STATUS_OPEN    => Mage::helper('helpmate')->__('Open'),
            self::STATUS_REPLIED => Mage::helper('helpmate')->__('Replied'),
            self::STATUS_ONHOLD  => Mage::helper('helpmate')->__('Onhold'),
            self::STATUS_CLOSE   => Mage::helper('helpmate')->__('Closed')
        );
    }

    static public function isSystemOption($status)
    {
        $status = (int) $status;
        return in_array($status, array_keys(self::getSystemOptionArray()));
    }

    public function getOptionTitle($optionId)
    {
        $options = $this->getOptionArray();
        return (isset($options[$optionId]) ? $options[$optionId] : 'Undefined');
    }

    /**
     * Processing object before save data
     *
     * @return Mage_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $data = $this->getData();
        if (empty($data['id'])) {
            unset($data['id']);
        }
        if (TM_Helpmate_Model_Status::isSystemOption($data['id'])) {
            $data['status'] = true;
        }
        $this->setData($data);
        return parent::_beforeSave();
    }
}