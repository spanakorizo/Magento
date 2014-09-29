<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-05-12T23:33:50+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Widget/Grid/Column/Renderer/PaymentMethod.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Widget_Grid_Column_Renderer_PaymentMethod extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    public function render(Varien_Object $row)
    {
        $paymentMethod = $row->getData('payment_method');
        if ($paymentMethod == 'sagepaydirectpro') {
            try {
                $order = Mage::getModel('sales/order')->load($row->getEntityId());
                if ($order->getId()) {
                    $payment = $order->getPayment();
                    if ($payment->getMethodInstance()) {
                        return $payment->getMethodInstance()->getTitle();
                    }
                }
            } catch (Exception $e) {
                // Could not get payment method instance - probably payment module was removed.
                return parent::render($row);
            }
        } else {
            return parent::render($row);
        }
    }
}
