<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */


$installer->startSetup();


$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('tm_knowledgebase_faq')};
CREATE TABLE IF NOT EXISTS {$this->getTable('tm_knowledgebase_faq')} (
    `id` int(11) unsigned NOT NULL auto_increment,
    `title` VARCHAR(200) NULL,
    `meta_keywords` text NOT NULL,
    `meta_description` text NOT NULL,
    `content` text,
    `identifier` varchar(255) NOT NULL DEFAULT '',
    `author` mediumint(9) UNSIGNED DEFAULT NULL,
    `status` tinyint(1) NOT NULL default 1,
    `rate` int(11) unsigned NOT NULL,
    `created_at` datetime DEFAULT NULL,
    `modified_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    FULLTEXT KEY (title,content)
) ENGINE = MyISAM DEFAULT CHARSET=utf8;

--      CONSTRAINT `FK_LINK_USER_KNOWLEDGEBASE_FAQ` FOREIGN KEY (`author`)
--      REFERENCES {$this->getTable('admin_user')} (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE)
-- ENGINE = InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('tm_knowledgebase_category')};
CREATE  TABLE IF NOT EXISTS {$this->getTable('tm_knowledgebase_category')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` VARCHAR(45) NULL ,
  `identifier` varchar(255) NOT NULL DEFAULT '',
  `store_id` smallint(5) unsigned NOT NULL,
  `active` tinyint(1) NOT NULL default 1,
  `created_at` datetime NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_LINK_STORE_KNOWLEDGEBASE_CATEGORY` FOREIGN KEY (`store_id`)
        REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

-- DROP TABLE IF EXISTS {$this->getTable('tm_knowledgebase_faq_category')};
CREATE  TABLE IF NOT EXISTS {$this->getTable('tm_knowledgebase_faq_category')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `faq_id` int(11) unsigned NOT NULL ,
  `category_id` int(11) unsigned NOT NULL ,
  PRIMARY KEY (`id`) ,
--  CONSTRAINT `FK_LINK_FAQ_KNOWLEDGEBASE_FAQ_CATEGORY` FOREIGN KEY (`faq_id`)
--    REFERENCES {$this->getTable('tm_knowledgebase_faq')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_CATEGORY_KNOWLEDGEBASE_FAQ_CATEGORY` FOREIGN KEY (`category_id`)
    REFERENCES {$this->getTable('tm_knowledgebase_category')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

");



$installer->addAttribute('catalog_product', 'knowledgebase_faq', array(
    'group'             => 'KnowledgeBase',
    'label'             => 'KnowledgeBase Article',
    'type'              => 'text',
    'input'             => 'multiselect', //grid
    'default'           => '0',
    'class'             => '',
//    'backend'           => '',
    'frontend'          => '',

    'source'            => 'knowledgebase/product_attribute_faq_source',
    'backend'           => 'knowledgebase/product_attribute_faq_backend',

    'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible'           => true,
    'required'          => false,
    'user_defined'      => false,
    'searchable'        => false,
    'filterable'        => false,
    'comparable'        => false,
    'visible_on_front'  => false,
    'visible_in_advanced_search' => false,
    'unique'            => false

));

$installer->endSetup();

