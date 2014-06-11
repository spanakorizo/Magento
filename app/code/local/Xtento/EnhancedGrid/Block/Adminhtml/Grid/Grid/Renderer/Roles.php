<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2013-10-19T14:53:48+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Block/Adminhtml/Grid/Grid/Renderer/Roles.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Block_Adminhtml_Grid_Grid_Renderer_Roles extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $allRoles = Mage::getModel('xtento_enhancedgrid/system_config_source_admin_roles')->toOptionArray();
        $roleIds = $row->getRoleIds();
        $gridRoles = array();
        foreach ($allRoles as $role) {
            if (in_array($role['value'], explode(",", $roleIds))) {
                $gridRoles[] = $role['label'];
            }
        }
        return implode(", ", $gridRoles);
    }
}