<?php

class TM_Helpmate_Block_Adminhtml_Ticket_Edit_Tab_Notes extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $id)),
            'method' => 'post'
        ));
        $this->setForm($form);

         if (Mage::registry('helpmate_ticket_data') ) {
            $data = Mage::registry('helpmate_ticket_data')->getData();
        }

        $fieldset = $form->addFieldset(
            'ticket_general_form',
            array('legend' => Mage::helper('helpmate')->__('Notes'))
        );
        $fieldset->addField('id', 'hidden', array(
        //  'class'     => 'required-entry',
            'name'      => 'id'
        ));

//        $orders = array(array(
//            'value' => null,
//            'label' => ''
//        ));
//        foreach (Mage::getModel('sales/order')->getCollection() as $order) {
//            $orders[] = array(
//                'value' => $order->getId(),
//                'label' => $order->getRealOrderId()
//            );
//        }
//        $fieldset->addField('order_id', 'select', array(
//            'label'     => Mage::helper('helpmate')->__('Order Number'),
//            'name'      => 'order_id',
//            'values'    => $orders,
//            'note'      => Mage::helper('helpmate')->__('Exist orders list')
//        ));
//        Zend_Debug::dump($data);
//        $_order = Mage::getModel('sales/order')->load($data['order_id']);
//        if ($_order) {
//            $data['order_number'] = $_order->getNumber();
//        }
//        $fieldset->addType(
//            'helpmate_autocompleter', 
//            'TM_Helpmate_Block_Adminhtml_Ticket_Edit_Form_Element_Autocompleter'
//        );
//        $fieldset->addField('order_id', 'helpmate_autocompleter', array(
//            'label'     => Mage::helper('helpmate')->__('Order Number'),
//            'name'      => 'order_id',
////            'note'      => Mage::helper('helpmate')->__('Exist orders list')
//            'autocompleterUrl'   => Mage::getUrl('*/*/order'),
//            'autocompleterValue' => isset($data['order_number']) ? $data['order_number'] : ''
//        ));
//
//        $orderId = $data['order_id'];
//        $order = Mage::getModel('sales/order')->load($orderId);
//        if ($order instanceof Mage_Sales_Model_Order) {
//            $data['order_link'] = $order->getRealOrderId();
//            $link = Mage::getModel('adminhtml/url')
//                ->setRouteName('adminhtml')
//                ->getUrl(
//                    '*/sales_order/view',
//                    array('id' => $orderId)
//            );
//
//            $adminRoute = (string) Mage::getConfig()->getNode(
//                'admin/routers/adminhtml/args/frontName'
//            );
//
//            $link = str_replace('/helpmate/', '/' . $adminRoute . '/', $link); //@todo
//            $fieldset->addField('order_link', 'link', array(
//                'href'      => $link,
//                'label'     => Mage::helper('helpmate')->__('Order Info'),
////                'disabled'  => true,
//                'name'      => 'order_link'
//            ));
//        }

        $fieldset->addField('notes', 'textarea', array(
          'label'     => Mage::helper('helpmate')->__('Notes'),
          'name'      => 'notes'
        ));

        $form->setValues($data);
        $onclick = "if ($('notes').value == '') " .
                " $('notes').addClassName('required-entry validation-failed');" .
                "editForm.submit();return false;";
        $fieldset->addField('add', 'button', array(
           'value' => Mage::helper('helpmate')->__('Add Notes'),
           'class' => 'form-button',
           'name'  => 'add_comment_button',
           'onclick' => $onclick
        ));
        return parent::_prepareForm();
    }
}