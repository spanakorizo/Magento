<?php
class TM_Helpmate_Block_Adminhtml_Ticket extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_ticket';
        $this->_blockGroup = 'helpmate';
        $this->_headerText = Mage::helper('helpmate')->__('Ticket');
        $this->_addButtonLabel = Mage::helper('helpmate')->__('Add New Ticket');
        parent::__construct();

        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('helpmate')->__('Refresh Email Storage'),
            'onclick'   => "setLocation('" . $this->getUrl('*/*/storage') . "')",
            'class'     => 'save',
        ), -100);
    }
}