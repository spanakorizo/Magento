<?php


/**
 * @var Mage_Core_Model_Resource_Setup
 */
$installer = $this;

$installer->startSetup();

$type = 'mediumint(9) UNSIGNED';
$column = $installer->getConnection()->fetchAll(
    "SHOW COLUMNS FROM {$this->getTable('admin_user')} WHERE field = 'user_id'"
);
if (isset($column[0]['Type'])) {
    $type = $column[0]['Type'];
}

//$type = Varien_Db_Ddl_Table::TYPE_INTEGER;

$installer->run("
    ALTER TABLE `{$this->getTable('tm_helpmate_ticket')}`
        ADD COLUMN `user_id` {$type} DEFAULT NULL AFTER `department_id`,
        ADD CONSTRAINT `FK_LINK_USER_HELPMATE_TICKET` FOREIGN KEY `FK_LINK_USER_HELPMATE_TICKET` (`user_id`)
        REFERENCES `{$this->getTable('admin_user')}` (`user_id`)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

  --  ALTER TABLE `{$this->getTable('tm_helpmate_ticket')}`
  --      MODIFY COLUMN `user_id` {$type} DEFAULT NULL;

    ALTER TABLE `{$this->getTable('tm_helpmate_theard')}`
        MODIFY COLUMN `user_id` {$type} DEFAULT NULL;


    ALTER TABLE `{$this->getTable('tm_helpmate_department_user')}`
        MODIFY COLUMN `user_id` {$type} NOT NULL;

");

$installer->endSetup();