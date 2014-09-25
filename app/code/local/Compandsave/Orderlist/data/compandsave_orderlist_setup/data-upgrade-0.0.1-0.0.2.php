<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'affiliate_commissionable_value', 'decimal(12,4) NULL');
$installer->endSetup();