<?php
class TM_KnowledgeBase_Block_Adminhtml_Faq extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_faq';
        $this->_blockGroup = 'knowledgebase';
        $this->_headerText = Mage::helper('knowledgebase')->__('Article');
        $this->_addButtonLabel = Mage::helper('knowledgebase')->__('Add New Article');
        parent::__construct();
    }
}