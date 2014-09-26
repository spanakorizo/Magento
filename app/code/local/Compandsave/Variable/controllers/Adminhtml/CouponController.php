<?php
class Compandsave_Variable_Adminhtml_CouponController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Instantiate our grid container block and add to the page content.
     * When accessing this admin index page, we will see a grid of all
     * coupon variable currently available in our Magento instance, along with
     * a button to add a new one if we wish.
     */
    public function indexAction()
    {
        // instantiate the grid container
        $CouponBlock = $this->getLayout()
            ->createBlock('compandsave_variable_adminhtml/coupon');

        // Add the grid container as the only item on this page
        $this->loadLayout()
            ->_addContent($CouponBlock)
            ->renderLayout();
    }

    /**
     * This action handles both viewing and editing existing coupons.
     */
    public function editAction()
    {
        /**
         * Retrieve existing Coupon data if an ID was specified.
         * If not, we will have an empty Coupon entity ready to be populated.
         */
        $Coupon = Mage::getModel('compandsave_variable/coupon');
        if ($CouponId = $this->getRequest()->getParam('id', false)) {
            $Coupon->load($CouponId);

            if ($Coupon->getId() < 1){
				$this->_getSession()->addError(
                    $this->__('This Coupon no longer exists.')
                );
                return $this->_redirect('compandsave_variable_admin/coupon/index');
            }
        }

        // process $_POST data if the form was submitted
        if ($postData = $this->getRequest()->getPost('CouponData')) {
            try {
                $Coupon->addData($postData);
                $Coupon->save();

                $this->_getSession()->addSuccess(
                    $this->__('The Coupon Code "'. $postData['value'] .'" has been saved.')
                );

                // redirect to remove $_POST data from the request
                return $this->_redirect(
                    'compandsave_variable_admin/coupon/index',
                    array('id' => $Coupon->getId())
                );
            } catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
            }

            /**
             * If we get to here, then something went wrong. Continue to
             * render the page as before, the difference this time being
             * that the submitted $_POST data is available.
             */
        }

        // Make the current coupon object available to blocks.
        Mage::register('current_coupon', $Coupon);

        // Instantiate the form container.
        $couponEditBlock = $this->getLayout()->createBlock(
            'compandsave_variable_adminhtml/coupon_edit'
        );

        // Add the form container as the only item on this page.
        $this->loadLayout()
            ->_addContent($couponEditBlock)
            ->renderLayout();
    }

    public function deleteAction()
    {
        $Coupon = Mage::getModel('compandsave_variable/coupon');

        if ($CouponId = $this->getRequest()->getParam('id', false)) {
            $Coupon->load($CouponId);
        }

        if ($Coupon->getId() < 1){
			$this->_getSession()->addError(
				$this->__('This Coupon no longer exists.')
			);
			return $this->_redirect('compandsave_variable_admin/coupon/index');
		}

        try {
            $Coupon->delete();

            $this->_getSession()->addSuccess(
                $this->__('The Coupon "'. $Coupon->getValue() .'" has been deleted.')
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect(
            'compandsave_variable_admin/coupon/index'
        );
    }

    /**
     * Thanks to Ben for pointing out this method was missing. Without
     * this method the ACL rules configured in adminhtml.xml are ignored.
     */
    protected function _isAllowed()
    {
        /**
         * we include this switch to demonstrate that you can add action
         * level restrictions in your ACL rules. The isAllowed() method will
         * use the ACL rule we have configured in our adminhtml.xml file:
         * - acl
         * - - resources
         * - - - admin
         * - - - - children
         * - - - - - smashingmagazine_coupondirectory
         * - - - - - - children
         * - - - - - - - coupon
         *
         * eg. you could add more rules inside coupon for edit and delete.
         */
        $actionName = $this->getRequest()->getActionName();
        switch ($actionName) {
            case 'index':
            case 'edit':
            case 'delete':
                // intentionally no break
            default:
                $adminSession = Mage::getSingleton('admin/session');
                $isAllowed = $adminSession
                    ->isAllowed('compandsave_variable/coupon');
                break;
        }

        return $isAllowed;
    }
}