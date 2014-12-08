<?php

class TM_KnowledgeBase_Adminhtml_Knowledgebase_FaqController extends Mage_Adminhtml_Controller_Action
{

    protected function _initAction() {
        $this->loadLayout();
        $this->_setActiveMenu('knowledgebase/knowledgebase')
            ->_addBreadcrumb(
                Mage::helper('knowledgebase')->__('Articles Manager'),
                Mage::helper('knowledgebase')->__('Articles')
            );

        return $this;
    }

//    public function uninstallAttributeAction()
//    {
//        $uninstall = new Mage_Eav_Model_Entity_Setup('core_setup');
//        $attr = $uninstall->getAttribute('catalog_product', 'knowledgebase_faq');
//        Zend_Debug::dump($attr);
//        $uninstall->removeAttribute('catalog_product', 'knowledgebase_faq');
//        die;
//    }

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', 0 );
        $faq = Mage::getModel('knowledgebase/faq')->load($id);

        if (!$faq->getId() && $id !== 0) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('knowledgebase')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $faq->setData($data);
        }

        $categories = array();
        $rowset = Mage::getModel('knowledgebase/faq_category')
            ->getCollection()
            ->addFaqFilter($faq->getId());
        foreach ($rowset as $row) {
            $categories[] = $row->getCategoryId();
        }
        $faq->setCategories($categories);

        $faq->getStores();

        Mage::register('knowledgebase_faq_data', $faq);

        $this->loadLayout();
        $this->_setActiveMenu('knowledgebase/faq');
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
                Mage::helper('knowledgebase')->__('Unable to find item to save')
            );
            $this->_redirect('*/*/');
            return;
        }

        try  {
            $faq = Mage::getModel('knowledgebase/faq');

            if (empty($data['identifier'])) {
                $data['identifier'] = preg_replace(
                    "/[^0-9a-zA-Z]/i", '-', $data['title']
                );
                $data['identifier'] = strtolower(preg_replace(
                    "/-+/", '-', $data['identifier']
                ));
            }
            if (empty($data['id'])) {
                unset($data['id']);
                $faq->loadByIdentifier($data['identifier']);
                if ($faq->getId()) {
                    Mage::getSingleton('adminhtml/session')->addError(
                        Mage::helper('knowledgebase')->__('url already exist')
                    );
                    $this->_redirect('*/*/');
                    return;
                }
            }
            $faq->setData($data);
            //prepare time
            if ('' == $faq->getCreatedAt()) {
                $faq->setCreatedAt(now());
            }
            $faq->setModifiedAt(now());
            $faq->save();

            $modelFaqCategory = Mage::getModel('knowledgebase/faq_category');
            $faqId = $faq->getId();
            foreach ($modelFaqCategory->getCollection()->addFaqFilter($faqId) as $row) {
                $row->delete();
            }
            $categories = empty($data['categories']) ? array() : $data['categories'];
            if (!empty($categories)) {

                foreach ($categories as $categoryId) {
                    Mage::getModel('knowledgebase/faq_category')
                        ->setFaqId($faqId)
                        ->setCategoryId($categoryId)
                        ->save();
                }
            }

            $modelFaqStore = Mage::getModel('knowledgebase/faq_store');
            foreach ($modelFaqStore->getCollection()->addFaqFilter($faqId) as $row) {
                $row->delete();
            }
            $stores = empty($data['stores']) ? array(0) : $data['stores'];

            foreach ($stores as $storeId) {
                Mage::getModel('knowledgebase/faq_store')
                    ->setFaqId($faqId)
                    ->setStoreId($storeId)
                    ->save();
            }

            //success
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('helpmate')->__('Item was successfully saved')
            );
            Mage::getSingleton('adminhtml/session')->setFormData(false);


            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $faq->getId()));
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
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('knowledgebase/faq');

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

    public function massDeleteAction()
    {
        $ids = $this->getRequest()->getParam('knowledgebase');
        if(!is_array($ids)) {
            Mage::getSingleton('adminhtml/session')
                ->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($ids as $id) {
                    Mage::getModel('knowledgebase/faq')->load($id)
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
        $fileName   = 'knowledgebase_faq.csv';
        $content    = $this->getLayout()->createBlock('knowledgebase/adminhtml_faq_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'knowledgebase_faq.xml';
        $content    = $this->getLayout()->createBlock('knowledgebase/adminhtml_faq_grid')
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

    /**
     * Initialize product from request parameters
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _initProduct()
    {
        $productId  = (int) $this->getRequest()->getParam('id');
        $product    = Mage::getModel('catalog/product')
            ->setStoreId($this->getRequest()->getParam('store', 0));

        if (!$productId) {
            if ($setId = (int) $this->getRequest()->getParam('set')) {
                $product->setAttributeSetId($setId);
            }

            if ($typeId = $this->getRequest()->getParam('type')) {
                $product->setTypeId($typeId);
            }
        }

        $product->setData('_edit_mode', true);
        if ($productId) {
            $product->load($productId);
        }

        $attributes = $this->getRequest()->getParam('attributes');
        if ($attributes && $product->isConfigurable() &&
            (!$productId || !$product->getTypeInstance()->getUsedProductAttributeIds())) {
            $product->getTypeInstance()->setUsedProductAttributeIds(
                explode(",", base64_decode(urldecode($attributes)))
            );
        }

        Mage::register('product', $product);
        Mage::register('current_product', $product);
        return $product;
    }

    public function productAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->renderLayout();
    }
}