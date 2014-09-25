<?php

$installer = $this;
/* @var $installer Mage_Catalog_Model_Resource_Setup */

$installer->updateAttribute(
    'catalog_product',
    'knowledgebase_faq',
    'is_visible',
    false
);
