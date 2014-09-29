<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-05-12T23:33:50+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Widget/Grid/Column/Renderer/Address.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Widget_Grid_Column_Renderer_Address extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $address = false;
        if ($this->getColumn()->getId() == 'full_billing_address') {
            $address = $row->getBillingAddress();
        }
        if ($this->getColumn()->getId() == 'full_shipping_address') {
            $address = $row->getShippingAddress();
        }
        if ($address) {
            if (Mage::helper('xtento_enhancedgrid')->isMageExport()) {
                return str_replace("\n", " - ", $address->format('text'));
            } else {
                return $address->format('html');
            }
        }
        return "";
    }
}

?>