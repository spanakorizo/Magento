<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.1)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-05-02T21:30:40+00:00
 * Last Modified: 2013-11-25T17:50:54+01:00
 * File:          app/code/local/Xtento/EnhancedGrid/controllers/Adminhtml/Enhancedgrid/IndexController.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Adminhtml_Enhancedgrid_IndexController extends Mage_Adminhtml_Controller_Action
{
    public function permissionsAction()
    {
        Mage::getSingleton('adminhtml/session')->addWarning(Mage::helper('xtento_enhancedgrid')->__('You don\'t have rights to customize grids. Please go to System > Permissions > Roles and assign the "XTENTO Enhanced Grid > Grid Customization" permission to your admin role.'));
        $this->loadLayout();
        $this->renderLayout();
    }
}