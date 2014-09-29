<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2013-10-06T17:25:04+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Adminhtml/Grid/Edit/Tab/Columns.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Adminhtml_Grid_Edit_Tab_Columns extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $this->setTemplate('xtento/enhancedgrid/columns.phtml');
        return parent::_prepareForm();
    }

    protected function getColumns()
    {
        $model = Mage::registry('enhanced_grid_current_grid');
        $configuredColumns = $model->getConfiguredColumns();
        return $configuredColumns;
    }
}