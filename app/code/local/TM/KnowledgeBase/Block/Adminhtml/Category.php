<?php
class TM_KnowledgeBase_Block_Adminhtml_Category extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_category';
        $this->_blockGroup = 'knowledgebase';
        $this->_headerText = Mage::helper('knowledgebase')->__('Category');
        $this->_addButtonLabel = Mage::helper('knowledgebase')->__('Add New Category');
        parent::__construct();
    }
}