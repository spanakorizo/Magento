<?php

class TM_KnowledgeBase_Block_Adminhtml_Category_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'knowledgebase';
        $this->_controller = 'adminhtml_category';
        
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('knowledgebase')->__('Save Category'));
        $this->_updateButton('delete', 'label', Mage::helper('knowledgebase')->__('Delete Category'));

    }

    public function getHeaderText()
    {
        if( Mage::registry('knowledgebase_category_data')
            && Mage::registry('knowledgebase_category_data')->getId()) {

            return Mage::helper('knowledgebase')->__(
                "Edit Category '%s'",
                $this->htmlEscape(Mage::registry('knowledgebase_category_data')->getName())
            );
            
        } else {
            return Mage::helper('knowledgebase')->__('Add New Category');
        }
    }
}