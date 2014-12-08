<?php

class TM_Helpmate_Adminhtml_Helpmate_StatusController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction()
    {
        $this->loadLayout();
        $this->_setActiveMenu('helpmate/helpmate')
            ->_addBreadcrumb(
                Mage::helper('helpmate')->__('Gateway Manager'),
                Mage::helper('helpmate')->__('Status')
            );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $_id = $this->getRequest()->getParam('id', 0);

        $model = Mage::getModel('helpmate/status')->load($_id);
        $id = $model->getId();

        if (!$id && 0 !== $_id) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('helpmate')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('helpmate_status_data', $model);

        $this->loadLayout();
        $this->_setActiveMenu('helpmate/status');
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_redirect('*/*/edit');
    }

    public function saveAction()
    {
        $data = $this->getRequest()->getPost();

        if (!$data) {
             Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('helpmate')->__('Unable to find item to save')
            );
            $this->_redirect('*/*/');
        }

        try  {
            $model = Mage::getModel('helpmate/status');
            $model->setData($data);
            $model->save();

            //success
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('helpmate')->__('Item was successfully saved')
            );
            Mage::getSingleton('adminhtml/session')->setFormData(false);

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect(
                '*/*/edit', array('id' => $this->getRequest()->getParam('id'))
            );
            return;
        }
    }

    public function deleteAction()
    {
        $_id = (int) $this->getRequest()->getParam('id', 0);
        if (!TM_Helpmate_Model_Status::isSystemOption($_id)) {
            try {
                $model = Mage::getModel('helpmate/status');

                $model->setId($_id)->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $_id));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction()
    {
        $_ids = $this->getRequest()->getParam('helpmate');
        if(!is_array($_ids)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('adminhtml')->__('Please select item(s)')
            );
        } else {
            try {
                // sys statusses filtring
                $_ids = array_diff($_ids, array_keys(
                    TM_Helpmate_Model_Status::getSystemOptionArray()
                ));
                foreach ($_ids as $_id) {
                    $model = Mage::getModel('helpmate/status')->load($_id);
                    $model->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count($_ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'helpmate_status.csv';
        $content    = $this->getLayout()->createBlock('helpmate/adminhtml_status_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'helpmate_status.xml';
        $content    = $this->getLayout()->createBlock('helpmate/adminhtml_status_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse(
        $fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}