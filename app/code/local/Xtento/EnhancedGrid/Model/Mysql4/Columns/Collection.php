<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2013-10-05T18:01:00+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Model/Mysql4/Columns/Collection.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Model_Mysql4_Columns_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('xtento_enhancedgrid/columns');
    }
}