<?php

class TM_KnowledgeBase_Block_Adminhtml_Faq_Edit_Tab_Content
extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getStoreConfig('helpmate/ticketForm/wysiwyg') &&
            Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {

            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

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
            array(
                'legend' => Mage::helper('knowledgebase')->__('Content'),
                'class'=>'fieldset-wide'
            )
        );

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
            'tab_id'        => $this->getTabId(),
            'add_variables' => true,
            'add_widgets'   => true,
            'files_browser_window_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg_images/index'),
            'directives_url' => Mage::getSingleton('adminhtml/url')->getUrl('adminhtml/cms_wysiwyg/directive'),
            'add_directives' => true
//            'width' => '200%'
        ));
        $contentField = $fieldset->addField('content', 'editor', array(
            'label'    => Mage::helper('knowledgebase')->__('Content'),
            'class'    => 'required-entry',
            'required' => true,
            'name'     => 'content',
            'style'    => 'height:16em',
            'config'   => $wysiwygConfig,
            'wysiwyg'  => Mage::getStoreConfig('helpmate/ticketForm/wysiwyg'),
        ));
        // Setting custom renderer for content field to remove label column
        $renderer = $this->getLayout()->createBlock('adminhtml/widget_form_renderer_fieldset_element')
                    ->setTemplate('cms/page/edit/form/renderer/content.phtml');
        $contentField->setRenderer($renderer);

        $form->setValues($data);
        return parent::_prepareForm();
    }

        /**
    * Prepare label for tab
    *
    * @return string
    */
    public function getTabLabel()
    {
        return Mage::helper('knowledgebase')->__('Content');
    }

    /**
    * Prepare title for tab
    *
    * @return string
    */
    public function getTabTitle()
    {
        return Mage::helper('knowledgebase')->__('Content');
    }

    /**
    * Returns status flag about this tab can be shown or not
    *
    * @return true
    */
    public function canShowTab()
    {
        return true;
    }

    /**
    * Returns status flag about this tab hidden or not
    *
    * @return true
    */
    public function isHidden()
    {
        return false;
    }
}