


<?php
class Compandsave_Functions_Block_Adminhtml_Coupon
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        parent::_construct();
        //echo "I am in the block/Duplicate.php";
        //$this->setTemplate('cas_functions/duplicate/hello.phtml');

        /**
         * The $_blockGroup property tells Magento which alias to use to
         * locate the blocks to be displayed in this grid container.
         * In our example, this corresponds to Variable/Block/Adminhtml.
         */
        $this->_blockGroup = 'compandsave_functions_adminhtml';

        /**
         * $_controller is a slightly confusing name for this property.
         * This value, in fact, refers to the folder containing our
         * Grid.php and Edit.php - in our example,
         * Variable/Block/Adminhtml/Coupon. So, we'll use 'Coupon'.
         */
        $this->_controller = 'duplicate';

        /**
         * The title of the page in the admin panel.
         */
        $this->_headerText = Mage::helper('compandsave_functions')
            ->__('Customize Functions');
    }

    public function getCreateUrl()
    {
        /**
         * When the "Add" button is clicked, this is where the user should
         * be redirected to - in our example, the method editAction of
         * CouponController.php in Variable module.
         */
        return $this->getUrl(
            'compandsave_functions_admin/duplicate/edit'
        );
    }
}