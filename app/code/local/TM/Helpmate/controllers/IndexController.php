<?php
class TM_Helpmate_IndexController extends Mage_Core_Controller_Front_Action
{

    public function preDispatch()
    {
        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        if (!Mage::getStoreConfig('helpmate/general/enabled')) {
            $this->_forward('defaultNoRoute', 'index', 'cms');
//            $this->setFlag('', 'no-dispatch', true);
//            if(!Mage::getSingleton('customer/session')->getBeforeUrl()) {
//                Mage::getSingleton('customer/session')->setBeforeUrl($this->_getRefererUrl());
//            }
//            $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
//            $this->getResponse()->setHeader('Status','404 File not found');

//            $this->_forward('defaultNoRoute');
        }
    }

    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function customerAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('customer/account/login');
        }
        $this->loadLayout();
        $this->renderLayout();
    }

    public function contactsAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $layout = $this->getLayout();

        $session = Mage::getSingleton('customer/session');
        if (!$session->isLoggedIn()) {

            $content = $layout->getBlock('content');

            $ticketNumber = $this->getRequest()->getParam('ticket', 0);
            $ticket = Mage::getModel('helpmate/ticket')->loadByNumber($ticketNumber);

            if (null !== $ticket->getCustomerId()) {

                if (!$session->authenticate($this)) {
                    $this->setFlag('', 'no-dispatch', true);
                }
            } else {

                $email = $this->getRequest()->getParam('email');
                if ($email !== $ticket->email) {
                    $content->append($layout->createBlock(
                        'core/template',
                        'confirm_guest_access',
                        array('template' => 'tm/helpmate/confirm_guest_access.phtml')
                    ));
                    $this->renderLayout();
                    return;
                }
            }
        } else  {
            $content = $layout->getBlock('my.account.wrapper');
        }

        $content->append($layout->createBlock(
            'TM_Helpmate_Block_Ticket_View',
            'helpmate_ticket_view',
            array('template' => 'tm/helpmate/ticket/view.phtml')
        ));

        $blockTicketTheardNew = $layout->createBlock(
            'TM_Helpmate_Block_Ticket_Theard_New',
            'helpmate_ticket_theard_new',
            array('template' => 'tm/helpmate/ticket/theard/new.phtml')
        );

        $blockTicketTheardNewAdditional = $layout->createBlock(
            'core/text_list', 'form.additional.info'
        );

        $blockAttached = $layout->createBlock(
            'core/template',
            'helpmate_ticket_theard_new_additional_attached',
            array('template' => 'tm/helpmate/ticket/new/attached.phtml')
        );
        $blockTicketTheardNewAdditional->append($blockAttached);

        $blockTicketTheardNew->append($blockTicketTheardNewAdditional, 'form.additional.info');

        $content->append($blockTicketTheardNew);

        $this->renderLayout();
    }

    public function saveAction()
    {
//        if(!Mage::getSingleton('customer/session')->authenticate($this)) {
//            Mage::getSingleton('core/session')->addError(
//                 Mage::helper('helpmate')->__('Sorry, only logined customer can use helpmate.')
//            );
//            $this->_redirect('*/*/index');
////            $this->_redirectReferer();
//            return;
//        }
        $status       = TM_Helpmate_Model_Status::STATUS_OPEN;
        $priority     = $this->getRequest()->getParam('priority');
        $departmentId = $this->getRequest()->getParam('department_id');
        $storeId      = Mage::app()->getStore()->getId();
        $title        = $this->getRequest()->getParam('title');
        $customerId   = Mage::getSingleton('customer/session')->getCustomerId();
        $email        = Mage::getSingleton('customer/session')->getCustomer()->getEmail();
        $text         = $this->getRequest()->getParam('text');
//        $file         = $this->getRequest()->getParam('file');
        $orderId      = $this->getRequest()->getParam('order_id');
        $field0       = $this->getRequest()->getParam('field0');
        $field1       = $this->getRequest()->getParam('field1');
        $field2       = $this->getRequest()->getParam('field2');

        if (empty ($email)) {
            $email = $this->getRequest()->getParam('email');
        }

        if(empty($email)) {
            Mage::getSingleton('core/session')->addError(
                 Mage::helper('helpmate')->__('Sorry, email required.')
            );
            $this->_redirect('*/*/index');
            return;
        }
        $author = $email;
        if (null === $customerId) {
            $customer = Mage::getModel('customer/customer')
                ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                ->loadByEmail($email);
            if ($customer instanceof Mage_Customer_Model_Customer) {
                $customerId = $customer->getId();
                $author = $customer->getName();
            }
        }
        $number = Mage::getModel('helpmate/ticket')->generateNumberByEmail(
            $email
        );
        if (Mage::getStoreConfig('helpmate/general/enableAkismet')
            && class_exists('TM_Akismet_Model_Service')
            && $akismet = Mage::getModel('akismet/service')
            && $akismet->isSpam($author, $email, $text)) {

                $this->_redirectReferer();
                return;
        }
        //
        $adminUser = Mage::getModel('helpmate/department_user')
            ->getCollection()
            ->addDepartmentFilter($departmentId)
            ->getFirstItem()
        ;
        $userId = null;
        if ($adminUser) {
            $userId = $adminUser->getUserId();
        }

        $files = $this->_saveFile();
        if (null === $files) {
            $this->_redirectReferer();
            return;
        }

        $ticket = Mage::getModel('helpmate/ticket');
        $ticket->setCustomerId( $customerId)
            ->setEmail(         $email)
            ->setNumber(        $number)
            ->setStatus(        $status)
            ->setTitle(         $title)
            ->setPriority(      $priority)
            ->setCreatedAt(     now())
            ->setModifiedAt(    now())
            ->setDepartmentId(  $departmentId)
            ->setUserId(        $userId)
            ->setStoreId(       $storeId)
            ->setOrderId(       $orderId)
            ->setField0(        $field0)
            ->setField1(        $field1)
            ->setField2(        $field2)
            ->save()
            ;

        $theard = Mage::getModel('helpmate/theard');
        $theard->setTicketId(  $ticket->getId())
            ->setCreatedAt(    now())
            ->setText(         $text)
            ->setFile(         $files)
            ->setUserId(       null) // user admin id
            ->setStatus(       $status)
            ->setPriority(     $priority)
            ->setDepartmentId( $departmentId)
            ->save()
            ;

        try {
            Mage::dispatchEvent('helpmate_notify_customer_ticket_create', array(
                'ticket'  => $ticket
            ));

            Mage::dispatchEvent('helpmate_notify_admin_ticket_change', array(
                'theard'  => $theard
            ));
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError(
                $e->getMessage()
            );
        }

        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('helpmate')->__(
                'Thank you for contacting us. Your ticket has been created  and assigned to the appropriate department. Our support staff will be in touch with you as soon as possible.')
        );

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->_redirect('*/*/index');
        }
        $refererUrl = Mage::getSingleton('core/session')->getPreviousRefererUrl();
        if (empty($refererUrl)) {
            return $this->_redirectReferer();
        }
        Mage::getSingleton('core/session')->setPreviousRefererUrl(null);
        $this->getResponse()->setRedirect($refererUrl);

    }

    public function saveTheardAction()
    {
//        if(!Mage::getSingleton('customer/session')->authenticate($this)) {
//            Mage::getSingleton('core/session')->addError(
//                 Mage::helper('helpmate')->__(
//                     'Sorry, only logined customer can add self answer.'
//            ));
//            $this->_redirectReferer();
//            return;
//        }
        try {
            $ticketNumber = $this->getRequest()->getParam('ticket_number');
            $text         = $this->getRequest()->getParam('text');

            $files = $this->_saveFile();
            if (null === $files) {
                $this->_redirectReferer();
                return;
            }

            $statusOpen = TM_Helpmate_Model_Status::STATUS_OPEN;
            $ticket = Mage::getModel('helpmate/ticket')->loadByNumber($ticketNumber);
            $ticket->setStatus($statusOpen)
                ->save();

            $theard = Mage::getModel('helpmate/theard');

            $theard->setTicketId(  $ticket->getId())
                ->setCreatedAt(    now())
                ->setText(         $text)
                ->setFile(         $files)
                ->setUserId(       null) // user admin id
                ->setStatus(       $ticket->getStatus())
                ->setPriority(     $ticket->getPriority())
                ->setDepartmentId( $ticket->getDepartmentId())
                ->save()
                ;

            Mage::dispatchEvent('helpmate_notify_admin_ticket_change', array(
                'theard'  => $theard
            ));

        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('core/session')->addError(
                $e->getMessage()
            );
        }
        Mage::getSingleton('core/session')->addSuccess(
            Mage::helper('helpmate')->__(
                'Your message has been added'
        ));

        $this->_redirectReferer();
    }

    protected function _saveFile()
    {
        if (true != Mage::getStoreConfig('helpmate/general/enabledAttached')) {
            return false;
        }

        $path = Mage::getBaseDir('media') . DS . 'helpmate' . DS;

        if (empty($_FILES)) {
            return false;
        }

        try {
            $fileNames = '';
            //secure bug/feature here
            for ($i = 0; $i < 5; $i++) {
                if (empty($_FILES['file' . $i]['name'])) {
                    continue;
                }

                $uploader = new Varien_File_Uploader('file' . $i);
                $uploader->setAllowedExtensions(explode(
                    ',',
                    Mage::getStoreConfig('helpmate/general/attachedAllowedExtensions')
                ));

                $uploader->setAllowRenameFiles(true);
                $uploader->save($path);
                $fileName = $uploader->getUploadedFileName();
                if (empty($fileNames)) {
                    $fileNames .= $fileName;
                } else {
                    $fileNames .= ';' . $fileName;
                }

                unset($_FILES['file' . $i]);
            }

        } catch (Exception $e) {

            Mage::getSingleton('core/session')->addError(
                $e->getMessage()
            );
            return null;
        }
        $fileNames = str_replace(DS, '/', $fileNames);
        return $fileNames;
    }
}
