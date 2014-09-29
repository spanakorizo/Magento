<?php

class TM_KnowledgeBase_Block_Adminhtml_Faq_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_blockGroup = 'knowledgebase';
        $this->_controller = 'adminhtml_faq';
        
        parent::__construct();
        
        $this->_updateButton('save', 'label', Mage::helper('knowledgebase')->__('Save Article'));
        $this->_updateButton('delete', 'label', Mage::helper('knowledgebase')->__('Delete Article'));

    }

    public function getHeaderText()
    {
        if( Mage::registry('knowledgebase_faq_data')
            && Mage::registry('knowledgebase_faq_data')->getId()) {

            return Mage::helper('knowledgebase')->__(
                "Edit Articles '%s'",
                $this->htmlEscape(Mage::registry('knowledgebase_faq_data')->getTitle())
            );
            
        } else {
            return Mage::helper('knowledgebase')->__('Add New Article');
        }
    }
}