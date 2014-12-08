<?php
class TM_Helpmate_Block_Adminhtml_Status extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_status';
        $this->_blockGroup = 'helpmate';
        $this->_headerText = Mage::helper('helpmate')->__('Status');
//        $this->_addButtonLabel = Mage::helper('helpmate')->__('Add Gateway');
        parent::__construct();
    }
}