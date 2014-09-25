<?php

class TM_Helpmate_Block_Adminhtml_Theard_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
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
        $id = $this->getRequest()->getParam('id');

        $form = new Varien_Data_Form(array(
            'id'     => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $id)),
            'method' => 'post'
        ));

        if (Mage::registry('helpmate_theard_data') ) {
            $data = Mage::registry('helpmate_theard_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'helpmate_form',
            array('legend'=>Mage::helper('helpmate')->__('Answer information'))
        );
        
        $fieldset->addField('id', 'hidden', array(
          'class'     => 'required-entry',
          'name'      => 'id'
        ));
        
        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
            'tab_id'        => $this->getTabId(),
            'add_variables' => false,
            'add_widgets'   => false,
        ));
        $fieldset->addField('text', 'editor', array(
          'label'     => Mage::helper('helpmate')->__('Text'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'text',
          'style'     => 'height:6em',
          'config'    => $wysiwygConfig
        ));
        $departments = array();
        foreach (Mage::getModel('helpmate/department')->getCollection() as $department) {
            $departments[] = array(
                'value' => $department->id,
                'label' => $department->name
            );
        }
        $fieldset->addField('department_id', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Department'),
            'name'      => 'department_id',
            'values'    => $departments
        ));


        $statuses = array();
        foreach (Mage::getModel('helpmate/status')->getOptionArray() as $key => $value) {
            $statuses[] = array(
                'value' => $key,
                'label' => $value
            );
        }
        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Status'),
            'name'      => 'status',
            'values'    => $statuses
        ));

        $priorities = array();
        foreach (Mage::getModel('helpmate/priority')->getOptionArray() as $key => $value) {
            $priorities[] = array(
                'value' => $key,
                'label' => $value
            );
        }
        $fieldset->addField('priority', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Priority'),
            'name'      => 'priority',
            'values'    => $priorities
        ));

        $fieldset->addField('created_at', 'date', array(
            'label'     => Mage::helper('helpmate')->__('Create date'),
//            'required'  => true,
            'disabled'  => true,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
            //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
            'name'      => 'created_at',
        ));
        
        $form->setValues($data);
        $form->setUseContainer(true);
        $this->setForm($form);
        
        return parent::_prepareForm();
    }
}