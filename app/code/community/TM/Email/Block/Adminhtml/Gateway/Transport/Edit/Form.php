<?php

class TM_Email_Block_Adminhtml_Gateway_Transport_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tm_email_gateway_transport_form');
        $this->setTitle(Mage::helper('tm_email')->__('Mail Transport'));
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

        $data = array();
        if (Mage::registry('tm_email_gateway_transport_data') ) {
            $data = Mage::registry('tm_email_gateway_transport_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'gateway_general_form',
            array('legend' => Mage::helper('tm_email')->__('Email Transport Details'))
        );
        $fieldset->addField('id', 'hidden', array(
        //  'class'     => 'required-entry',
            'name'      => 'id'
        ));

        $fieldset->addField('name', 'text', array(
            'label'     => Mage::helper('tm_email')->__('Name'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'name',
        ));

        $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('tm_email')->__('Email'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'email',
        ));

        $fieldset->addField('status', 'select', array(
            'label'     => Mage::helper('tm_email')->__('Status'),
            'name'      => 'status',
            'required'  => true,
            'values'    => array(

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('tm_email')->__('Disabled')
                ),
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('tm_email')->__('Enabled')
                )
            ),
        ));

        $fieldset->addField('type', 'select', array(
            'label'     => Mage::helper('tm_email')->__('Type'),
            'name'      => 'type',
            'required'  => true,
            'values'    => array(
//                array(
//                    'value'     => TM_Email_Model_Gateway_Transport::TYPE_SENDMAIL,
//                    'label'     => Mage::helper('tm_email')->__('Sendmail')
//                ),
                array(
                    'value'     => TM_Email_Model_Gateway_Transport::TYPE_SMTP,
                    'label'     => Mage::helper('tm_email')->__('Smtp')
                )
            ),
        ));

        $fieldset->addField('host', 'text', array(
            'label'     => Mage::helper('tm_email')->__('Host'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'host',
        ));

        $fieldset->addField('user', 'text', array(
            'label'     => Mage::helper('tm_email')->__('User'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'user',
        ));

        $fieldset->addField('password', 'password', array(
            'label'     => Mage::helper('tm_email')->__('Password'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'password',
        ));

        $fieldset->addField('port', 'text', array(
            'label'     => Mage::helper('tm_email')->__('Port'),
            'class'     => 'required-entry',
            'required'  => true,
            'note'      => Mage::helper('tm_email')->__('25 and 587 (465) for SMTP by default'),
            'name'      => 'port',
        ));

        $fieldset->addField('secure', 'select', array(
            'label'     => Mage::helper('tm_email')->__('Secure'),
            'name'      => 'secure',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('tm_email')->__('None'),
                ),
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('tm_email')->__('SSL/TLS'),
                ),
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('tm_email')->__('STARTTLS'),
                )
            ),
        ));

        $fieldset->addField('auth', 'select', array(
            'label'     => Mage::helper('tm_email')->__('Authorize Type'),
            'name'      => 'auth',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 'login',
                    'label'     => Mage::helper('tm_email')->__('Login'),
                ),
                array(
                    'value'     => 'plain',
                    'label'     => Mage::helper('tm_email')->__('Plain'),
                ),
                array(
                    'value'     => 'crammd5',
                    'label'     => Mage::helper('tm_email')->__('Crammd5'),
                )
            ),
        ));

        /*
        $fieldset->addField('remove', 'select', array(
            'label'     => Mage::helper('tm_email')->__('Remove'),
            'name'      => 'remove',
            'required'  => true,
            'values'    => array(

                array(
                    'value'     => 0,
                    'label'     => Mage::helper('tm_email')->__('Disabled')
                ),
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('tm_email')->__('Enabled')
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