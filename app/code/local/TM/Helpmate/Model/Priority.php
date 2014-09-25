<?php

class TM_Helpmate_Model_Priority extends Varien_Object
{
    const PRIORITY_LOW       = 1;
    const PRIORITY_MEDIUM    = 2;
    const PRIORITY_HIGHT     = 3;
    const PRIORITY_EMERGENCY = 4;
    const PRIORITY_CRITICAL  = 5;

    static public function getOptionArray()
    {
        return array(
            self::PRIORITY_LOW       => Mage::helper('helpmate')->__('Low'),
            self::PRIORITY_MEDIUM    => Mage::helper('helpmate')->__('Medium'),
            self::PRIORITY_HIGHT     => Mage::helper('helpmate')->__('High'),
            self::PRIORITY_EMERGENCY => Mage::helper('helpmate')->__('Emergency'),
            self::PRIORITY_CRITICAL  => Mage::helper('helpmate')->__('Critical')
        );
    }

    public function getOptionTitle($optionId)
    {
        $options = $this->getOptionArray();
        return (isset($options[$optionId]) ? $options[$optionId] : 'Undefined');
    }
}