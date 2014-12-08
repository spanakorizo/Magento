<?php

class TM_Helpmate_Adminhtml_Helpmate_DepartmentController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction() {
        $this->loadLayout();
        $this->_setActiveMenu('helpmate/helpmate')
            ->_addBreadcrumb(
                Mage::helper('helpmate')->__('Department Manager'),
                Mage::helper('helpmate')->__('Department')
            );

        return $this;
    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function reportAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

//
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $department = Mage::getModel('helpmate/department')->load($id);
        $departmentId = $department->getId();

        if (!$departmentId && $id !== 0) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('helpmate')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $department->setData($data);
        }
        $users = array();
        $rowset = Mage::getModel('helpmate/department_user')
            ->getCollection()
            ->addDepartmentFilter($departmentId);
        foreach ($rowset as $row) {
            $users[] = $row->getUserId();
        }

        $department->setUsers($users);

        Mage::register('helpmate_department_data', $department);

        $this->loadLayout();
        $this->_setActiveMenu('helpmate/department');
        $this->renderLayout();
    }
//
    public function newAction()
    {
        $this->_redirect('*/*/edit');
    }
//
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
            $department = Mage::getModel('helpmate/department');

            if (empty($data['id'])) {
                unset($data['id']);
            }
            if (empty($data['created_at'])) {
                $data['created_at'] = now();
            }
            $department->setData($data);

            $department->save();

            $departmentId = $department->getId();
            $collection = Mage::getModel('helpmate/department_user')
                ->getCollection()
                ->addDepartmentFilter($departmentId);
            foreach ($collection as $row) {
                $row->delete();
            }
            if (!empty($data['users'])) {

                foreach ($data['users'] as $userId) {
                    $departmentUser = Mage::getModel('helpmate/department_user');
                    $departmentUser
                        ->setDepartmentId($departmentId)
                        ->setUserId($userId)
                        ->save();
                }
            }

            //success
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('helpmate')->__('Item was successfully saved')
            );
            Mage::getSingleton('adminhtml/session')->setFormData(false);


            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $department->getId()));
                return;
            }
            $this->_redirect('*/*/');
            return;
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            Mage::getSingleton('adminhtml/session')->setFormData($data);
            $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            return;
        }
    }

    public function deleteAction()
    {
        $departmentId = $this->getRequest()->getParam('id');
        if( 0 < $departmentId) {
            try {
                $department = Mage::getModel('helpmate/department');

                $department->setId($departmentId)->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $departmentId));
            }
        }
        $this->_redirect('*/*/');
    }
//
    public function massDeleteAction()
    {
        $_ids = $this->getRequest()->getParam('helpmate');
        if(!is_array($_ids)) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($_ids as $_id) {
                    $department = Mage::getModel('helpmate/department')->load($_id);
                    $department->delete();
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
//
    public function exportCsvAction()
    {
        $fileName   = 'helpmate_department.csv';
        $content    = $this->getLayout()->createBlock('helpmate/adminhtml_department_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'helpmate_department.xml';
        $content    = $this->getLayout()->createBlock('helpmate/adminhtml_department_grid')
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
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}