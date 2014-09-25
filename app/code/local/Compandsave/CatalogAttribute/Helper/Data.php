<?php

class Compandsave_CatalogAttribute_Helper_Data
    extends Mage_Core_Helper_Abstract
{


    public function getProduct()
    {
        if (!Mage::registry('product')) {
           // return Mage::getModel('catalog/product');
        }
        return Mage::registry('product');
    }
	
	public function getCategories()
    {
        if (!Mage::registry('current_category')) {
           // return Mage::getModel('catalog/category');
        }
        return Mage::registry('current_category');
    }
	
}