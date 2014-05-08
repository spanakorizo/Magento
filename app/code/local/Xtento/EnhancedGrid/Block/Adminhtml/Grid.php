<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.1)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-05-02T21:30:40+00:00
 * Last Modified: 2013-10-05T18:57:25+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Adminhtml/Grid.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Adminhtml_Grid extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'xtento_enhancedgrid';
        $this->_controller = 'adminhtml_grid';
        $this->_headerText = Mage::helper('xtento_enhancedgrid')->__('XTENTO Enhanced Grids - Customized Grids');
        $this->_addButtonLabel = Mage::helper('xtento_enhancedgrid')->__('Add New Customized Grid');
        parent::__construct();
    }
}