<?php
class Nextopia_Search_Model_Boolean
{
    public function toOptionArray()
    {
        return array(
            array('value'=> 'True', 'label'=>Mage::helper('nsearch')->__('True')),
            array('value'=>'False','label'=>Mage::helper('nsearch')->__('False')),
        );
    }
}
