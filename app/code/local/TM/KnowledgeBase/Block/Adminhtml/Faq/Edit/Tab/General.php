<?php

class TM_KnowledgeBase_Block_Adminhtml_Faq_Edit_Tab_General
    extends Mage_Adminhtml_Block_Widget_Form
        implements Mage_Adminhtml_Block_Widget_Tab_Interface
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
            array('legend' => Mage::helper('knowledgebase')->__('Article Information'))
        );
        $fieldset->addField('id', 'hidden', array(
        //  'class'     => 'required-entry',
            'name'      => 'id'
        ));

        $fieldset->addField('title', 'text', array(
            'label'     => Mage::helper('knowledgebase')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));

        $fieldset->addField('identifier', 'text', array(
            'label'     => Mage::helper('knowledgebase')->__('Url'),
//            'class'     => 'required-entry',
//            'required'  => true,
            'name'      => 'identifier',
            'class'     => 'validate-identifier',
        ));

        $categories = array();
        foreach (Mage::getModel('knowledgebase/category')->getCollection() as $category) {

            $categories[] = array(
                'value' => $category->id,
                'label' => $category->name
            );
        }
        $fieldset->addField('categories', 'multiselect', array(
            'label'     => Mage::helper('knowledgebase')->__('Categories'),
            'name'      => 'categories',
            'values'    => $categories,
//            'required'  => true,
        ));

        if (!Mage::app()->isSingleStoreMode()) {
        	$fieldset->addField('stores', 'multiselect', array(
                'name'      => 'stores',
                'label'     => Mage::helper('knowledgebase')->__('Store View'),
                'title'     => Mage::helper('knowledgebase')->__('Store View'),
//                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')
                    ->getStoreValuesForForm(false, true),
            ));
        }

        $authors = array();
        foreach (Mage::getModel('admin/user')->getCollection() as $user) {
            $authors[] = array(
                'value' => $user->user_id,
                'label' => $user->username
            );
        }
//        Varien_Data_Form_Element_
        $fieldset->addField('author', 'select', array(
            'label'     => Mage::helper('knowledgebase')->__('Author'),
            'name'      => 'author',
            'values'    => $authors
        ));

        $fieldset->addField('rate', 'text', array(
            'label'     => Mage::helper('knowledgebase')->__('Rate'),
            'name'      => 'rate',
        ));

        $fieldset->addField('created_at', 'date', array(
            'label'     => Mage::helper('knowledgebase')->__('Create date'),
//            'required'  => true,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
            //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
            'name'      => 'created_at',
            'disabled'  => true
        ));

        $fieldset->addField('modified_at', 'date', array(
            'label'     => Mage::helper('knowledgebase')->__('Modified date'),
//            'required'  => true,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
            //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
            'name'      => 'modified_at',
            'disabled'  => true
        ));

        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('knowledgebase')->__('Sort order'),
            'name'      => 'sort_order',
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('knowledgebase')->__('Status'),
            'name'      => 'status',
//            'required'  => true,
            'values'    => array(

                array(
                  'value'     => 0,
                  'label'     => Mage::helper('knowledgebase')->__('Disabled'),
                ),
                array(
                  'value'     => 1,
                  'label'     => Mage::helper('knowledgebase')->__('Enabled'),
                )
            ),
        ));

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
        return Mage::helper('knowledgebase')->__('Article Information');
    }

   /**
    * Prepare title for tab
    *
    * @return string
    */
    public function getTabTitle()
    {
        return Mage::helper('knowledgebase')->__('Article Information');
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