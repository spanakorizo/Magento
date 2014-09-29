<?php

class TM_KnowledgeBase_Block_Adminhtml_Faq_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{

    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        
        $form = new Varien_Data_Form(array(
//            'id'      => 'edit_form',
//            'action'  => $this->getUrl('*/*/save', array('id' => $id)),
//            'method'  => 'post'
            
        ));
        $form->setHtmlIdPrefix('faq_');
        $this->setForm($form);

        if (Mage::registry('knowledgebase_faq_data') ) {
            $data = Mage::registry('knowledgebase_faq_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'category_general_form',
            array('legend' => Mage::helper('knowledgebase')->__('Meta Data'))
        );

        $fieldset->addField('meta_keywords', 'textarea', array(
            'label'     => Mage::helper('knowledgebase')->__('Keywords'),
            'name'      => 'meta_keywords',
        ));

        $fieldset->addField('meta_description', 'textarea', array(
            'label'     => Mage::helper('knowledgebase')->__('Description'),
            'name'      => 'meta_description',
        ));

        $form->setValues($data);
        return parent::_prepareForm();
    }
}