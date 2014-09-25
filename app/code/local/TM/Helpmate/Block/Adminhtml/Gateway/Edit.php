<?php

class TM_Helpmate_Block_Adminhtml_Gateway_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpmate';
        $this->_controller = 'adminhtml_gateway';
        
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('helpmate')->__('Save Gateway'));
        $this->_updateButton('delete', 'label', Mage::helper('helpmate')->__('Delete Gateway'));

        $this->_addButton('test', array(
            'label'     => Mage::helper('helpmate')->__('Test Email Gateway Connection'),
            'onclick'   => "$('edit_form').action = '" . $this->getUrl('*/*/test') . "'; editForm.submit();",
            'class'     => 'save',
        ), -100);

    }

    public function getHeaderText()
    {
        if( Mage::registry('helpmate_gateway_data')
            && Mage::registry('helpmate_gateway_data')->getId()) {

            return Mage::helper('helpmate')->__(
                "Edit Email Gateway '%s'",
                $this->htmlEscape(Mage::registry('helpmate_gateway_data')->getName())
            );
            
        } else {
            return Mage::helper('helpmate')->__('Add New Email Gateway');
        }
    }
}