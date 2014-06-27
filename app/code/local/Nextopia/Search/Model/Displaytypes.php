<?php
class Nextopia_Search_Model_Displaytypes
{
    public function toOptionArray()
    {
        return array(
            array('value'=> 'Grid', 'label'=>Mage::helper('nsearch')->__('Grid')),
            array('value'=> 'List', 'label'=>Mage::helper('nsearch')->__('List')),
        );
    }
}
