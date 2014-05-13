<?php
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer = $this;
$installer->startSetup();
$tableName = $installer->getTable('compandsave_variable/coupon');
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
	->addColumn('entity_type_id', Varien_Db_Ddl_Table::TYPE_TEXT, 32, array(
        'nullable'  => false,
        'default'   => 'Coupon',
        ), 'Entity Type Id')
	->addColumn('value', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => false,
        ), 'Value')
	->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, 255, array(
		'nullable'  => true,
        ), 'Description')
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
	->addIndex($installer->getIdxName('compandsave_variable/coupon', array('entity_type_id')),
        array('entity_type_id'))
	->addIndex(
        $installer->getIdxName(
            array('compandsave_variable/coupon'),
            array('entity_type_id','value'),
            Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE
        ),
        array('entity_type_id','value'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
	->setComment('Compandsave Variable Table');
$installer->getConnection()->createTable($table);