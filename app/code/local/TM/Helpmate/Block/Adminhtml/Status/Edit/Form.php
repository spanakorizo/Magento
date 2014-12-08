<?php

class TM_Helpmate_Block_Adminhtml_Status_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('helpmate_status_form');
        $this->setTitle(Mage::helper('helpmate')->__('Status Information'));
    }

    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        $form = new Varien_Data_Form(array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('id' => $id)),
                'method' => 'post',
            )
        );

        if (Mage::registry('helpmate_status_data') ) {
            $data = Mage::registry('helpmate_status_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'status_general_form',
            array('legend' => Mage::helper('helpmate')->__('Status Details'))
        );
        $fieldset->addField('id', 'hidden', array(
        //  'class'     => 'required-entry',
            'name'      => 'id'
        ));

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('helpmate')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('helpmate')->__('Disabled')
                ),
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('helpmate')->__('Enabled')
                )
            ),
        ));

        $fieldset->addField('sort_order', 'text', array(
            'label'     => Mage::helper('helpmate')->__('Sort Order'),
            'name'      => 'sort_order',
        ));

        /*
        $fieldset->addField('remove', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Remove'),
            'name'      => 'remove',
            'required'  => true,
            'values'    => array(

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('helpmate')->__('Disabled')
                ),
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('helpmate')->__('Enabled')
                )
            ),
        ));
        */
        $form->setValues($data);

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}