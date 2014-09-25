<?php
/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'couponcode', 'varchar(255) null');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'shipdate', 'timestamp null');
$installer->getConnection()->addColumn($installer->getTable('sales/order_item'), 'shipflag', 'char(1) null');
$installer->endSetup();