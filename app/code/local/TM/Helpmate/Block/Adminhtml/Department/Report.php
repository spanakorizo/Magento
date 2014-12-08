<?php
class TM_Helpmate_Block_Adminhtml_Department_Report extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_department_report';
        $this->_blockGroup = 'helpmate';
        $this->_headerText = Mage::helper('helpmate')->__('Department Report');

        parent::__construct();
        $this->_removeButton('add');
    }
}