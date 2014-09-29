<?php
class Compandsave_OrderFilter_Adminhtml_OrderfilterController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')
            ->isAllowed('sales/compandsave_orderfilter');
    }
    public function indexAction()
    {
        $this->_getSession()->setFormData(null); //housekeeping
        $this->_title($this->__('Order Filters'));
        $this->loadLayout();
        $this->_setActiveMenu('sales');
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
    public function editAction()
    {
        $model = Mage::getModel('compandsave_orderfilter/bookmark');
        Mage::register('current_bookmark', $model);
        $id = $this->getRequest()->getParam('id');
        try {
            if ($id) {
                if (!$model->load($id)->getId()) {
                    Mage::throwException(
                        $this->__('No record with ID "%s" found.', $id)
                    );
                }
            }
            if ($model->getId()) {
                $pageTitle =
                    $this->__('Edit %s (%s)', $model->getName(), $model->getId());
            }
            else {
                $pageTitle = $this->__('New Filter');
            }
            $this->_title($this->__('Order Filter'))
                ->_title($this->__('Bookmarks'))
                ->_title($pageTitle);

            $this->loadLayout();
            $this->_setActiveMenu('sales');
            $this->renderLayout();
        }
        catch (Exception $e)
        {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }

    public function newAction()
    {
        // Redirect the user via a magento internal redirect
        $this->_forward('edit');
    }

    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost())
        {
            $this->_getSession()->setFormData($data);
            $model = Mage::getModel('compandsave_orderfilter/bookmark');
            $id = $this->getRequest()->getParam('id');
            try {
                if ($id) {
                    $model->load($id);
                }
                $model->addData($data)->save();
                $this->_getSession()->addSuccess(
                    $this->__('Filter was successfully saved'));
                $this->_getSession()->setFormData(null);

                if ($this->getRequest()->getParam('back')) {
                    $params = array('id' => $model->getId());
                    $this->_redirect('*/*/edit', $params);
                }
                else {
                    $this->_redirect('*/*');
                }
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                if ($model && $model->getId()) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                }
                else {
                    $this->_redirect('*/*/new');
                }
            }
            return;
        }
        $this->_getSession()->addError($this->__('No data found to save'));
        $this->_redirect('*/*');
    }

    public function deleteAction()
    {
        $model = Mage::getModel('compandsave_orderfilter/bookmark');
        $id = $this->getRequest()->getParam('id');
        try {
            if ($id) {
                if (!$model->load($id)->getId()) {
                    Mage::throwException(
                        $this->__('No record with ID "%s" found.', $id)
                    );
                }
                $name = $model->getName();
                $model->delete();
                $this->_getSession()->addSuccess($this->__('"%s" (ID %d) was successfully deleted', $name, $id));
                $this->_redirect('*/*');
            }
        }
        catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->_redirect('*/*');
        }
    }
}