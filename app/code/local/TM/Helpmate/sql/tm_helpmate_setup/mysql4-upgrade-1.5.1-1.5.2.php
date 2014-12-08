<?php


/**
 * @var Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

$installer->getConnection()->addColumn(
    $this->getTable('tm_helpmate_ticket'), 'visitor_id', "int(11) unsigned DEFAULT NULL"
);

$installer->endSetup();