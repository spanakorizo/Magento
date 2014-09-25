<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales/order'),
    'private_notes',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'comment'   => 'Private Notes'
    )
);

$installer->endSetup();