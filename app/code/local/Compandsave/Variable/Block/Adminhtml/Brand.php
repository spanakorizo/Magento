<?php
class Compandsave_Variable_Block_Adminhtml_Brand
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        parent::_construct();

        /**
         * The $_blockGroup property tells Magento which alias to use to
         * locate the blocks to be displayed in this grid container.
         * In our example, this corresponds to Variable/Block/Adminhtml.
         */
        $this->_blockGroup = 'compandsave_variable_adminhtml';

        /**
         * $_controller is a slightly confusing name for this property.
         * This value, in fact, refers to the folder containing our
         * Grid.php and Edit.php - in our example,
         * Variable/Block/Adminhtml/Coupon. So, we'll use 'Coupon'.
         */
        $this->_controller = 'Brand';

        /**
         * The title of the page in the admin panel.
         */
        $this->_headerText = Mage::helper('compandsave_variable')
            ->__('CompAndSave Variable');
    }

    public function getCreateUrl()
    {
        /**
         * When the "Add" button is clicked, this is where the user should
         * be redirected to - in our example, the method editAction of
         * CouponController.php in Variable module.
         */
        return $this->getUrl(
            'compandsave_variable_admin/brand/edit'
        );
    }
}