<?php

class TM_KnowledgeBase_Block_Adminhtml_Faq_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
//
//    /**
//     * Init form
//     */
//    public function __construct()
//    {
//        parent::__construct();
//        $this->setId('knowledgebase_faq_form');
//        $this->setTitle(Mage::helper('knowledgebase')->__('Articles Information'));
//    }
//
//    /**
//     * Load Wysiwyg on demand and Prepare layout
//     */
//    protected function _prepareLayout()
//    {
//        parent::_prepareLayout();
//        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
//            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
//        }
//    }

    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $id)),
                'method' => 'post',
            )
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}