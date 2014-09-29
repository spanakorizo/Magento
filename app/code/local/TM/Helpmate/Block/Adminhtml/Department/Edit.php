<?php

class TM_Helpmate_Block_Adminhtml_Department_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpmate';
        $this->_controller = 'adminhtml_department';
        
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('helpmate')->__('Save Department'));
        $this->_updateButton('delete', 'label', Mage::helper('helpmate')->__('Delete Department'));

    }

    public function getHeaderText()
    {
        if( Mage::registry('helpmate_department_data')
            && Mage::registry('helpmate_department_data')->getId()) {

            return Mage::helper('helpmate')->__(
                "Edit Department '%s'",
                $this->htmlEscape(Mage::registry('helpmate_department_data')->getName())
            );
            
        } else {
            return Mage::helper('helpmate')->__('Add New Department');
        }
    }
}