<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.1)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-05-02T21:30:40+00:00
 * Last Modified: 2014-04-29T17:43:43+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Widget/Grid/Column/Filter/PaymentMethod.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Widget_Grid_Column_Filter_PaymentMethod extends Mage_Adminhtml_Block_Widget_Grid_Column_Filter_Select
{
    protected function _getOptions()
    {
        $options = array(array('value' => null, 'label' => ''));

        $paymentMethods = Mage::helper('xtcore/payment')->getPaymentMethodList(true, false, false, null, false);
        foreach ($paymentMethods as $code => $title) {
            if ($this->getColumn()->getHideDisabledMethods() && !Mage::getStoreConfigFlag('payment/' . $code . '/active')) {
                continue;
            }
            $options[] = array('value' => $code, 'label' => $title);
        }
        return $options;
    }
}
