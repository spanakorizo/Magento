<?php

/**
 * @var Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

$installer->run("

ALTER TABLE {$this->getTable('tm_helpmate_ticket')} 
    MODIFY COLUMN `number` VARCHAR(32) NOT NULL;

");

$installer->endSetup();