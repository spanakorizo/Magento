<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;

$installer = $this;

$installer->getConnection()->addColumn(
    $installer->getTable('compandsave_variable/coupon'),
    'expiry_date',
    array(
        'type'      => Varien_Db_Ddl_Table::TYPE_DATE,
        'nullable'  => true,
		'comment'   => 'Expiry Date'
    )
);
?>