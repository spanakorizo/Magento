<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'old_order_id', 'int(11) NULL');
$installer->endSetup();