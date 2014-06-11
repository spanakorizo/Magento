<?php

class TM_Helpmate_Block_Adminhtml_Gateway_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{

    /**
     * Init form
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('helpmate_gateway_form');
        $this->setTitle(Mage::helper('helpmate')->__('Gateway Information'));
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

        if (Mage::registry('helpmate_gateway_data') ) {
            $data = Mage::registry('helpmate_gateway_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'gateway_general_form',
            array('legend' => Mage::helper('helpmate')->__('Email Gateway Details'))
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

        $fieldset->addField('email', 'text', array(
            'label'     => Mage::helper('helpmate')->__('Email'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'email',
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

        $fieldset->addField('type', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Type'),
            'name'      => 'type',
            'required'  => true,
            'values'    => array(

                array(
                    'value'     => 1,//TM_Helpmate_Model_Gateway::TYPE_POP3,
                    'label'     => Mage::helper('helpmate')->__('Pop3')
                ),
                array(
                    'value'     => 2,//TM_Helpmate_Model_Gateway::TYPE_IMAP,
                    'label'     => Mage::helper('helpmate')->__('Imap')
                )
            ),
        ));

        $fieldset->addField('host', 'text', array(
            'label'     => Mage::helper('helpmate')->__('Host'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'host',
        ));

        $fieldset->addField('user', 'text', array(
            'label'     => Mage::helper('helpmate')->__('User'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'user',
        ));

        $fieldset->addField('password', 'password', array(
            'label'     => Mage::helper('helpmate')->__('Password'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'password',
        ));

        $fieldset->addField('port', 'text', array(
            'label'     => Mage::helper('helpmate')->__('Port'),
            'class'     => 'required-entry',
            'required'  => true,
            'note'      => Mage::helper('helpmate')->__(
                '110 for POP3, 995 for POP3-SSL, 143 for IMAP-TLS and 993 for IMAP-SSL by default'
            ),
            'name'      => 'port',
        ));

        $fieldset->addField('secure', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Secure'),
            'name'      => 'secure',
            'required'  => true,
            'values'    => array(
                array(
                    'value'     => 0,
                    'label'     => Mage::helper('helpmate')->__('None'),
                ),
                array(
                    'value'     => 1,
                    'label'     => Mage::helper('helpmate')->__('SSL/TLS'),
                ),
                array(
                    'value'     => 2,
                    'label'     => Mage::helper('helpmate')->__('STARTTLS'),
                )
            ),
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