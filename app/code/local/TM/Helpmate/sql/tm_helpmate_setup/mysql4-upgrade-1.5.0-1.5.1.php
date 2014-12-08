<?php


/**
 * @var Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

$conn = $installer->getConnection();

$select = $conn
    ->select()
    ->from($this->getTable('tm_helpmate_gateway'))
;

$gateways = $conn->fetchAll($select);

foreach ($gateways as $gateway) {
    $gatewayId = $gateway['id'];
    unset($gateway['id']);
    $storage = Mage::getModel('tm_email/gateway_storage');
    $storage->setData($gateway);
    $storage->save();

    $collection = Mage::getModel('helpmate/department')->getCollection()
        ->addGatewayIdFilter($gatewayId)
    ;

    foreach ($collection as $department) {
        $department->setGatewayId($storage->getId());
        $department->save();
    }
}

$conn->dropForeignKey(
    $installer->getTable('tm_helpmate_department'),
    'FK_LINK_GATEWAY_HELPMATE_DEPARTMENT'
);
$conn->dropKey(
    $installer->getTable('tm_helpmate_department'),
    'FK_LINK_GATEWAY_HELPMATE_DEPARTMENT'
);

$conn->addForeignKey(
    $installer->getFkName('tm_helpmate_department', 'gateway_id', 'tm_email/gateway_storage', 'id'),
    $installer->getTable('tm_helpmate_department'),
    'gateway_id',
    $installer->getTable('tm_email/gateway_storage'),
    'id'
);

$conn->dropTable($installer->getTable('tm_helpmate_gateway'));

$installer->endSetup();