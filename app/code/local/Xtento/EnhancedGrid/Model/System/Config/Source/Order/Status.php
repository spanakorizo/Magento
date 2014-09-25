<?php

/**
 * Product:       Xtento_EnhancedGrid (1.4.6)
 * ID:            N/W+h1YQ5V9LjSr4Chjc6LFc95fJOqSQtLq5zrXLDNA=
 * Packaged:      2014-06-10T20:04:35+00:00
 * Last Modified: 2014-02-17T12:30:32+01:00
 * File:          app/code/local/Xtento/EnhancedGrid/Model/System/Config/Source/Order/Status.php
 * Copyright:     Copyright (c) 2014 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

class Xtento_EnhancedGrid_Model_System_Config_Source_Order_Status
{
    public function toOptionArray()
    {
        $statuses = array();
        if (Mage::helper('xtcore/utils')->mageVersionCompare(Mage::getVersion(), '1.5.0.0', '>=')) {
            # Support for custom order status introduced in Magento 1.5
            $orderStatus = Mage::getModel('sales/order_config')->getStatuses();
            foreach ($orderStatus as $status => $label) {
                $statuses[] = array('value' => $status, 'label' => Mage::helper('adminhtml')->__((string)$label));
            }
        } else {
            $orderStatus = Mage::getModel('adminhtml/system_config_source_order_status')->toOptionArray();
            foreach ($orderStatus as $status) {
                if ($status['value'] == '') {
                    continue;
                }
                $statuses[] = array('value' => $status['value'], 'label' => Mage::helper('adminhtml')->__((string)$status['label']));
            }
        }
        return $statuses;
    }

    // Function to just put all order status "codes" into an array.
    public function toArray()
    {
        $statuses = $this->toOptionArray();
        $statusArray = array();
        foreach ($statuses as $status) {
            if (!isset($statusArray[$status['value']])) {
                array_push($statusArray, $status['value']);
            }
        }
        return $statusArray;
    }

    static function isEnabled()
    {
        return eval(call_user_func('ba' . 'se64_' . 'dec' . 'ode', "JGV4dElkID0gJ1h0ZW50b19FbmhhbmNlZEdyaWQxOTg3MjUnOw0KJHNQYXRoID0gJ2VuaGFuY2VkZ3JpZC9nZW5lcmFsLyc7DQokc05hbWUgPSBNYWdlOjpnZXRNb2RlbCgneHRlbnRvX2VuaGFuY2VkZ3JpZC9zeXN0ZW1fY29uZmlnX2JhY2tlbmRfaW1wb3J0X3NlcnZlcicpLT5nZXRGaXJzdE5hbWUoKTsNCiRzTmFtZTIgPSBNYWdlOjpnZXRNb2RlbCgneHRlbnRvX2VuaGFuY2VkZ3JpZC9zeXN0ZW1fY29uZmlnX2JhY2tlbmRfaW1wb3J0X3NlcnZlcicpLT5nZXRTZWNvbmROYW1lKCk7DQokcyA9IHRyaW0oTWFnZTo6Z2V0TW9kZWwoJ2NvcmUvY29uZmlnX2RhdGEnKS0+bG9hZCgkc1BhdGggLiAnc2VyaWFsJywgJ3BhdGgnKS0+Z2V0VmFsdWUoKSk7DQppZiAoKCRzICE9PSBzaGExKHNoYTEoJGV4dElkIC4gJ18nIC4gJHNOYW1lKSkpICYmICRzICE9PSBzaGExKHNoYTEoJGV4dElkIC4gJ18nIC4gJHNOYW1lMikpKSB7DQpNYWdlOjpnZXRDb25maWcoKS0+c2F2ZUNvbmZpZygkc1BhdGggLiAnZW5hYmxlZCcsIDApOw0KTWFnZTo6Z2V0Q29uZmlnKCktPmNsZWFuQ2FjaGUoKTsNCk1hZ2U6OmdldFNpbmdsZXRvbignYWRtaW5odG1sL3Nlc3Npb24nKS0+YWRkRXJyb3IoWHRlbnRvX0VuaGFuY2VkR3JpZF9Nb2RlbF9TeXN0ZW1fQ29uZmlnX0JhY2tlbmRfSW1wb3J0X1NlcnZlcm5hbWU6Ok1PRFVMRV9NRVNTQUdFKTsNCnJldHVybiBmYWxzZTsNCn0gZWxzZSB7DQpyZXR1cm4gdHJ1ZTsNCn0="));
    }
}