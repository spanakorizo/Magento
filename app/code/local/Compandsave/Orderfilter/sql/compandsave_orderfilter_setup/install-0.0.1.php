<?php
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('compandsave_orderfilter/orderfilter');
if ($installer->getConnection()->isTableExists($tableName)){
    $installer->getConnection()->dropTable($tableName);
}
$table = $installer->getConnection()->newTable($tableName)
    ->addColumn('entity_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null,
        array(
            'unsigned' => true,
            'nullable' => false,
            'primary' => true,
            'identity' => true
        ), 'ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, '255',
        array(
            'nullable' => false,
            'default' => ''
        ), 'Filter Name')
    ->addColumn('filter_sql', Varien_Db_Ddl_Table::TYPE_TEXT, '64K',
        array(
            'nullable' => true,
            'default' => ''
        ), 'Filter Sql')
    ->addColumn('created_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null,
        array(
            'nullable' => false,
        ), 'Created At')
    ->addColumn('updated_at', Varien_Db_Ddl_Table::TYPE_DATETIME, null,
        array(
            'nullable' => false,
        ), 'Modified At')
    ->addIndex(
        $installer->getIdxName($tableName, array('name')),
        array('name'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE)
    )
    ->setComment('Filter Table Entity');

$installer->getConnection()->createTable($table);

$installer->endSetup();