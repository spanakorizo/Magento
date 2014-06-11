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

    protected function _beforeToHtml()
    {
        $this->addTab('form_section', array(
            'label'     => Mage::helper('helpmate')->__('General'),
            'title'     => Mage::helper('helpmate')->__('General'),
            'content'   => $this->getLayout()
                ->createBlock('helpmate/adminhtml_ticket_edit_tab_general')
                ->toHtml(),
        ));
        $this->addTab('notes_section', array(
            'label'     => Mage::helper('helpmate')->__('Notes'),
            'title'     => Mage::helper('helpmate')->__('Notes'),
            'content'   => $this->getLayout()
                ->createBlock('helpmate/adminhtml_ticket_edit_tab_notes')
                ->toHtml(),
        ));

        return parent::_beforeToHtml();
    }
}