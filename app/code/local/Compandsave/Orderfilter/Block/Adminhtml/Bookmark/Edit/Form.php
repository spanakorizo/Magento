<?php
class Compandsave_OrderFilter_Block_Adminhtml_Bookmark_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save',
                array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post'
        ));
        $fieldset = $form->addFieldset('general_form', array(
            'legend' => $this->__('General Setup')
        ));
        if (is_object(Mage::registry('current_bookmark')) &&
            Mage::registry('current_bookmark')->getId()) {
            $fieldset->addField('entity_id', 'label', array(
                'label' => $this->__('Entity ID:'),
            ));
        }
        $fieldset->addField('name', 'text', array(
            'label' => $this->__('Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'name',
        ));
        $fieldset->addField('filter_sql', 'textarea', array(
            'label' => $this->__('SQL Filter'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'filter_sql',
        ));

        $form->setUseContainer(true);
        $form->addValues($this->_getFormData());
        $this->setForm($form);
    }


    protected function _getFormData()
    {
        $data = Mage::getSingleton('adminhtml/session')->getFormData();
        if (! $data && Mage::registry('current_bookmark')->getData()) {
            $data = Mage::registry('current_bookmark')->getData();
        }
        return (array) $data;
    }
}