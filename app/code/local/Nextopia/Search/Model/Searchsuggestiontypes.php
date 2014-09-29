<?php
class Nextopia_Search_Model_Searchsuggestiontypes
{
    public function toOptionArray()
    {
        return array(
            array('value'=> 0, 'label'=>Mage::helper('nsearch')->__('Off')),
            array('value'=> 1, 'label'=>Mage::helper('nsearch')->__('Only when no matches')),
            array('value'=> 2, 'label'=>Mage::helper('nsearch')->__('Always')),
        );
    }

}
