<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('tm_knowledgebase_faq')}
        ADD COLUMN `sort_order` INT NOT NULL DEFAULT 0 AFTER `modified_at`;

    ALTER TABLE {$this->getTable('tm_knowledgebase_category')}
        ADD COLUMN `sort_order` INT NOT NULL DEFAULT 0 AFTER `created_at`;

    CREATE TABLE IF NOT EXISTS {$this->getTable('tm_knowledgebase_faq_store')} (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `faq_id` int(11) unsigned NOT NULL,
        `store_id` smallint(5) unsigned NOT NULL,
        PRIMARY KEY (`id`),
        KEY `FK_LINK_STORE_KNOWLEDGEBASE_FAQ_STORE` (`store_id`),
        CONSTRAINT `FK_LINK_STORE_KNOWLEDGEBASE_FAQ_STORE` FOREIGN KEY (`store_id`) REFERENCES {$this->getTable('core_store')} (`store_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

");

$collection = Mage::getModel('knowledgebase/faq')->getCollection();
$default = 0;
foreach ($collection as $row) {
    Mage::getModel('knowledgebase/faq_store')
        ->setFaqId($row->getId())
        ->setCategoryId($default)
        ->save();
}

$installer->endSetup();