<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-05-12T23:36:17+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Widget/Grid/Column/Renderer/Items.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Widget_Grid_Column_Renderer_Items extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        if ($this->getColumn()->getIsOrderColumn() === false) {
            $orderItems = $row->getAllItems();
        } else {
            $orderItems = $row->getAllVisibleItems();
        }
        if (Mage::helper('xtento_enhancedgrid')->isMageExport()) {
            // Export reduced version when exporting orders using the built-in CSV/Excel XML export functionality of Magento
            $productInfo = "";
            foreach ($orderItems as $orderItem) {
                if ($this->getColumn()->getIsOrderColumn() === false) {
                    $productInfo .= sprintf("%dx %s - ", round($orderItem->getQty()), $orderItem->getName());
                } else {
                    $productInfo .= sprintf("%dx %s - ", round($orderItem->getQtyOrdered()), $orderItem->getName());
                }
            }
            $productInfo = substr($productInfo, 0, -3);
        } else {
            $hasCustomOptions = false;
            if ($this->getColumn()->getShowCustomOptions()) {
                foreach ($orderItems as $orderItem) {
                    $productOptions = $orderItem->getProductOptions();
                    if (isset($productOptions['options']) || isset($productOptions['attributes_info'])) {
                        $hasCustomOptions = true;
                        break;
                    }
                }
            }
            $productInfo = '<div><table class="xtento-item-table"><thead><tr class="xtento-item-tr">';
            if ($this->getColumn()->getShowThumbnail()) {
                $productInfo .= '<th>' . Mage::helper('xtento_enhancedgrid')->__('Image') . '</th>';
            }
            $productInfo .= '<th>' . Mage::helper('xtento_enhancedgrid')->__('Name') . '</th>';
            if ($hasCustomOptions) {
                $productInfo .= '<th>' . Mage::helper('xtento_enhancedgrid')->__('Product Options') . '</th>';
            }
            $productInfo .= '<th>' . Mage::helper('xtento_enhancedgrid')->__('SKU') . '</th>';
            if ($this->getColumn()->getIsOrderColumn() !== false) {
                $productInfo .= '<th>' . Mage::helper('xtento_enhancedgrid')->__('Total') . '</th>';
            }
            $productInfo .= '</tr></thead><tbody>';
            $lineCount = 0;
            foreach ($orderItems as $orderItem) {
                $lineCount++;
                $trClass = "";
                $trStyle = "";
                if ($this->getColumn()->getData('truncate_items') !== null && $lineCount > $this->getColumn()->getData('truncate_items')) {
                    $trClass = ' xtento-item-tr-hidden-' . $row->getId();
                    $trStyle = ' style="display:none;"';
                }
                $productInfo .= '<tr class="xtento-item-tr' . $trClass . '"' . $trStyle . '>';
                if ($this->getColumn()->getShowThumbnail()) {
                    try {
                        $pictureUrl = Mage::helper('catalog/image')->init(Mage::getModel('catalog/product')->load($orderItem->getProductId()), 'small_image')->resize(50);
                    } catch (Exception $e) {
                        $pictureUrl = '';
                    }
                    $productInfo .= '<td><img alt="' . Mage::helper('xtento_enhancedgrid')->__('No Image') . '" src="' . $pictureUrl . '" style="max-height: 50px" /></td>';
                }
                $backorderedStatus = '';
                if ($orderItem->getQtyBackordered() > 0) {
                    $backorderedStatus = sprintf(" <strong>(" . Mage::helper('xtento_enhancedgrid')->__('Backordered') . ": %d)</strong>", $orderItem->getQtyBackordered());
                }
                $productInfo .= '<td>' . $orderItem->getName() . $backorderedStatus . '</td>';
                if ($hasCustomOptions) {
                    $customOptionText = '';
                    $customOptions = '';
                    $productOptions = $orderItem->getProductOptions();
                    if (isset($productOptions['options']))
                        $customOptions = $productOptions['options'];
                    else if (isset($productOptions['attributes_info'])) {
                        $customOptions = $productOptions['attributes_info'];
                    }
                    if ($customOptions) {
                        foreach ($customOptions as $customOption) {
                            $customOptionText .= '<b><i>' . $customOption['label'] . ':</i></b><br /> ';
                            $customOptionText .= $customOption['value'] . '<br />';
                        }
                    }
                    $productInfo .= '<td>' . $customOptionText . '</td>';
                }

                if ($this->getColumn()->getIsOrderColumn() === false) {
                    $productInfo .= '<td>' . round($orderItem->getQty()) . 'x ' . $orderItem->getSku() . '</td>';
                } else {
                    $productInfo .= '<td>' . round($orderItem->getQtyOrdered()) . 'x ' . $orderItem->getSku() . '</td>';
                }

                if ($this->getColumn()->getIsOrderColumn() !== false) {
                    if ($this->getColumn()->getData('show_order_currency')) {
                        $productInfo .= '<td>' . Mage::helper('core')->currencyByStore($orderItem->getBaseRowTotalInclTax(), $row->getStore(), true, false) . '</td>';
                    } else {
                        $productInfo .= '<td>' . Mage::helper('core')->currency($orderItem->getBaseRowTotalInclTax(), true, false) . '</td>';
                    }
                }

                $productInfo .= '</tr>';

            }
            $productInfo .= '</tbody></table>';
            if ($this->getColumn()->getData('truncate_items') !== null && $lineCount > $this->getColumn()->getData('truncate_items')) {
                $productInfo .= '<a href="#" onclick="showMoreItems(' . $row->getId() . '); this.hide(); return false;">' . Mage::helper('xtento_enhancedgrid')->__('Show more items') . '</a>';
            }
            $productInfo .= '</div>';
        }
        return $productInfo;
    }
}

?>