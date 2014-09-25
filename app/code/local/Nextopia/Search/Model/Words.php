<?php
class Nextopia_Search_Model_Words
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('nsearch')->__('Hello')),
            array('value'=>2, 'label'=>Mage::helper('nsearch')->__('Goodbye')),
            array('value'=>3, 'label'=>Mage::helper('nsearch')->__('Yes')),
            array('value'=>4, 'label'=>Mage::helper('nsearch')->__('No')),
            array('value'=>5, 'label'=>Mage::helper('nsearch')->__('Maybe')),
        );
    }
}
