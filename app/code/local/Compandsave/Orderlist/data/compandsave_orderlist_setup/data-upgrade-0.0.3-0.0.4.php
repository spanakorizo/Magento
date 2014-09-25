<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();
$table_name = $installer->getTable('sales/order');
$installer->getConnection()->addColumn(
    $table_name,
    'printed',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable'  => false,
        'default' => 0 ,
        'comment'   => 'Printed'
    )
);
$installer->getConnection()->addColumn(
    $table_name,
    'shipped',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable'  => false,
        'default' => 0 ,
        'comment'   => 'shipped'
    )
);
$installer->getConnection()->addColumn(
    $table_name,
    'last_modified_by',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_INTEGER,
        'nullable'  => false,
        'default' => 0,
        'comment'   => 'Last Modified User'
    )
);

$installer->endSetup();