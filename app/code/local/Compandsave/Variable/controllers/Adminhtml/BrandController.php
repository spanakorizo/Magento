<?php
class Compandsave_Variable_Adminhtml_BrandController
    extends Mage_Adminhtml_Controller_Action
{
    /**
     * Instantiate our grid container block and add to the page content.
     * When accessing this admin index page, we will see a grid of all
     * brand variable currently available in our Magento instance, along with
     * a button to add a new one if we wish.
     */
    public function indexAction()
    {
        // instantiate the grid container
        $BrandBlock = $this->getLayout()
            ->createBlock('compandsave_variable_adminhtml/brand');

        // Add the grid container as the only item on this page
        $this->loadLayout()
            ->_addContent($BrandBlock)
            ->renderLayout();
    }

    /**
     * This action handles both viewing and editing existing brands.
     */
    public function editAction()
    {
        /**
         * Retrieve existing Coupon data if an ID was specified.
         * If not, we will have an empty Coupon entity ready to be populated.
         */
        $Brand = Mage::getModel('compandsave_variable/brand');
        if ($BrandId = $this->getRequest()->getParam('id', false)) {
            $Brand->load($BrandId);

            if ($Brand->getId() < 1){
				$this->_getSession()->addError(
                    $this->__('This Brand no longer exists.')
                );
                return $this->_redirect('compandsave_variable_admin/brand/index');
            }
        }

        // process $_POST data if the form was submitted
        if ($postData = $this->getRequest()->getPost('BrandData')) {
            try {
                $Brand->addData($postData);
                $Brand->save();

                $this->_getSession()->addSuccess(
                    $this->__('The Brand HTML "'. $postData['value'] .'" has been saved.')
                );

                // redirect to remove $_POST data from the request
                return $this->_redirect(
                    'compandsave_variable_admin/brand/index',
                    array('id' => $Brand->getId())
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

        // Make the current brand object available to blocks.
        Mage::register('current_brand', $Brand);

        // Instantiate the form container.
        $brandEditBlock = $this->getLayout()->createBlock(
            'compandsave_variable_adminhtml/brand_edit'
        );

        // Add the form container as the only item on this page.
        $this->loadLayout()
            ->_addContent($brandEditBlock)
            ->renderLayout();
    }

    public function deleteAction()
    {
        $Brand = Mage::getModel('compandsave_variable/brand');

        if ($brandId = $this->getRequest()->getParam('id', false)) {
            $Brand->load($brandId);
        }

        if ($Brand->getId() < 1){
			$this->_getSession()->addError(
				$this->__('This Brand no longer exists.')
			);
			return $this->_redirect('compandsave_variable_admin/brand/index');
		}

        try {
            $Brand->delete();

            $this->_getSession()->addSuccess(
                $this->__('The Brand "'. $Brand->getValue() .'" has been deleted.')
            );
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
        }

        return $this->_redirect(
            'compandsave_variable_admin/brand/index'
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
         * - - - - - smashingmagazine_branddirectory
         * - - - - - - children
         * - - - - - - - brand
         *
         * eg. you could add more rules inside brand for edit and delete.
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
                    ->isAllowed('compandsave_variable/brand');
                break;
        }

        return $isAllowed;
    }
}