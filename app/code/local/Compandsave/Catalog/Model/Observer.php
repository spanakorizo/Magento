<?php
class Compandsave_Catalog_Model_Observer
{

    public function catalog_product_save($observer)
    {
        $product = $observer->getProduct();

		Mage::getModel('compandsave_catalog/relation_backend')->MappingAutoAfterProductCreate($product);
        
        return true;

    }
	
	/* public function applyAllRulesOnBundleProduct($observer)
    {
        $product = $observer->getEvent()->getProduct();

        return true;

    } */
	
	public function CatalogRelations(Varien_Event_Observer $observer)
    {
        $adapter = $observer->getEvent()->getAdapter();
        $affectedEntityIds = $adapter->getAffectedEntityIds();
		
		Mage::getModel('compandsave_catalog/relation_backend')->MappingAutoAfterProductImport($affectedEntityIds);
        
		return true;
    }

}
