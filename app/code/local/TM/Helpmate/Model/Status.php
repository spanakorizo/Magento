<?php

class TM_Helpmate_Model_Status extends Varien_Object
{
    const STATUS_OPEN     = 1;
    const STATUS_REPLIED  = 2;
    const STATUS_ONHOLD   = 3;
    const STATUS_CLOSE	  = 4;

    static public function getOptionArray()
    {
        return array(
            self::STATUS_OPEN    => Mage::helper('helpmate')->__('Open'),
            self::STATUS_REPLIED => Mage::helper('helpmate')->__('Replied'),
            self::STATUS_ONHOLD  => Mage::helper('helpmate')->__('Onhold'),
            self::STATUS_CLOSE   => Mage::helper('helpmate')->__('Closed')
        );
    }

    public function getOptionTitle($optionId)
    {
        $options = $this->getOptionArray();
        return (isset($options[$optionId]) ? $options[$optionId] : 'Undefined');
    }
}