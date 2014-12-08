<?php

class TM_Helpmate_Block_Adminhtml_Status_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpmate';
        $this->_controller = 'adminhtml_status';

        parent::__construct();
    }

    public function getHeaderText()
    {
        $item = Mage::registry('helpmate_status_data');
        if($item && $item->getId()) {

            return Mage::helper('helpmate')->__(
                "Edit Status '%s'",
                $this->htmlEscape($item->getName())
            );

        } else {
            return Mage::helper('helpmate')->__('Add New Status');
        }
    }
}