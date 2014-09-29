<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2013-10-19T14:50:40+02:00
 * File:          app/code/local/Xtento/EnhancedGrid/Model/System/Config/Source/Admin/Roles.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Model_System_Config_Source_Admin_Roles
{
    public function toOptionArray()
    {
        $roleArray = array();
        $roles = Mage::getModel('admin/roles')->getCollection();
        foreach ($roles as $role) {
            $roleArray[] = array('value' => $role->getId(), 'label' => sprintf('%s (ID: %d)', $role->getRoleName(), $role->getId()));
        }
        return $roleArray;
    }
}