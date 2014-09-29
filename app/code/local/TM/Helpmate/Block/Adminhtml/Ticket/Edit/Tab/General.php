<?php

class TM_Helpmate_Block_Adminhtml_Ticket_Edit_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');

        $form = new Varien_Data_Form(array(
            'id'      => 'edit_form',
            'action'  => $this->getUrl('*/*/save', array('id' => $id)),
            'method'  => 'post'

        ));

        $this->setForm($form);

        if (Mage::registry('helpmate_ticket_data') ) {
            $data = Mage::registry('helpmate_ticket_data')->getData();
        }
        $isOldTicket = isset($data['id']);
        $fieldsetGeneral = $form->addFieldset(
            'ticket_general_form',
            array('legend' => Mage::helper('helpmate')->__('General Details'))
        );
        $fieldsetGeneral->addField('id', 'hidden', array(
            'name'      => 'id'
        ));

        $fieldsetGeneral->addField('number', 'hidden', array(
            'name'      => 'number'
        ));

        $fieldsetGeneral->addField('title', 'text', array(
            'label'     => Mage::helper('helpmate')->__('Title'),
            'class'     => 'required-entry',
            'required'  => true,
            'name'      => 'title',
        ));
        if ($isOldTicket) {
            $fieldsetGeneral->addField('created_at', 'date', array(
                'label'     => Mage::helper('helpmate')->__('Create date'),
    //            'required'  => true,
                'disabled'  => $isOldTicket,
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
                //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                'name'      => 'created_at',
            ));

            $fieldsetGeneral->addField('modified_at', 'date', array(
                'label'     => Mage::helper('helpmate')->__('Modified date'),
    //            'required'  => true,
                'disabled'  => $isOldTicket,
                'image'     => $this->getSkinUrl('images/grid-cal.gif'),
                'format'    => Varien_Date::DATETIME_INTERNAL_FORMAT,
                //Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                'name'      => 'modified_at',
            ));
        }

        if (!Mage::app()->isSingleStoreMode()) {
            $fieldsetGeneral->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => Mage::helper('cms')->__('Store View'),
                'title'     => Mage::helper('cms')->__('Store View'),
                'required'  => true,
                'values'    => Mage::getSingleton('adminhtml/system_store')
                    ->getStoreValuesForForm(false, true),
                'note' =>  empty($data['store_id']) ?
                    Mage::helper('helpmate')->__('Please set correct store view for that ticket. Default Magento Store View (%s) will be used for sending emails when All store view is chosen.',
                        Mage::app()->getWebsite()
                            ->getDefaultGroup()
                            ->getDefaultStore()
                            ->getName()
                    ) : ''
            ));
        }
//        else {
//            $fieldset->addField('store_id', 'hidden', array(
//                'name'      => 'store_id',
////                'value'     => Mage::app()->getStore(true)->getId()
//            ));
//        }

        $departments = array();
        $collection = Mage::getModel('helpmate/department')->getCollection();
        foreach ($collection as $department) {
            $departments[] = array(
                'value' => $department->id,
                'label' => $department->name
            );
        }
        $fieldsetGeneral->addField('department_id', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Department'),
            'name'      => 'department_id',
            'values'    => $departments
        ));

        $users = array();
        $users[] = array(
            'value' => null,
            'label' => 'Not assigned'
        );

        foreach (Mage::getModel('admin/user')->getCollection() as $user) {
            $users[] = array(
                'value' => $user->user_id,
                'label' => $user->username
            );
        }

        $fieldsetGeneral->addField('user_id', 'select', array(
            'label'  => Mage::helper('helpmate')->__('Assigned'),
            'name'   => 'user_id',
            'values' => $users
        ));


        $statuses = array();
        foreach (Mage::getModel('helpmate/status')->getOptionArray() as $key => $value) {
            $statuses[] = array(
                'value' => $key,
                'label' => $value
            );
        }
        $fieldsetGeneral->addField('status', 'select', array(
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
        $fieldsetGeneral->addField('priority', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Priority'),
            'name'      => 'priority',
            'values'    => $priorities
        ));

        $fieldsetGeneral->addType(
            'helpmate_autocompleter',
            'TM_Helpmate_Block_Adminhtml_Ticket_Edit_Form_Element_Autocompleter'
        );

        $customer = null;
        if (isset($data['customer_id'])) {
            $customer = Mage::getSingleton('customer/customer')->load($data['customer_id']);
        }
        if (!$customer && !empty($data['email'])) {
            $resource = Mage::getSingleton('customer/customer')->getResource();
            $connection = $resource->getReadConnection();
            $select = $connection->select()
                ->from($resource->getEntityTable(), array($resource->getEntityIdField()))
                ->where('email=:customer_email')
                ;
            $customer = Mage::getSingleton('customer/customer')->load(
                $connection->fetchOne(
                    $select, array('customer_email' => $data['email'])
                )
            );
        }

        if ($customer && $customer->getId()) {
            $data['email'] = $customer->getEmail();
            $data['customer_link'] = $customer->getName();

            $link = Mage::helper("adminhtml")->getUrl(
                "adminhtml/customer/edit",
                    array('id' => $customer->getId())
            );

            $fieldsetGeneral->addField('customer_link', 'link', array(
                'href'      => $link,
                'label'     => Mage::helper('helpmate')->__('Customer'),
                'name'      => 'customer_link'
            ));
            $data['customer_id'] = $customer->getId();

            $fieldsetGeneral->addField('email', 'label', array(
                'label'    => Mage::helper('helpmate')->__('Email'),
                'class'    => 'required-entry',
                'required' => true,
                'disabled' => true,
                'name'     => 'email',
            ));
            $fieldsetGeneral->addField('customer_id', 'hidden', array(
                'name' => 'customer_id'
            ));
        } elseif($isOldTicket) {
            $fieldsetGeneral->addField('email', 'label', array(
                'label'    => Mage::helper('helpmate')->__('Email'),
                'class'    => 'required-entry',
                'required' => true,
                'disabled' => true,
                'name'     => 'email',
            ));
        } else {
            $fieldsetGeneral->addField('customer_id', 'helpmate_autocompleter', array(
                'label'              => Mage::helper('helpmate')->__('Email'),
                'name'               => 'customer_id',
                'autocompleterUrl'   => Mage::getUrl('*/*/customer'),
                'autocompleterValue' => '',
                'required'           => true,
            ));
        }

        $_order = Mage::getModel('sales/order');
        if (isset($data['order_id']) && !empty($data['order_id'])) {
            $_order = $_order->load($data['order_id']);
            if ($_order) {
                $data['order_number'] = $_order->getNumber();
            }
        }

        $fieldsetGeneral->addField('order_id', 'helpmate_autocompleter', array(
            'label'     => Mage::helper('helpmate')->__('Order Number'),
            'name'      => 'order_id',
//            'note'      => Mage::helper('helpmate')->__('Exist orders list')
            'autocompleterUrl'   => Mage::getUrl('*/*/order'),
            'autocompleterValue' => isset($data['order_number']) ? $data['order_number'] : ''
        ));

        $field = Mage::getStoreConfig("helpmate/ticketForm/field0");
        if(!empty($field)) {
            $fieldsetGeneral->addField('field0', 'text', array(
                'label'     => Mage::helper('helpmate')->__($field),
                'name'      => 'field0',
            ));
        }

        $field = Mage::getStoreConfig("helpmate/ticketForm/field1");
        if(!empty($field)) {
            $fieldsetGeneral->addField('field1', 'text', array(
                'label'     => Mage::helper('helpmate')->__($field),
                'name'      => 'field1',
            ));
        }

        $field = Mage::getStoreConfig("helpmate/ticketForm/field2");
        if(!empty($field)) {
            $fieldsetGeneral->addField('field2', 'text', array(
                'label'     => Mage::helper('helpmate')->__($field),
                'name'      => 'field2',
            ));
        }

        if ($_order->getId()) {
            $data['order_link'] = $_order->getRealOrderId();

            $link = Mage::helper("adminhtml")->getUrl(
                "adminhtml/sales_order/view",
                    array('order_id' => $data['order_id'])
            );
            $fieldsetGeneral->addField('order_link', 'link', array(
                'href'      => $link,
                'label'     => Mage::helper('helpmate')->__('Order Info'),
//                'disabled'  => true,
                'name'      => 'order_link'
            ));
        }

        if ($isOldTicket) {
            $fieldsetComments = $form->addFieldset(
                'ticket_comments_form',
                array(
                    'legend' => Mage::helper('helpmate')->__('Comments')
    //                'html_content' => true
                )
            );
            /////////////////////////////////////
            $fieldsetComments->addType('helpmate_theard', 'TM_Helpmate_Block_Adminhtml_Ticket_Edit_Form_Element_Theard');
            $fieldsetComments->addField('theard', 'helpmate_theard', array(
                'name'      => 'theard'
            ));
        }

        $fieldsetAddComment = $form->addFieldset(
            'ticket_add_comment_form',
            array(
                'legend' => Mage::helper('helpmate')->__('Add Comment')
            )
        );

        $faqs = array(array(
            'value' => '',
            'label' => ''
        ));
        $collection = Mage::getModel('knowledgebase/faq')->getCollection()
            ->addCategoryNamesData()
            ->addStoresData()
            ;

//        $faqcategory = explode(',', Mage::getStoreConfig('helpmate/ticketForm/faqcategory'));
//        if (!empty($faqcategory)) {
//            $collection->addCategoryIdFilter($faqcategory);
//        }

        foreach ($collection as $row) {
            $categories = explode(',', $row->getData('categorynames'));
            foreach ($categories as $category) {
                $faqs[$category] = array(
                    'label' => $category,
                    'value' => array()
                );
            }
        }

        foreach ($collection as $row) {
            $_url = '';
            if (isset($data['store_id']) && (in_array($data['store_id'], $row->getStores())
                || in_array(0, $row->getStores())) &&  (bool)$row->getStatus()) {

                $_url = Mage::getUrl('knowledgebase/index/view', array(
                    '_store' => $data['store_id'],
                    'faq' => $row->getIdentifier()
                ));
            }
            $_content = $_url . '#delimeter' . $row->content;
//            $_content = strip_tags(
//                @html_entity_decode($_content, ENT_COMPAT | ENT_HTML401, 'UTF-8')
//            );
            $_faq = array(
                'value' => $_content,
                'label' => $row->title
            );
            $categories = explode(',', $row->getData('categorynames'));
            foreach ($categories as $category) {
                $faqs[$category]['value'][] = $_faq;
            }
        }
        $onchange = "var id = 'text', content = this.value;

            var contentParts = content.split('#delimeter');

            content = contentParts[1];

            if (contentParts[0] != '' && !confirm('Insert complete article')) {
                content = 'See this link <a href=\'' + contentParts[0] + '\'>'
                + this.options[this.selectedIndex].text
                + '</a>';
            }

            $(id).value = content;
            if (typeof tinyMCE !== 'undefined') {
                tinyMCE.execInstanceCommand(id,'mceSetContent', false, content);
            }
            ";

        $fieldsetAddComment->addField('faq', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Knowledge Faq'),
            'name'      => 'faq',
            'values'    => $faqs,
            'note'      => Mage::helper('helpmate')->__('Quick answer based on predefined templates'),
            'onchange'  => $onchange
        ));


        $faqs = array(array(
            'value' => '',
            'label' => ''
        ));
        $collection = Mage::getModel('knowledgebase/faq')->getCollection()
            ->addCategoryNamesData()
            ->addStoresData()
            ;

        $faqcategory = explode(',', Mage::getStoreConfig('helpmate/ticketForm/faqcategory'));
        if (!empty($faqcategory)) {
            $collection->addCategoryIdFilter($faqcategory);
        }

        foreach ($collection as $row) {
            $categories = explode(',', $row->getData('categorynames'));
            foreach ($categories as $category) {
                $faqs[$category] = array(
                    'label' => $category,
                    'value' => array()
                );
            }
        }

        foreach ($collection as $row) {
            $_url = '';
            if ((in_array($data['store_id'], $row->getStores())
                || in_array(0, $row->getStores())) &&  (bool)$row->getStatus()) {

                $_url = Mage::getUrl('knowledgebase/index/view', array(
                    '_store' => $data['store_id'],
                    'faq' => $row->getIdentifier()
                ));
            }
            $_content = $_url . '#delimeter' . $row->content;
//            $_content = strip_tags(
//                @html_entity_decode($_content, ENT_COMPAT | ENT_HTML401, 'UTF-8')
//            );
            $_faq = array(
                'value' => $_content,
                'label' => $row->title
            );
            $categories = explode(',', $row->getData('categorynames'));
            foreach ($categories as $category) {
                $faqs[$category]['value'][] = $_faq;
            }
        }
        $onchange = "var id = 'text', content = this.value;

            var contentParts = content.split('#delimeter');

            content = contentParts[1];

//            if (contentParts[0] != '' && confirm('Set link on FAQ article')) {
//                content = 'See this link <a href=\'' + contentParts[0] + '\'>'
//                + this.options[this.selectedIndex].text
//                + '</a>';
//            }

            $(id).value = content;
            if (typeof tinyMCE !== 'undefined') {
                tinyMCE.execInstanceCommand(id,'mceSetContent', false, content);
            }
            ";

        $fieldsetAddComment->addField('faq2', 'select', array(
            'label'     => Mage::helper('helpmate')->__('Quick Answers'),
            'name'      => 'faq2',
            'values'    => $faqs,
            'note'      => 'Quick answer based on predefined templates',
            'onchange'  => $onchange
        ));

        $wysiwygConfig = Mage::getSingleton('cms/wysiwyg_config')->getConfig(array(
            'tab_id'        => $this->getTabId(),
            'add_variables' => false,
            'add_widgets'   => false,
            'width'         => '100%',
        ));

        $fieldsetAddComment->addField('text', 'editor', array(
            'label'     => Mage::helper('helpmate')->__('Comment'),
            'name'      => 'text',
            'config'    => $wysiwygConfig,
            'wysiwyg'   => Mage::getStoreConfig('helpmate/ticketForm/wysiwyg'),
            'style'     => "width: 640px"
        ));
        $fieldsetAddComment->addField('enabled', 'checkbox', array(
            'label' => Mage::helper('helpmate')->__('Hidden comment'),
            'name'  => 'enabled',
            'id'    => 'enabled',
        ));
        $fieldsetAddComment->addField('file', 'file', array(
            'name'      => 'file',
            'label'     => Mage::helper('helpmate')->__('File'),
            'title'     => Mage::helper('helpmate')->__('File')
        ));

        $form->setValues($data);
        $onclick = "if ($('text').value == '') " .
                " $('text').addClassName('required-entry validation-failed');" .
                "editForm.submit();return false;";
        $fieldsetAddComment->addField('add', 'button', array(
           'value' => Mage::helper('helpmate')->__('Add Comment'),
           'class' => 'form-button',
           'name'  => 'add_comment_button',
           'onclick' => $onclick
        ));
        return parent::_prepareForm();
    }

    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('helpmate/adminhtml_ticket_helper_file')
        );
    }
}
