<?php
class Compandsave_Variable_Block_Adminhtml_Coupon_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'compandsave_variable_adminhtml';
        $this->_controller = 'coupon';

        /**
         * The $_mode property tells Magento which folder to use
         * to locate the related form blocks to be displayed in
         * this form container. In our example, this corresponds
         * to Variable/Block/Adminhtml/Coupon/Edit/.
         */
        $this->_mode = 'edit';

        $newOrEdit = $this->getRequest()->getParam('id')
            ? $this->__('Edit')
            : $this->__('New');
        $this->_headerText =  $newOrEdit . ' ' . $this->__('coupon');
    }
}