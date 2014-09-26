<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-05-13T14:13:47+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Model/Grid/Processor.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Model_Grid_Processor
{
    public function processBlock($block, $reinitMode = false)
    {
        $gridType = Mage::helper('xtento_enhancedgrid')->getGridBlockType($block);
        // Process grid
        if ($gridType) {
            $gridConfiguration = false;
            if (!Mage::helper('xtento_enhancedgrid')->isMageExport()) {
                Mage::getSingleton('xtento_enhancedgrid/columns')->getAndSaveGridColumns($block, $gridType);
            }
            $gridConfigurationCollection = Mage::getModel('xtento_enhancedgrid/grid')->getCollection();
            $gridConfigurationCollection->addFieldToFilter('type', $gridType);
            $gridConfigurationCollection->addFieldToFilter('enabled', 1);
            foreach ($gridConfigurationCollection as $foundGridConfiguration) {
                if (in_array(implode("", Mage::getSingleton('admin/session')->getUser()->getRoles()), explode(",", $foundGridConfiguration->getRoleIds()))) {
                    $gridConfiguration = $foundGridConfiguration;
                    break 1;
                }
            }
            // Process columns
            if ($gridConfiguration !== false) {
                $blockColumns = $block->getColumns();
                // Pre-process existing columns to avoid filtering problems
                if (!$reinitMode) {
                    foreach ($blockColumns as $column) {
                        if ($column->getIndex()) {
                            $column->setFilterIndex('`main_table`.' . $column->getIndex());
                        }
                    }
                }
                // Add custom columns
                $customColumns = Mage::getSingleton('xtento_enhancedgrid/columns_custom')->getCustomColumns($gridConfiguration->getType());
                $usedCustomColumns = array();
                $configuredColumns = $gridConfiguration->getConfiguredColumns();
                foreach ($configuredColumns as $columnIndex => $columnData) {
                    if (!$columnData['is_visible']) {
                        if (isset($blockColumns[$columnIndex])) {
                            $block->xtRemoveColumn($columnIndex);
                        }
                    } else {
                        if (!isset($blockColumns[$columnIndex])) {
                            if (!$reinitMode) {
                                foreach ($columnData as $key => $value) {
                                    if (preg_match("/\_default$/", $key)) {
                                        unset($columnData[$key]);
                                    }
                                }
                                if ($columnIndex == 'purchased_items') {
                                    if ($columnData['filter_by'] == 'sku') {
                                        $columnData['filter_index'] = '`sales/order_item`.sku';
                                    } else if ($columnData['filter_by'] == 'name') {
                                        $columnData['filter_index'] = '`sales/order_item`.name';
                                    } else {
                                        $columnData['filter_index'] = '`sales/order_item`.sku';
                                    }
                                }
                                if (preg_match('/price/i', $columnData['renderer'])) {
                                    if (!array_key_exists('currency', $columnData)) {
                                        $columnData['currency'] = 'base_currency_code';
                                    }
                                }
                                $block->addColumn($columnIndex, $columnData);
                                if (isset($customColumns[$columnIndex])) {
                                    $usedCustomColumns[$columnIndex] = $customColumns[$columnIndex];
                                }
                            }
                        } else {
                            foreach ($columnData as $key => &$value) {
                                if ($key == "filter" && $value === "") {
                                    $value = false;
                                }
                            }
                            $block->xtUpdateColumn($columnIndex, $columnData);
                        }
                    }
                }

                if (Mage::helper('xtento_enhancedgrid')->isMageExport()) {
                    $block->xtRemoveColumn("sagepay_transaction_state");
                    $block->xtRemoveColumn("combined-input");
                    $block->xtRemoveColumn("carrier-selector");
                    $block->xtRemoveColumn("tracking-input");
                }

                if (!$reinitMode) {
                    // Apply column sort order
                    $block->xtResetColumnsOrder();
                    $previousIndex = null;
                    foreach ($configuredColumns as $columnIndex => $columnData) {
                        if (isset($columnData['is_visible']) && $columnData['is_visible']) {
                            if (!is_null($previousIndex)) {
                                $block->addColumnsOrder($columnIndex, $previousIndex);
                            }
                            $previousIndex = $columnIndex;
                        }
                    }
                    #var_dump($block->getColumns());
                    $block->sortColumnsByOrder();

                    Mage::register('xtento_enhancedgrid_block_info', new Varien_Object(array('block' => $block, 'custom_columns' => $usedCustomColumns, 'grid_configuration' => $gridConfiguration)), true);
                    $block->xtPrepareCollection();
                    if (!Mage::helper('xtento_enhancedgrid')->isMageExport()) {
                        Mage::unregister('xtento_enhancedgrid_block_info');
                    }
                    #$block->xtCountCollection();
                }
            }
        }
        return $this;
    }
}