<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-05-12T23:33:50+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Widget/Grid/Column/Renderer/Backordered.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Widget_Grid_Column_Renderer_Backordered extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $orderItems = $row->getAllVisibleItems();
        $backOrderItemQty = 0;
        foreach ($orderItems as $orderItem) {
            if ($orderItem->getQtyBackordered() > 0) {
                $backOrderItemQty += $orderItem->getQtyBackordered();
            }
        }
        $columnHtml = 'No';
        if ($backOrderItemQty > 0) {
            $columnHtml = '<span style="font-size:14px; color: red; font-weight: bold;">'.Mage::helper('xtento_enhancedgrid')->__('Yes').' ('.$backOrderItemQty.')</span>';
        }
        return $columnHtml;
    }
}

?>