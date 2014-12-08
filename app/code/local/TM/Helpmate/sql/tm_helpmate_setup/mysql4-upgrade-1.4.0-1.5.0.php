<?php


/**
 * @var Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

$table = $installer->getConnection()
    ->newTable($installer->getTable('tm_helpmate_status'))
    ->addColumn('id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'identity'  => true,
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
        ), 'ID')
    ->addColumn('name', Varien_Db_Ddl_Table::TYPE_TEXT, 50, array(
        'nullable'  => true,
        'default'   => null,
        ), 'Name')
    ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'default'   => '0',
        ), 'Status')
    ->addColumn('sort_order', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'default'   => '0',
        ), 'Sort order')
    ->setComment('Statuses Table');
$installer->getConnection()->createTable($table);


$defaultStatuses  = array(
    TM_Helpmate_Model_Status::STATUS_OPEN    => Mage::helper('helpmate')->__('Open'),
    TM_Helpmate_Model_Status::STATUS_REPLIED => Mage::helper('helpmate')->__('Replied'),
    TM_Helpmate_Model_Status::STATUS_ONHOLD  => Mage::helper('helpmate')->__('Onhold'),
    TM_Helpmate_Model_Status::STATUS_CLOSE   => Mage::helper('helpmate')->__('Closed')
);

$sortOrder = 10;
foreach ($defaultStatuses as $id => $name) {

    $installer->getConnection()->insert($installer->getTable('helpmate/status'), array(
        'id'         => $id,
        'name'       => $name,
        'status'     => true,
        'sort_order' => $sortOrder
    ));
    $sortOrder += 10;
}

/**
 * Add foreign key
 */
$installer->getConnection()->addForeignKey(
    $installer->getFkName(
        'tm_helpmate_ticket', 'status', 'tm_helpmate_status', 'id'
    ),
    $installer->getTable('tm_helpmate_ticket'),
    'status',
    $installer->getTable('tm_helpmate_status'),
    'id',
    Varien_Db_Adapter_Interface::FK_ACTION_NO_ACTION
);


$installer->endSetup();