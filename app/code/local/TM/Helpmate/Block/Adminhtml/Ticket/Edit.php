<?php

class TM_Helpmate_Block_Adminhtml_Ticket_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId = 'id';
        $this->_blockGroup = 'helpmate';
        $this->_controller = 'adminhtml_ticket';
        
        $this->_updateButton('save', 'label', Mage::helper('helpmate')->__('Save Ticket'));
        $this->_updateButton('delete', 'label', Mage::helper('helpmate')->__('Delete Ticket'));

    }

    public function getHeaderText()
    {
        $data = Mage::registry('helpmate_ticket_data');
//        Zend_Debug::dump($data->getNumber());
        if ($data && $data->getId() && $data->getNumber()) {

            return Mage::helper('helpmate')->__(
                "Edit Ticket # %s (%s)",
                $this->htmlEscape(1000000 + $data->getId()),
                $this->htmlEscape($data->getNumber())
            );
            
        }
        return Mage::helper('helpmate')->__('Add New Ticket');
    }
}