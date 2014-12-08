<?php
class TM_Helpmate_Block_Adminhtml_Theard_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'helpmate';
        $this->_controller = 'adminhtml_theard';

        parent::__construct();

        $this->_updateButton('save', 'label', Mage::helper('helpmate')->__('Save Item'));
        $this->_updateButton('save', 'onclick', 'sendForm();return false;');
        $this->_updateButton('delete', 'label', Mage::helper('helpmate')->__('Delete Item'));
        $this->_removeButton('reset');

        if (Mage::registry('helpmate_theard_data') ) {
            $ticketId = Mage::registry('helpmate_theard_data')->getTicketId();
            if (!empty($ticketId)) {
                $url = $this->getUrl('*/helpmate_ticket/edit', array('id' => $ticketId));
            } else {
                $url = $this->getUrl('*/helpmate_ticket/index');
            }
            $this->_updateButton('back', 'onclick', 'setLocation(\'' . $url . '\')');
        }

        $this->_formScripts[] = "
            function sendForm(){\$('edit_form').submit();}
            function toggleEditor() {
                if (tinyMCE.getInstanceById('text') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'text');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'text');
                }
            }

//            function saveAndContinueEdit(){
//                editForm.submit($('edit_form').action+'back/edit/');
//            }
        ";
    }

    public function getHeaderText()
    {
        return Mage::helper('helpmate')->__("Edit Theard");
    }
}