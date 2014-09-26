<?php

class TM_Helpmate_Adminhtml_TheardController extends Mage_Adminhtml_Controller_Action
{
    protected function _initAction() {
        $this->loadLayout();
        $this->_setActiveMenu('helpmate/helpmate')
            ->_addBreadcrumb(
                Mage::helper('helpmate')->__('Ticket Manager'),
                Mage::helper('helpmate')->__('Ticket')
            );
        
        return $this;
    }
 
    //
    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', 0);

        $theard = Mage::getModel('helpmate/theard')->load($id);

        if (null === $theard->getId()) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('helpmate')->__('Item does not exist')
            );
            $this->_redirect('*/*/');
        }

        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
            $theard->setData($data);
        }
        
        Mage::register('helpmate_theard_data', $theard);

        $this->loadLayout();
        $this->_setActiveMenu('helpmate/ticket');

        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent($this->getLayout()->createBlock(
            'helpmate/adminhtml_theard_edit'
        ))
        ;
       
        $this->renderLayout();
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
            $theard = Mage::getModel('helpmate/theard')
                ->load($data['id'])
                ->setData($data)
                ->save();

            //success
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('helpmate')->__('Item was successfully saved')
            );
            Mage::getSingleton('adminhtml/session')->setFormData(false);

            $this->_redirect('*/*/edit', array('id' => $theard->getId()));
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
        $id = $this->getRequest()->getParam('id', 0);

//        $theard = Mage::getModel('helpmate/theard')->load($id);
//        $ticketId = $theard->getTicketId();
//        Zend_Debug::dump($id);
//        Zend_Debug::dump($this->getUrl('*/adminhtml_ticket/edit', array('id' => $ticketId)));
//        Zend_Debug::dump($this->getUrl('*/*/edit', array('id' => $id)));
//        die;
        if ($id > 0) {
            try {

                $theard = Mage::getModel('helpmate/theard')
                    ->load($id)
                    ;
                $ticketId = $theard->getTicketId();
                $theard->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/adminhtml_ticket/edit', array('id' => $ticketId));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/adminhtml_ticket/edit', array('id' => $ticketId));
    }
}