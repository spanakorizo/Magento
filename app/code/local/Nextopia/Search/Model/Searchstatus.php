<?php
class Nextopia_Search_Model_Searchstatus
{
    public function toOptionArray()
    {
        return array(
            array('value'=> 0, 'label'=>Mage::helper('nsearch')->__('Disabled')),
            array('value'=> 1, 'label'=>Mage::helper('nsearch')->__('Enabled')),
        );
    }
}
