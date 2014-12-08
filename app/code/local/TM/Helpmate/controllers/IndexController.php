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

    protected function _isAkismetEnabled()
    {
        return Mage::getStoreConfig('helpmate/general/enableAkismet')
            && class_exists('TM_Akismet_Model_Service');
    }

    protected function _isCaptchaEnabled()
    {
        $formId = 'helpmate_ticket_form';
        $helperClass = Mage::getConfig()->getHelperClassName('captcha');
        if (@!class_exists($helperClass)) {
            return $this;
        }
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        return $captchaModel->isRequired();
    }

    public function indexAction()
    {
        $this->loadLayout();
        
        if ($this->_isAkismetEnabled() && $this->_isCaptchaEnabled()) {
            $isShowCapcha = (bool) Mage::getModel('core/session')->getShowCaptcha();
            Mage::getModel('core/session')->setShowCaptcha(null);
            if (!$isShowCapcha) {
                $layout = $this->getLayout();
                $layout->getBlock('captcha')->setFormId('__broken__' . uniqid());
                Mage::getModel('core/session')->setDisableCheckCaptchaOnTicketSave(true);
            }
        }
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
        $visitorId    = Mage::getSingleton('log/visitor')->getId();

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

        if ($this->_isAkismetEnabled() 
            && ($akismet = Mage::getModel('akismet/service'))
            && $akismet->isSpam($author, $email, $text)
            ) {

                Mage::getModel('core/session')->setShowCaptcha(true);
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
            ->setVisitorId(     $visitorId)
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

        $path = Mage::getBaseDir('var') . DS . 'helpmate' . DS;

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

    public function fileAction()
    {
        $name = $this->getRequest()->getParam('filename');
        $name = basename($name);

        $extension = pathinfo($name, PATHINFO_EXTENSION);
        $allowedExtensions = explode(
            ',',
            Mage::getStoreConfig('helpmate/general/attachedAllowedExtensions')
        );

        if (!in_array($extension, $allowedExtensions)) {
            Mage::getSingleton('core/session')->addError(
                Mage::helper('helpmate')->__(
                    'File not exists'
            ));

            $this->setFlag('', 'no-dispatch', true);
            $this->_redirect('noRoute');
        }

        $path = Mage::getBaseDir('var') . DS . 'helpmate' . DS . $name;
        if (!file_exists($path)) {
            $path = Mage::getBaseDir('media') . DS . 'helpmate' . DS . $name;
            if (!file_exists($path)) {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('helpmate')->__(
                        'File not exists'
                ));

                $this->setFlag('', 'no-dispatch', true);
                $this->_redirect('noRoute');
            }
        }

        switch ($extension) {
            case 'txt':
                $contentType = 'text/plain';
                break;
            case 'csv':
                $contentType = 'text/csv';
                break;
            case 'xml':
                $contentType = 'text/xml';
                break;
            case 'css':
                $contentType = 'text/css';
                break;
            case 'pdf':
                $contentType = 'application/pdf';
                break;
            case 'flv':
                $contentType = 'video/x-flv';
                break;
            case 'avi':
                $contentType = 'video/mpeg';
                break;
            case 'wmv':
                $contentType = 'video/x-ms-wmv';
                break;
            case 'jpg':
            case 'jpeg':
                $contentType = 'image/jpeg';
                break;
            case 'gif':
                $contentType = 'image/gif';
                break;
            case 'png':
                $contentType = 'image/png';
                break;
            default:
//                $contentType = 'application/octet-stream';
                $contentType = 'application/force-download';
                $this->getResponse()
                    ->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($path))
                ;
                break;
        }
//        Zend_Debug::dump($contentType);
//        die;


        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Content-type', $contentType)
            ->setHeader('Content-Length', filesize($path))
//            ->setHeader('Content-Disposition', 'attachment' . '; filename=' . basename($path))
            ->clearBody()
        ;
        $this->getResponse()->sendHeaders();
        readfile($path);
        die;
    }

//
//    public function apiindexAction()
//    {
//
//        $magentohost = Mage::getBaseUrl();// 'http://templates-master.com';
//        //Basic parameters that need to be provided for oAuth authentication
//        //on Magento
//        $params = array(
//            'siteUrl'         => "{$magentohost}oauth",
//            'requestTokenUrl' => "{$magentohost}oauth/initiate",
//            'accessTokenUrl'  => "{$magentohost}oauth/token",
//            'authorizeUrl'    => "{$magentohost}oauth/authorize",
////            'authorizeUrl'    => "{$magentohost}admin/oauth_authorize", //This URL is used only if we authenticate as Admin user type
//            'consumerKey'     => '094b371d035c101dbaeebbba96e72f46', //Consumer key registered in server administration
//            'consumerSecret'  => 'b8d13ab7f0f82bdc3dc10a3d508f43a0', //Consumer secret registered in server administration
//            'callbackUrl'     => "{$magentohost}helpdesk/index/apicallback", //Url of callback action below
//        );
//        $oAuthClient = Mage::getModel('tmcore/oauth_client');
//        $oAuthClient->reset();
//        $oAuthClient->init($params);
//        $oAuthClient->authenticate();
//        return;
//    }
//
//    public function apicallbackAction()
//    {
//        $magentohost = Mage::getBaseUrl();// 'http://templates-master.com';
//
//        $oAuthClient = Mage::getModel('tmcore/oauth_client');
//        $params = $oAuthClient->getConfigFromSession();
//        $oAuthClient->init($params);
//        $state = $oAuthClient->authenticate();
//        if ($state == TM_Core_Model_Oauth_Client::OAUTH_STATE_ACCESS_TOKEN) {
//            $accessToken = $oAuthClient->getAuthorizedToken();
//        }
//        $restClient = $accessToken->getHttpClient($params);
//        /* @var $restClient Zend_Oauth_Client */
////////////////////////////////////////////////////////////////////////////////
//         // Set REST resource URL
//        $_request = "{$magentohost}api/rest/helpdesk/tickets";
//        $restClient->setUri($_request)
////            ->setParameterGet('page', 3)
//        ;
//        // In Magento it is neccesary to set json or xml headers in order to work
//        $restClient->setHeaders('Accept', 'application/json');
//        // Get method
//        $restClient->setMethod(Zend_Http_Client::GET);
//        //Make REST request
//        $response = $restClient->request();
//        // Here we can see that response body contains json list of products
//        $body = $response->getBody();
//        Zend_Debug::dump($body);
//        $body = json_decode($body);
//        Zend_Debug::dump($body,$_request);
//
//        return;
//    }
}
