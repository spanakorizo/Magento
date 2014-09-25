<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-06-07T14:28:49+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Widget/Grid/Column/Renderer/OrderSource.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Widget_Grid_Column_Renderer_OrderSource extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Options
{
    public function render(Varien_Object $row)
    {
        $paymentMethod = $row->getData('payment_method2');
        if ($paymentMethod == 'm2epropayment') {
            try {
                $order = Mage::getModel('sales/order')->load($row->getEntityId());
                if ($order->getId()) {
                    $payment = $order->getPayment();
                    if ($payment->getMethodInstance()) {
                        $additionalData = @unserialize($payment->getAdditionalData());
                        if (isset($additionalData["component_mode"])) {
                            $compMode = $additionalData["component_mode"];
                            $title = "";
                            switch ($compMode) {
                                case Ess_M2ePro_Helper_Component_Ebay::NICK:
                                    $title = Ess_M2ePro_Helper_Component_Ebay::TITLE;
                                    break;
                                case Ess_M2ePro_Helper_Component_Amazon::NICK:
                                    $title = 'Amazon';
                                    break;
                                case Ess_M2ePro_Helper_Component_Buy::NICK:
                                    $title = 'Rakuten.com';
                                    break;
                                case Ess_M2ePro_Helper_Component_Play::NICK:
                                    $title = Ess_M2ePro_Helper_Component_Play::TITLE;
                                    break;
                            }
                            return $title;
                        }
                    }
                }
            } catch (Exception $e) {
                // Could not get payment method instance - probably payment module was removed.
                return "";
            }
        } else {
            return $this->__('Magento');
        }
    }
}