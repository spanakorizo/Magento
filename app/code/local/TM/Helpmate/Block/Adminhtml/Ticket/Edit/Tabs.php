<?php
class TM_Helpmate_Block_Adminhtml_Ticket_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('helpmate_ticket_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('helpmate')->__('Ticket Information'));
    }
}