<?php

class TM_Email_Model_System_Config_Source_Gateway_Storage
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $return = array();
        $return[] = array(
            'value' => '',
            'label' => ''
        );

        return array_merge(
            $return, Mage::getModel('tm_email/gateway_storage')->getOptionArray()
        );
    }
}
