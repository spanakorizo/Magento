<?php
class Nextopia_Search_Model_LoadMethod
{
    public function toOptionArray()
    {
        return array(
            array('value'=> 'Product ID', 'label'=>Mage::helper('nsearch')->__('Product ID')),
            array('value'=>'Sku','label'=>Mage::helper('nsearch')->__('Sku')),
        );
    }
}
