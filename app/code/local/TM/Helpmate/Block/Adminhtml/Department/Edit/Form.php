<?php

class TM_Helpmate_Block_Adminhtml_Department_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('helpmate_department_form');
        $this->setTitle(Mage::helper('helpmate')->__('Department Information'));
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

        if (Mage::registry('helpmate_department_data') ) {
            $data = Mage::registry('helpmate_department_data')->getData();
        }

        $fieldsetGeneral = $form->addFieldset(
            'department_general_form',
            array('legend' => Mage::helper('helpmate')->__('General'))
        );
        $fieldsetGeneral->addField('id', 'hidden', array(
        //  'class'     => 'required-entry',
            'name'      => 'id'
        ));
        $fieldsetGeneral->addField('active', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Show on frontend'),
            'name'      => 'active',
//            'required'  => true,
            'values'    => array(

                array(
                  'value'     => 0,
                  'label'     => Mage::helper('helpmate')->__('No'),
                ),
                array(
                  'value'     => 1,
                  'label'     => Mage::helper('helpmate')->__('Yes'),
                )
            ),
        ));

        $fieldsetGeneral->addField('name', 'text', array(
            'label'     => Mage::helper('helpmate')->__('Text'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
        	$fieldsetGeneral->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('helpmate')->__('Store View'),
                'title'     => Mage::helper('helpmate')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')
                    ->getStoreValuesForForm(false, true),
            ));
        }
//        else {
//            $fieldset->addField('store_id', 'hidden', array(
//                'name'      => 'store_id',
////                'value'     => Mage::app()->getStore(true)->getId()
//            ));
//        }

        $fieldsetGeneral->addField('created_at', 'date', array(
            'label'     => Mage::helper('helpmate')->__('Create date'),
//            'required'  => true,
//            'disabled'  => true,
            'image'     => $this->getSkinUrl('images/grid-cal.gif'),
            'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
            //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
            'name'      => 'created_at',
        ));
        $users = array();
        foreach (Mage::getModel('admin/user')->getCollection() as $user) {
            $users[] = array(
                'value' => $user->user_id,
                'label' => $user->username
            );
        }
//        Varien_Data_Form_Element_
        $fieldsetGeneral->addField('users', 'multiselect', array(
            'label'     => Mage::helper('helpmate')->__('Department Users'),
            'name'      => 'users',
            'values'    => $users
        ));


        $fieldsetEmail = $form->addFieldset(
            'department_email_form',
            array('legend' => Mage::helper('helpmate')->__('Email'))
        );
//        $fieldsetEmail->addField('notified', 'select', array(
//            'label'     => Mage::helper('helpmate')->__('Notify'),
//            'name'      => 'notified',
//            'values'    => array(
//
//                array(
//                  'value'     => 0,
//                  'label'     => Mage::helper('helpmate')->__('No'),
//                ),
//                array(
//                  'value'     => 1,
//                  'label'     => Mage::helper('helpmate')->__('Yes'),
//                )
//            ),
//        ));
//        $fieldsetEmail->addField('email', 'text', array(
//            'label'     => Mage::helper('helpmate')->__('Email'),
//            'name'      => 'email',
//        ));
        $gateways = array(array(
            'value' => '',
            'label' => ''
        ));
        foreach (Mage::getModel('tm_email/gateway_storage')->getOptionArray() as $value => $label) {
            $gateways[] = array(
                'value' => $value,
                'label' => $label
            );
        }
//        Varien_Data_Form_Element_
        $fieldsetGeneral->addField('gateway_id', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Gateway'),
            'name'      => 'gateway_id',
            'required'  => true,
            'values'    => $gateways
        ));

        $fieldsetEmail->addField('sender', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Sender'),
            'name'      => 'sender',
            'values'    => Mage::getSingleton('adminhtml/system_config_source_email_identity')->toOptionArray()
        ));

        $emailSourceModel = Mage::getSingleton('adminhtml/system_config_source_email_template');
        $emailSourceModel->setPath('helpmate_email_ticket_notification');
        $fieldsetEmail->addField('email_template_new', 'select', array(
            'label'     => Mage::helper('helpmate')->__('New Ticket Template'),
            'name'      => 'email_template_new',
            'values'    => $emailSourceModel->toOptionArray()
        ));

        $emailSourceModel->setPath('helpmate_email_theard_notification');
        $fieldsetEmail->addField('email_template_admin', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Admin Ticket Template'),
            'name'      => 'email_template_admin',
            'values'    => $emailSourceModel->toOptionArray()
        ));

        $emailSourceModel->setPath('helpmate_email_ticket_answer');
        $fieldsetEmail->addField('email_template_answer', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Answer on Ticket Template'),
            'name'      => 'email_template_answer',
            'values'    => $emailSourceModel->toOptionArray()
        ));

        $form->setValues($data);

        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
