<?php
class TM_Helpmate_Block_Adminhtml_Department extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_department';
        $this->_blockGroup = 'helpmate';
        $this->_headerText = Mage::helper('helpmate')->__('Department');
        $this->_addButtonLabel = Mage::helper('helpmate')->__('Add Department');
        parent::__construct();
    }
}