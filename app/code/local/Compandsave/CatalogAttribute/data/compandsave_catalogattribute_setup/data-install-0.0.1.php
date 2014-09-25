<?php

/* @var $installer Mage_Catalog_Model_Resource_Setup */
$installer = Mage::getResourceModel('catalog/setup', 'default_setup');

$installer->startSetup();
/*
$installer->addAttribute('catalog_product', 'canonical_url', array(
        'group' => 'Meta Information',
        'backend' => null,
        'frontend' => null,
        'source' => null,
        'type' => 'text',
        'table' => null,
        'label' => 'Canonical URL',
        'input' => 'text',
        'frontend_class' => null,
        'required' => 0,
        'user_defined' => 1,
        'default' => '',
        'unique' => 1,
        'note' => 'Canonical URL for a page for SEO purposes',

       
        'input_renderer' => null,
        'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible' => 1,
        'searchable' => 0,
        'visible_in_advanced_search' => 0,
        'filterable' => 0,
        'filterable_in_search' => 0,
        'comparable' => 0,
        'visible_on_front' => 0,
        'is_html_allowed_on_front' => 0,
        'used_in_product_listing' => 0,
        'used_for_sort_by' => 0,
        'is_configurable' => 0,
        'apply_to' => 'simple,configurable,bundle,grouped',
        'position' => 0,
        'wysiwyg_enabled' => 0,
        'used_for_promo_rules' => 0,
        'sort_order' => 2,
    )
);

$installer->addAttribute('catalog_category', 'canonical_category',array(
    'type' => 'text',
    'label'=> 'Canonical URL',
    'input' => 'text',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => "",
    'group' => "General Information"
	)
);

$installer->run(' ALTER TABLE  `cms_page` ADD  `canonical_page` VARCHAR( 255 ) NULL;');
*/
$installer = $this;
$installer->getConnection()->addColumn($installer->getTable('sales/order'), 'old_order_id', 'int(11) NULL');
$installer->endSetup();