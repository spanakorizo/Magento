<?php
class Compandsave_Functions_Adminhtml_DuplicateController
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
        /*
        $DuplicateBlock = $this->getLayout()
            ->createBlock('compandsave_functions_adminhtml/duplicate');

        // Add the grid container as the only item on this page
        $this->loadLayout()
            ->_addContent($DuplicateBlock)
            ->renderLayout();*/
        
        $this->loadLayout();
        /*
        $block = $this->getLayout()->createBlock(
        'Mage_Adminhtml_Block_Template',
        'duplicatepage',
        array('template' => 'cas_functions/duplicate/hello.phtml')
        );*/
        $block = $this->getLayout()->createBlock('adminhtml/template')->setTemplate('cas_functions/duplicate/view.phtml');
        $this->getLayout()->getBlock('content')->append($block);
        
        $this->renderLayout();
    }

    /**
     * This action handles both viewing and editing existing coupons.
     */
/*
    public function editAction()
    {
        
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
    */
/*
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
*/
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
                // intentionally no break
            default:
                $adminSession = Mage::getSingleton('admin/session');
                $isAllowed = $adminSession
                    ->isAllowed('compandsave_Functions/duplicate');
                break;
        }

        return $isAllowed;
    }
}