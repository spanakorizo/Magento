<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = Mage::getResourceModel('catalog/setup', 'default_setup');
$installer->startSetup();

$installer->getConnection()->addColumn(
    $installer->getTable('sales_flat_order'),
    'autoship_label',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable'  => true,
        'comment'   => 'autoship_label'
    )
);



$installer->endSetup();
?>