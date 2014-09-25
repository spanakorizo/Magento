<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-04-29T17:44:22+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Adminhtml/Grid/Edit/Tab/Columnconfig.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Adminhtml_Grid_Edit_Tab_Columnconfig extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $model = Mage::registry('enhanced_grid_current_grid');
        $configuredColumns = $model->getConfiguredColumns();

        $form = new Varien_Data_Form();
        $this->setForm($form);

        $fieldset = $form->addFieldset('purchased_items', array(
            'legend' => Mage::helper('xtento_enhancedgrid')->__('Column: Purchased Items'),
        ));

        $fieldset->addField('show_custom_options', 'select', array(
            'label' => Mage::helper('xtento_enhancedgrid')->__('Show custom options'),
            'name' => 'columns[purchased_items][show_custom_options]',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value' => $this->_getColumnConfigValue($configuredColumns, 'purchased_items', 'show_custom_options', true)
        ));

        $fieldset->addField('show_order_currency', 'select', array(
            'label' => Mage::helper('xtento_enhancedgrid')->__('Show prices in order currency'),
            'name' => 'columns[purchased_items][show_order_currency]',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value' => $this->_getColumnConfigValue($configuredColumns, 'purchased_items', 'show_order_currency', true),
            'note' => $this->__('If set to "No", prices in the "Purchased Items" table will be shown in the base currency of Magento')
        ));

        $fieldset->addField('show_thumbnail', 'select', array(
            'label' => Mage::helper('xtento_enhancedgrid')->__('Show product thumbnail image'),
            'name' => 'columns[purchased_items][show_thumbnail]',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value' => $this->_getColumnConfigValue($configuredColumns, 'purchased_items', 'show_thumbnail', true)
        ));

        $fieldset->addField('truncate_items', 'text', array(
            'label' => Mage::helper('xtento_enhancedgrid')->__('Show only X items'),
            'name' => 'columns[purchased_items][truncate_items]',
            'value' => $this->_getColumnConfigValue($configuredColumns, 'purchased_items', 'truncate_items', '999'),
            'note' => $this->__('Show X items and hide the others by default; hover of the column to show more items')
        ));

        $fieldset->addField('filter_by', 'select', array(
            'label' => Mage::helper('xtento_enhancedgrid')->__('Search/filter by'),
            'name' => 'columns[purchased_items][filter_by]',
            'values' => array(
                array('value' => 'sku', 'label' => Mage::helper('xtento_enhancedgrid')->__('SKU')),
                array('value' => 'name', 'label' => Mage::helper('xtento_enhancedgrid')->__('Product Name')),
            ),
            'value' => $this->_getColumnConfigValue($configuredColumns, 'purchased_items', 'filter_by', 'sku'),
            'note' => $this->__('If you try to search in the "Purchased Items" column, you can either have the search done based on product SKUs or product names')
        ));

        $fieldset = $form->addFieldset('comment_history', array(
            'legend' => Mage::helper('xtento_enhancedgrid')->__('Column: Comment History'),
        ));

        $fieldset->addField('truncate_chars', 'text', array(
            'label' => Mage::helper('xtento_enhancedgrid')->__('Truncate after X characters'),
            'name' => 'columns[comment_history][truncate_chars]',
            'value' => $this->_getColumnConfigValue($configuredColumns, 'comment_history', 'truncate_chars', '130'),
            'note' => $this->__('Show X characters and truncate the comment history after that')
        ));

        $fieldset = $form->addFieldset('payment_method', array(
            'legend' => Mage::helper('xtento_enhancedgrid')->__('Column: Payment Method'),
        ));

        $fieldset->addField('hide_disabled_methods', 'select', array(
            'label' => Mage::helper('xtento_enhancedgrid')->__('Hide disabled payment methods'),
            'name' => 'columns[payment_method][hide_disabled_methods]',
            'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
            'value' => $this->_getColumnConfigValue($configuredColumns, 'payment_method', 'hide_disabled_methods', true),
            'note' => $this->__('Show only enabled payment methods in the "Payment Method" dropdown?')
        ));

        return parent::_prepareForm();
    }

    private function _getColumnConfigValue($columns, $column, $name, $default = false)
    {
        if (isset($columns[$column]) && array_key_exists($name, $columns[$column])) {
            return $columns[$column][$name];
        } else {
            return $default;
        }
    }
}