<?php

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
    
-- DROP TABLE IF EXISTS {$this->getTable('tm_helpmate_ticket')};
  CREATE  TABLE IF NOT EXISTS {$this->getTable('tm_helpmate_ticket')} (
      `id` int(11) unsigned NOT NULL auto_increment,
      `number` varchar(8) NOT NULL,
      `customer_id` int(10) unsigned DEFAULT NULL,
      `email` varchar(128) NOT NULL,
      `status` tinyint(1) NOT NULL default 1,
      `title` VARCHAR(45) NULL ,
      `priority` tinyint(1) NOT NULL default 1,
      `created_at` datetime NULL,
      `modified_at` datetime NULL,
      `department_id` int(11) unsigned NOT NULL,
      `store_id` smallint(5) unsigned NOT NULL,
      `notes` VARCHAR(255) NULL ,
      `order_id` int(10) unsigned DEFAULT NULL,
      -- `lock` tinyint(1) NOT NULL default 1,
      PRIMARY KEY (`id`) ,
      INDEX `FK_LINK_DEPARTMENT_HELPMATE_TICKET` (`department_id` ASC) ,
      CONSTRAINT `FK_LINK_CUSTOMER_HELPMATE_TICKET` FOREIGN KEY (`customer_id`)
        REFERENCES {$this->getTable('customer_entity')} (`entity_id`) ON DELETE SET NULL ON UPDATE SET NULL,
      CONSTRAINT `FK_LINK_STORE_HELPMATE_TICKET` FOREIGN KEY (`store_id`)
        REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE,
      CONSTRAINT `FK_LINK_DEPARTMENT_HELPMATE_TICKET` FOREIGN KEY (`department_id`)
        REFERENCES {$this->getTable('tm_helpmate_department')} (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION)
ENGINE = InnoDB DEFAULT CHARSET=utf8;


-- DROP TABLE IF EXISTS {$this->getTable('tm_helpmate_theard')};
CREATE  TABLE IF NOT EXISTS {$this->getTable('tm_helpmate_theard')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `ticket_id` int(11) unsigned NOT NULL,
  `message_id` varchar(255) NOT NULL,
  `created_at` datetime NULL,
  `text` text NULL,
  `file` VARCHAR(255) NULL ,
  `user_id` {$type} DEFAULT NULL,
  `status` tinyint(1) NOT NULL default 1,
  `priority` tinyint(1) NOT NULL default 1,
  `department_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`) ,
  CONSTRAINT `FK_LINK_USER_HELPMATE_THEARD` FOREIGN KEY (`user_id`)
        REFERENCES {$this->getTable('admin_user')} (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_DEPARTMENT_HELPMATE_THEARD` FOREIGN KEY (`department_id`)
        REFERENCES {$this->getTable('tm_helpmate_department')} (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,

  -- INDEX `FK_LINK_TICKET_HELPMATE_THEARD` (`id`),
  CONSTRAINT `FK_LINK_TICKET_HELPMATE_THEARD` FOREIGN KEY (`ticket_id`)
        REFERENCES {$this->getTable('tm_helpmate_ticket')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

        
-- DROP TABLE IF EXISTS {$this->getTable('tm_helpmate_gateway')};
CREATE  TABLE IF NOT EXISTS {$this->getTable('tm_helpmate_gateway')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `name` varchar(45) NULL ,
  `status` tinyint(1) NOT NULL default 1,
  `type` tinyint(1) NOT NULL default 1,  -- 2 - imap else pop3
  `email` varchar(128) NOT NULL default '',
  `host` varchar(128) NULL default '',
  `user` varchar(45)  NOT NULL default '',
  `password` varchar(45) NOT NULL default '',
  `port` smallint(5) unsigned NOT NULL default 110,
  `secure` smallint(1) unsigned NOT NULL default 0, -- 1 - ssl, 2 - tsl else none
  `remove` tinyint(1) NOT NULL default 0,
  PRIMARY KEY (`id`))
ENGINE = InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('tm_helpmate_gateway')}`(`id`,`name`,`status`,`email`,`host`,`user`,`port`)
VALUES (1, 'Default', 0, 'your email here', 'your host here', 'your user here', 110);

-- DROP TABLE IF EXISTS {$this->getTable('tm_helpmate_department')};
CREATE  TABLE IF NOT EXISTS {$this->getTable('tm_helpmate_department')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `active` tinyint(1) NOT NULL default 1,
  `name` VARCHAR(45) NULL ,
  `store_id` smallint(5) unsigned NOT NULL,
  `gateway_id` int(11) unsigned,
  `created_at` datetime NULL,
--  `notified` tinyint(1) NOT NULL default 1,
--  `email` varchar(128) NOT NULL DEFAULT '',
  `sender` VARCHAR(32) NULL DEFAULT 'general',
  `email_template_new` smallint(5) unsigned NOT NULL,
  `email_template_answer` smallint(5) unsigned NOT NULL,
  `email_template_admin` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `FK_LINK_GATEWAY_HELPMATE_DEPARTMENT` FOREIGN KEY (`gateway_id`)
        REFERENCES {$this->getTable('tm_helpmate_gateway')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_STORE_HELPMATE_DEPARTMENT` FOREIGN KEY (`store_id`)
        REFERENCES {$this->getTable('core_store')} (`store_id`) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('tm_helpmate_department')}`(`id`,`active`,`name`,`store_id`,`gateway_id`)
VALUES (1, 0, 'Default', 0, 1);

-- DROP TABLE IF EXISTS {$this->getTable('tm_helpmate_department_user')};
CREATE  TABLE IF NOT EXISTS {$this->getTable('tm_helpmate_department_user')} (
  `id` int(11) unsigned NOT NULL auto_increment,
  `department_id` int(11) unsigned NOT NULL ,
  `user_id` {$type} NOT NULL,
  PRIMARY KEY (`id`) ,
  -- INDEX `fk_tm_helpmate_department_user_1` () ,
  CONSTRAINT `FK_LINK_USER_HELPMATE_DEPARTMENT_USER` FOREIGN KEY (`user_id`)
    REFERENCES {$this->getTable('admin_user')} (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_LINK_DEPARTMENT_HELPMATE_DEPARTMENT_USER` FOREIGN KEY (`department_id`)
    REFERENCES {$this->getTable('tm_helpmate_department')} (`id`) ON DELETE CASCADE ON UPDATE CASCADE)
ENGINE = InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$this->getTable('tm_helpmate_department_user')}`(`department_id`,`user_id`)
VALUES (1, 1);

");
$installer->endSetup(); 
