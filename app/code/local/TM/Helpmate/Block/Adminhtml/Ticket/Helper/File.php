<?php
class TM_Helpmate_Block_Adminhtml_Ticket_Helper_File extends Varien_Data_Form_Element_File
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = Mage::getBaseUrl('media') . 'helpmate/'. $this->getValue();
        }
        return $url;
    }
}