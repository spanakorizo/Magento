<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.1)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-05-02T21:30:40+00:00
 * Last Modified: 2013-10-25T15:10:48+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Widget/Grid/Column/Filter/Items.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Widget_Grid_Column_Filter_Items extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public static function itemFilter($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return true;
        }
        $collection->getSelect()->having("item_filter like ?", "%$value%");
        return true;
    }
}