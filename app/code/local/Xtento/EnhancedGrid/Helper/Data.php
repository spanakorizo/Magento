<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.1)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-05-02T21:30:40+00:00
 * Last Modified: 2014-01-12T13:40:39+01:00
 * File:          app/code/local/Xtento/EnhancedGrid/Helper/Data.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Helper_Data extends Mage_Core_Helper_Abstract
{
    const EDITION = 'EE';

    public function getModuleEnabled()
    {
        if (!Mage::getStoreConfigFlag('enhancedgrid/general/enabled')) {
            return 0;
        }
        $moduleEnabled = Mage::getModel('core/config_data')->load('enhancedgrid/general/' . str_rot13('frevny'), 'path')->getValue();
        if (empty($moduleEnabled) || !$moduleEnabled || (0x28 !== strlen(trim($moduleEnabled)))) {
            return 0;
        }
        return true;
    }

    public function getGridBlockType($block) {
        $gridType = false;
        if ($block->getId() == 'sales_order_grid' && $block instanceof Mage_Adminhtml_Block_Widget_Grid) {
            $gridType = Xtento_EnhancedGrid_Model_Grid::GRID_SALES_ORDER;
        }
        if ($block->getId() == 'sales_invoice_grid' && $block instanceof Mage_Adminhtml_Block_Widget_Grid) {
            $gridType = Xtento_EnhancedGrid_Model_Grid::GRID_SALES_INVOICE;
        }
        if ($block->getId() == 'sales_shipment_grid' && $block instanceof Mage_Adminhtml_Block_Widget_Grid) {
            $gridType = Xtento_EnhancedGrid_Model_Grid::GRID_SALES_SHIPMENT;
        }
        if ($block->getId() == 'sales_creditmemo_grid' && $block instanceof Mage_Adminhtml_Block_Widget_Grid) {
            $gridType = Xtento_EnhancedGrid_Model_Grid::GRID_SALES_CREDITMEMO;
        }
        return $gridType;
    }

    /*
     * Is the current request a CSV/Excel XML export using the built-in functionality of Magento?
     */
    public function isMageExport() {
        return (stristr(Mage::app()->getRequest()->getControllerName(), 'sales_') && (Mage::app()->getRequest()->getActionName() == 'exportCsv' || Mage::app()->getRequest()->getActionName() == 'exportExcel'));
    }
}