<?php

class TM_KnowledgeBase_Adminhtml_CategoryController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction() {
        $this->loadLayout();
        $this->_setActiveMenu('knowledgebase/knowledgebase')
            ->_addBreadcrumb(
                Mage::helper('knowledgebase')->__('Category Manager'),
                Mage::helper('knowledgebase')->__('Category')
            );
        
        return $this;
    }
 
    public function indexAction() 
    {
        $this->_initAction();
        $this->renderLayout();
    }

 //
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', 0 );
        $category = Mage::getModel('knowledgebase/category')->load($id);
        $categoryId = $category->getId();

        if (!$categoryId && $id !== 0) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('knowledgebase')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $category->setData($data);
        }

        Mage::register('knowledgebase_category_data', $category);

        $this->loadLayout();
        $this->_setActiveMenu('knowledgebase/category');


        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent(
            $this->getLayout()->createBlock(
                'knowledgebase/adminhtml_category_edit'
            )
        );
        $this->renderLayout();
    }

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
                Mage::helper('knowledgebase')->__('Unable to find item to save')
            );
            $this->_redirect('*/*/');
            return;
        }

        try  {
            $category = Mage::getModel('knowledgebase/category');

            if (empty($data['id'])) {
                unset($data['id']);
            }
            if (empty($data['identifier'])) {
                $data['identifier'] = preg_replace(
                    "/[^0-9a-zA-Z]/i", '-', $data['name']
                );
                $data['identifier'] = strtolower(preg_replace(
                    "/-+/", '-', $data['identifier']
                ));
            }
            if (empty($data['id'])) {
                unset($data['id']);
                $category->loadByIdentifier($data['identifier']);
                if ($category->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('knowledgebase')->__('url already exist')
                    );
                    $this->_redirect('*/*/');
                    return;
                }
            }

            $category->setData($data);
            if ('' === $category->getCreatedAt()) {
                $category->setCreatedAt(now());
            }

            $category->save();

            //success
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('helpmate')->__('Item was successfully saved')
            );
            Mage::getSingleton('adminhtml/session')->setFormData(false);


            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $category->getId()));
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
//

    public function deleteAction()
    {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('knowledgebase/category');

                $model->setId($this->getRequest()->getParam('id'))
                ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $ids = $this->getRequest()->getParam('knowledgebase');
        if(!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    Mage::getModel('knowledgebase/category')->load($id)
                        ->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                    'Total of %d record(s) were successfully deleted', count($ids)
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
        $fileName   = 'knowledgebase_categories.csv';
        $content    = $this->getLayout()->createBlock('knowledgebase/adminhtml_category_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'knowledgebase_categories.xml';
        $content    = $this->getLayout()->createBlock('knowledgebase/adminhtml_category_grid')
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
//
}