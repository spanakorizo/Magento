<?php

/**
 * Product:       Xtento_GridActions (1.7.5)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-05-02T21:30:46+00:00
 * Last Modified: 2013-05-31T15:29:13+02:00
 * File:          app/code/local/Xtento/GridActions/controllers/Adminhtml/Gridactions/GridController.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_GridActions_Adminhtml_GridActions_GridController extends Mage_Adminhtml_Controller_Action
{
    public function massAction()
    {
        Mage::getModel('gridactions/processor')->processOrders();
        $this->_redirect('adminhtml/sales_order');
    }
}