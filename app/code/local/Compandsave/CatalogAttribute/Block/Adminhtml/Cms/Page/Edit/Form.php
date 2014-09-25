<?php
class Compandsave_CatalogAttribute_Block_Adminhtml_Cms_Page_Edit_Form extends Mage_Adminhtml_Block_Cms_Block_Edit_Form
{
		
    /**
     * Load Wysiwyg on demand and Prepare layout
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
            $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        }
    }

    protected function _prepareForm()
    {
        $model = Mage::registry('cms_block');

        $form = new Varien_Data_Form(
            array('id' => 'edit_form',  'method' => 'post')
        );

        $form->setHtmlIdPrefix('block_');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('cms')->__('General Information'), 'class' => 'fieldset-wide'));

        

       $fieldset->addField('canonical_page', 'text', array(
            'name'     => 'canonical_page', // This is the same as the column you had created within the cms_page table
            'label'    => 'Canonical URL',// This is the backend label of the field
            'required' => false
        	)
		);
        $this->setForm($form);

        return parent::_prepareForm();
    }

}