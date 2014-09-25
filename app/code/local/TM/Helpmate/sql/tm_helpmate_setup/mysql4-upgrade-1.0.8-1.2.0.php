<?php


/**
 * @var Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();


$installer->run("

    ALTER TABLE `{$this->getTable('tm_helpmate_theard')}` 
        ADD COLUMN `enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `department_id`;


");
        
$installer->endSetup();