<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('compandsave_variable/brand');
if ($installer->getConnection()->isTableExists($tableName)){
    $installer->getConnection()->dropTable($tableName);
}
$table = $installer->getConnection()->newTable($tableName)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'auto_increment' => true,
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Entity ID')
    ->addColumn('brand_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
    ), 'Brand ID')
    ->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 2147483648, array(
        'nullable'  => false,
    ), 'Value')
    ->addColumn('visibility', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Visibility')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Status')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Creation Time')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
    ), 'Update Time')
    ->addIndex(
        $installer->getIdxName(
            array('compandsave_variable/brand'),
            array('brand_id'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('brand_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
    ->setComment('Compandsave Variable Brand Table');
$installer->getConnection()->createTable($table);