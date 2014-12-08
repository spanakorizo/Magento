<?php
class TM_KnowledgeBase_Block_Adminhtml_Faq_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('knowledgebase_faq_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('knowledgebase')->__('Article Information'));

    }

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

//    protected function _beforeToHtml()
//    {
//        $this->addTab('form_section', array(
//            'label'     => Mage::helper('knowledgebase')->__('Article Information'),
//            'title'     => Mage::helper('knowledgebase')->__('Article Information'),
//            'content'   => $this->getLayout()
//                ->createBlock('knowledgebase/adminhtml_faq_edit_tab_general')
//                ->toHtml(),
//        ));
//        $this->addTab('content_section', array(
//            'label'     => Mage::helper('knowledgebase')->__('Content'),
//            'title'     => Mage::helper('knowledgebase')->__('Content'),
//            'content'   => $this->getLayout()
//                ->createBlock('knowledgebase/adminhtml_faq_edit_tab_content')
//                ->toHtml(),
//        ));
//        $this->addTab('meta_section', array(
//            'label'     => Mage::helper('knowledgebase')->__('Meta'),
//            'title'     => Mage::helper('knowledgebase')->__('Meta'),
//            'content'   => $this->getLayout()
//                ->createBlock('knowledgebase/adminhtml_faq_edit_tab_meta')
//                ->toHtml(),
//        ));
//
//        return parent::_beforeToHtml();
//    }
}