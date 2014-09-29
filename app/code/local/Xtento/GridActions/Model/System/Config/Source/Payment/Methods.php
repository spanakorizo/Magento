<?php

/**
 * Product:       Xtento_GridActions (1.7.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:28+00:00
 * Last Modified: 2012-02-13T20:51:34+01:00
 * File:          app/code/local/Xtento/GridActions/Model/System/Config/Source/Payment/Methods.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_GridActions_Model_System_Config_Source_Payment_Methods
{

    public function toOptionArray()
    {
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.5.0.0', '>=')) {
            return Mage::helper('payment')->getPaymentMethodList(true, true);
        } else {
            // Legacy
            return Mage::helper('xtcore/payment')->getPaymentMethodList(true, true);
        }
    }
}
