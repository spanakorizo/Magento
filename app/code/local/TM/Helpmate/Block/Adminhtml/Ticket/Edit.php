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

    protected function _generateNumber($id)
    {
        $prefix = '';
        for ($i = 0; $i < 3; $i++) {
          $prefix .= chr(rand(ord('a'), ord('z')));
        }
        $id = (string) $id;

        for ($i = 0; strlen($id) - 5; $i++){
            $id = '0' . $id;
        }
        return strtoupper($prefix) . '-' . $id;
    }

    public function getHeaderText()
    {
        $ticket = Mage::registry('helpmate_ticket_data');

        if ($ticket && $ticket->getId() && $ticket->getNumber()) {

            return Mage::helper('helpmate')->__(
                "Edit Ticket : \"%s\" # %s",
                $this->htmlEscape($ticket->getTitle()),
                $this->htmlEscape($ticket->getNumber())
            );

        }
        return Mage::helper('helpmate')->__('Add New Ticket');
    }
}