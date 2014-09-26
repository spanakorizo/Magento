<?php

/**
 * @var Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

$installer->run("
    
    ALTER TABLE {$this->getTable('tm_helpmate_ticket')}  
        ADD COLUMN `field0` TEXT  DEFAULT NULL AFTER `order_id`;
    ALTER TABLE {$this->getTable('tm_helpmate_ticket')} 
        ADD COLUMN `field1` TEXT  DEFAULT NULL AFTER `field0`;
    ALTER TABLE {$this->getTable('tm_helpmate_ticket')} 
        ADD COLUMN `field2` TEXT  DEFAULT NULL AFTER `field1`;

");

$installer->endSetup();