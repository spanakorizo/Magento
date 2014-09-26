

<?php
$installer = $this;
$installer->startSetup();
$table = $installer->getConnection()
    ->newTable($installer->getTable('compandsave_functions/customerfilter'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'entity_id')
    ->addColumn('FirstName', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        ), 'FirstName')
    ->addColumn('LastName', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        ), 'LastName')
    ->addColumn('CustomerID', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        ), 'CustomerID')
    ->addColumn('EmailAddress', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        ), 'EmailAddress')
    ->addColumn('ShippingAddress', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        ), 'ShippingAddress')
    ->addColumn('BillingAddress', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
        ), 'BillingAddress')
    ->addColumn('Company', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
        ), 'Company')
    ->addColumn('Telephone', Varien_Db_Ddl_Table::TYPE_TEXT, null, array(
        'nullable'  => true,
        ), 'Telephone');
$installer->getConnection()->createTable($table);
$table = $installer->getConnection()
    ->newTable($installer->getTable('compandsave_functions/conditionfilter'))
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'entity_id')
    ->addColumn('Condition', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        ), 'Condition')
    ->addColumn('Key', Varien_Db_Ddl_Table::TYPE_VARCHAR, null, array(
        'nullable'  => true,
        ), 'Key');
$installer->getConnection()->createTable($table);
$installer->endSetup();