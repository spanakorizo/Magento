<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2013-10-05T17:25:29+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Model/System/Config/Source/Grid/Type.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Model_System_Config_Source_Grid_Type
{
    public function toOptionArray()
    {
        return Mage::getSingleton('xtento_enhancedgrid/grid')->getTypes();
    }
}