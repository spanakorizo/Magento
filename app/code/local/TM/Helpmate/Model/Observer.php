<?php
class TM_Helpmate_Model_Observer
{
    /**
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return  TM_Helpmate_Model_Observer
     */
    public function sheduledAddEmailedTicket($schedule)
    {
        $departments  = Mage::getModel('helpmate/department')->getCollection()
            ->addActiveFilter()
            ->addGatewayData();

        $statusOpen     = TM_Helpmate_Model_Status::STATUS_OPEN;
        $statusReplied  = TM_Helpmate_Model_Status::STATUS_REPLIED;
        $priority       = TM_Helpmate_Model_Priority::PRIORITY_MEDIUM;

        foreach ($departments as $department) {
            $gateway = $department->getGateway();
            if (!$gateway->getStatus()) {
                continue;
            }
            try {
                $storage  = $gateway->getStorage();
                if (!$storage->countMessages()) {
                    continue;
                }

            } catch (Zend_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError(
                    Mage::helper('helpmate')->__(
                        'Problem connecting to gateway ("%s") "%s" for department "%s".',
                        $gateway->getName(),
                        $e->getMessage(),
                        $department->getName()
                    )
                );
                continue;
            }

            foreach ($storage as $messageNumber => $message) {

                $subject = '';
                try {

                if ($storage instanceof Zend_Mail_Storage_Imap
                    && $message->hasFlag(Zend_Mail_Storage::FLAG_SEEN)) {

                    continue;
                }

                $modelDepartmentUser = Mage::getModel('helpmate/department_user');
                $ticket = Mage::getModel('helpmate/ticket');
                $theard = Mage::getModel('helpmate/theard');
///////////////////////////////////////////////////////////////////////////////////////////////
//                foreach ($message->getHeaders() as $name => $value) {
//                    if (is_string($value)) {
//                        Zend_Debug::dump("$name: $value");
//                        continue;
//                    }
//                    foreach ($value as $entry) {
//                        Zend_Debug::dump("$name: $entry");
//                    }
//                }
/////////////////////////////////////////////////////////////////////////////////////////////////
//                $storage->noop();
                $parser = new TM_Helpmate_Model_Mail_MessageParser($message);

                $messageId = $parser->getMessageId();
                ////////////////////////////////////////////////////////////////
                //next message because this message was added before
                if (null !== $messageId) {
                    $theardCollection = $theard->getCollection()
                        ->addMessageIdFilter($messageId);
                    if ($theardCollection->count()) {
                        continue;
                    }
                }
                ////////////////////////////////////////////////////////////////
                //detect ticketId from In-Reply-To
                $ticketId  = null;
                $inReplyTo = $parser->getInReplyTo();

                if (null !== $inReplyTo) {
                    $_theard = $theard->getCollection()
                        ->addMessageIdFilter($inReplyTo)
                        ->getFirstItem()
                    ;
                    if ($_theard) {
                        $ticketId = $_theard->getTicketId();
                    }
                }
                ////////////////////////////////////////////////////////////////
                $createdAt    = $parser->getCreatedAt();
                $departmentId = $department->getId();
                $from         = $parser->getFrom();

                ////////////////////////////////////////////////////////////////
                //detect ticketId from subject
                $subject = $parser->getSubject();
                if (null === $ticketId) {
                    $ticketId = $parser->getTicketIdFromSubject($subject);
                }
                ////////////////////////////////////////////////////////////////
                if (null !== $ticketId) {
                    $ticket->load($ticketId);
                }

                $isAdminEmail = (bool) $modelDepartmentUser->getCollection()
                    ->addDepartmentFilter($departmentId)
                    ->addEmailFilter($from)
                    ->count()
                    ;
                $status = $isAdminEmail ? $statusReplied : $statusOpen;

                if (null === $ticket->getId()) {
                    ///////////////
                    $customerId = $customer = null;

                    $resource = Mage::getSingleton('customer/customer')->getResource();
                    $connection = $resource->getReadConnection();
                    $select = $connection->select()
                        ->from($resource->getEntityTable(), array($resource->getEntityIdField()))
                        ->where('email=:customer_email')
                        ;

                    $customer = Mage::getSingleton('customer/customer')->load(
                        $connection->fetchOne(
                            $select, array('customer_email' => $from)
                        )
                    );

                    if ($customer instanceof Mage_Customer_Model_Customer
                        && $customer->getId()) {

                        $customerId = $customer->getId();
                    }
                    /////////
                    $user = $modelDepartmentUser
                        ->getCollection()
                        ->addDepartmentFilter($departmentId)
                        ->getFirstItem()
                    ;
                    $userId = null;
                    if ($user) {
                       $userId = $user->getUserId();
                    }

                    $storeId = $department->getStoreId();
                    if (0 == $storeId && $customer) {
                        $storeId = $customer->getStoreId();
                    }
                    //////////////
                    $ticket->setCustomerId( $customerId)
                        ->setEmail(         $from)
                        ->setNumber(        )
                        ->setStatus(        $status)
                        ->setTitle(         $subject)
                        ->setPriority(      $priority)
                        ->setCreatedAt(     $createdAt)
                        ->setModifiedAt(    $createdAt)
                        ->setDepartmentId(  $departmentId)
                        ->setUserId(        $userId)
                        ->setStoreId(       $storeId)
                        ->setNotes(         'From Department : "' . $department->getName()
                                            . '" Email Gateway : "' . $gateway->getName() . '"')
                        ->save()
                        ;
                    Mage::dispatchEvent('helpmate_notify_customer_ticket_create', array(
                        'ticket'  => $ticket
                    ));
                }

                $ticket->setStatus($status)
                    ->setModifiedAt($createdAt)
                    ->save();

                $theard->setMessageId( $messageId)
                    ->setTicketId(     $ticket->getId())
                    ->setCreatedAt(    $createdAt)
                    ->setText(         $parser->getContent())
                    ->setFile(         $parser->getFilenames())
//                    ->setUserId(       $userId)
                    ->setStatus(       $status)
                    ->setPriority(     $ticket->getPriority())
                    ->setDepartmentId( $ticket->getDepartmentId())
                    ;

                if ($isAdminEmail) {
                    $user = $modelDepartmentUser
                        ->getCollection()
                        ->addDepartmentFilter($departmentId)
                        ->addEmailFilter($from)
                        ->getFirstItem()
                    ;
                    $userId = null;
                    if ($user) {
                       $userId = $user->getUserId();
                    }

                    if (!empty($userId)) {
                        $theard->setUserId($userId);
                    }
                }
                $theard->save();

                $event = 'helpmate_notify_admin_ticket_change';
                if ($isAdminEmail) {
                    $event = 'helpmate_send_ticket_answer';
                }

                Mage::dispatchEvent($event, array(
                    'theard' => $theard
                ));
//                $currentMessageId = $storage->getNumberByUniqueId(
//                    $storage->getUniqueId($messageNumber)
//                );
//                $storage->removeMessage($currentMessageId);
//                $storage->removeMessage($messageNum);

            // end foreach storrage messages
                } catch (Zend_Exception $e) {
                    if (null == $subject) {
                        $subject = '';
                    }
                    echo $subject . '   ' . $e->getMessage();
                    Mage::getSingleton('adminhtml/session')->addSuccess(
                        $subject . '   ' . $e->getMessage()
                    );
                }
            }
            //REMOVE ALL MESSAGE :(
            for ($i = count($storage); $i; --$i) {
                $storage->removeMessage($i);
            }

        }
        return $this;
    }

    /**
     * send customer email notification about ticket creation
     *
     * @param Varien_Event_Observer $observer
     * @return TM_Helpmate_Model_Observer
     */
    public function notifyCustomerTicketCreate(Varien_Event_Observer $observer)
    {

        if (true != Mage::getStoreConfig('helpmate/email/enableCustomerNotification')) {
            return $this;
        }

        $ticket     = $observer->getEvent()->getTicket();
        $department = $ticket->getDepartment();
        $gateway    = $department->getGateway();

        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');

        $this->_prepareEmailTemplate($mailTemplate);

        $emailTemplateId = $department->getEmailTemplateNew();
        if (0 == $emailTemplateId) {
            $emailTemplateId = Mage::getStoreConfig('helpmate/email/ticket_notification');
        }

        $name = $ticket->getEmail();
        if (null !== $ticket->getCustomerId()) {
            $name = Mage::getModel('customer/customer')
                ->load($ticket->getCustomerId())
                ->getName();
        }

        $vars = new Varien_Object(array(
            'name'   => $name,
            'number' => $ticket->getNumber(),
            'ticket' => $ticket->getTitle()
        ));

        $email = $ticket->getEmail();

        $archiveEmail = Mage::getStoreConfig('helpmate/email/archive_email');
        if (Zend_Validate::is($archiveEmail, 'EmailAddress')) {
            $name = array($email => $name, $archiveEmail => $archiveEmail);
            $email = array($email, $archiveEmail);
        }

        $mailTemplate->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $ticket->getStoreId()
            ))
//            ->setReplyTo($gateway->getEmail())
            ->sendTransactional(
                    $emailTemplateId,
                    $department->getSender(),
                    $email,
                    $name,
                    array('vars' => $vars)
        );
//        $mailTemplate->getSentSuccess();

        if (!$mailTemplate->getSentSuccess()) {
            throw new Mage_Core_Exception('mail not send ' . __METHOD__);
        }

        return $this;
    }

    /**
     * send admin(s) email notification about ticket creation and change
     *
     * @param Varien_Event_Observer $observer
     * @return TM_Helpmate_Model_Observer
     */
    public function notifyAdminTicketChange(Varien_Event_Observer $observer)
    {
        if (true != Mage::getStoreConfig('helpmate/email/enableAdminNotification')) {
            return $this;
        }

        $theard     = $observer->getEvent()->getTheard();
        $ticket     = $theard->getTicket();
        $department = $ticket->getDepartment();
        $gateway    = $department->getGateway();

        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');

        $this->_prepareEmailTemplate($mailTemplate);

        $messageId = $ticket->getLastMessageId();
        if (false === empty($messageId)) {
            $mailTemplate->getMail()->addHeader('In-Reply-To', '<' . $messageId . '>');
        }
        $newMessageId = $mailTemplate->getMail()->createMessageId();
        $mailTemplate->getMail()->setMessageId($newMessageId);

        $emailTemplateId = $department->getEmailTemplateAdmin();
        if (0 == $emailTemplateId) {
            $emailTemplateId = Mage::getStoreConfig('helpmate/email/theard_notification');
        }

        $departmentUser = Mage::getModel('helpmate/department_user');
        $departmentId = $department->getId();
        $users = $departmentUser->getCollection()
            ->addDepartmentFilter($departmentId)
            ->addEmailData()
            ;
        $name = $ticket->getEmail();
        if (null !== $ticket->getCustomerId()) {
            $name = Mage::getModel('customer/customer')
                ->load($ticket->getCustomerId())
                ->getName();
        }
        list($url) = explode('/key/', Mage::helper('adminhtml')->getUrl(
            'adminhtml/helpmate_ticket/edit/', array('id' => $ticket->getId())
        ));
        $_theard = nl2br($theard->getText());
        $_helper = Mage::helper('purify');
        if ($_helper) {
            $_helper->purify($_theard);
        }
        $vars = new Varien_Object(array(
            'name'   => $name,
            'number' => $ticket->getNumber(),
            'url'    => $url,
            'ticket' => $ticket->getTitle(),
            'theard' => $_theard
        ));
//        $emails = //Mage::getModel('admin/user')
        foreach ($users as $user) {

            $email = $user->getEmail();

            $archiveEmail = Mage::getStoreConfig('helpmate/email/archive_email');
            if (Zend_Validate::is($archiveEmail, 'EmailAddress')) {
                $name = array($email => $name, $archiveEmail => $archiveEmail);
                $email = array($email, $archiveEmail);
            }

            $mailTemplate->setDesignConfig(array(
                    'area' => 'frontend',
                    'store' => $ticket->getStoreId()
                ))
                ->setReplyTo($gateway->getEmail())
                ->sendTransactional(
                    $emailTemplateId,
                    $department->getSender(),
                    $email,
                    $name,
                    array('vars' => $vars)
                );
    //        $mailTemplate->getSentSuccess();

            if (!$mailTemplate->getSentSuccess()) {
                throw new Mage_Core_Exception('mail not send ' . __METHOD__);
            }
        }

        $theard->setMessageId($newMessageId)
            ->save();

        return $this;
    }

    /**
     * send answer for customer
     *
     * @param Varien_Event_Observer $observer
     * @return TM_Helpmate_Model_Observer
     */
    public function sendTicketAnswer(Varien_Event_Observer $observer)
    {
        $theard     = $observer->getEvent()->getTheard();
        $ticket     = $theard->getTicket();
        $department = $ticket->getDepartment();
        $gateway    = $department->getGateway();

        $mailTemplate = Mage::getModel('core/email_template');
        /* @var $mailTemplate Mage_Core_Model_Email_Template */

        $this->_prepareEmailTemplate($mailTemplate);

        $messageId = $ticket->getLastMessageId();
        if (false === empty($messageId)) {
            $mailTemplate->getMail()->addHeader('In-Reply-To', '<' . $messageId . '>');
        }
        //
        $newMessageId = $mailTemplate->getMail()->createMessageId();
        $mailTemplate->getMail()->setMessageId($newMessageId);

        $emailTemplateId = $department->getEmailTemplateAnswer();
        if (0 == $emailTemplateId) {
            $emailTemplateId = Mage::getStoreConfig('helpmate/email/ticket_answer');
        }
        $name = $ticket->getEmail();
        if (null !== $ticket->getCustomerId()) {
            $name = Mage::getModel('customer/customer')
                ->load($ticket->getCustomerId())
                ->getName();
        }
        $vars = new Varien_Object(array(
            'name'   => $name,
            'number' => $ticket->getNumber(),
            'ticket' => $ticket->getTitle(),
        ));
        $file = $theard->getFile();
        if (!empty($file)) {
            $path = Mage::getUrl('helpmate/index/file') . 'filename/';
            $files = array_filter(explode(';', $file));
            foreach ($files as &$file) {
                $file = '<p><a href="' . $path . $file . '" style="color:#1E7EC8;">' .
                    $file .
                '</a></p>';
            }
            $file = implode('', $files);
        }
        $vars['file'] = $file;
        $vars['theard'] = $theard->getPrecessedText(array('vars' => $vars));
        $vars['theard_text'] = strip_tags($vars['theard']);

        $email = $ticket->getEmail();
        
        $archiveEmail = Mage::getStoreConfig('helpmate/email/archive_email');
        if (Zend_Validate::is($archiveEmail, 'EmailAddress')) {
            $name = array($email => $name, $archiveEmail => $archiveEmail);
            $email = array($email, $archiveEmail);
        }

        $mailTemplate->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $ticket->getStoreId()
            ))
            ->setReplyTo($gateway->getEmail())
            ->sendTransactional(
                $emailTemplateId,
                $department->getSender(),
                $email,
                $name,
                array('vars' => $vars)
        );

        if (!$mailTemplate->getSentSuccess()) {
            throw new Mage_Core_Exception('mail not send '  . __METHOD__);
        }
        //
        $theard->setMessageId($newMessageId)
            ->save();
        //
        return $this;
    }

    /**
     *
     * @param Mage_Cron_Model_Schedule $schedule
     * @return TM_Helpmate_Model_Observer
     */
    public function autoCloseTicketAfterXDay($schedule)
    {
        $xDays = Mage::getStoreConfig('helpmate/general/autoCloseTicketAfterXDay');
        $time = Zend_Date::now()->subDay($xDays);

        $collection = Mage::getModel('helpmate/ticket')->getCollection()
            ->addStatusFilter(TM_Helpmate_Model_Status::STATUS_REPLIED)
            ->addLessModifiedAtFilter($time)
            ;

        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');
        
        $this->_prepareEmailTemplate($mailTemplate);

        $emailTemplateId = Mage::getStoreConfig('helpmate/email/ticket_autoclose');

        foreach ($collection as $ticket) {
            $notes = $ticket->getNotes();
            $ticket->setStatus(TM_Helpmate_Model_Status::STATUS_CLOSE)
                ->setNotes($notes . " Auto close ticket after {$xDays} days")
                ->save()
                    ;

            // send mail
            $department = $ticket->getDepartment();
            $gateway    = $department->getGateway();

            $name = $ticket->getEmail();
            if (null !== $ticket->getCustomerId()) {
                $name = Mage::getModel('customer/customer')
                    ->load($ticket->getCustomerId())
                    ->getName();
            }
            $vars = new Varien_Object(array(
                'name'   => $name,
                'number' => $ticket->getNumber(),
                'ticket' => $ticket->getTitle(),
                'x_day'  => $xDays
            ));

            $email = $ticket->getEmail();

            $archiveEmail = Mage::getStoreConfig('helpmate/email/archive_email');
            if (Zend_Validate::is($archiveEmail, 'EmailAddress')) {
                $name = array($email => $name, $archiveEmail => $archiveEmail);
                $email = array($email, $archiveEmail);
            }

            $mailTemplate->setDesignConfig(array(
                    'area' => 'frontend',
                    'store' => $ticket->getStoreId()
                ))
//                ->setReplyTo($gateway->getEmail())
                ->sendTransactional(
                    $emailTemplateId,
                    $department->getSender(),
                    $email,
                    $name,
                    array('vars' => $vars)
                );
            if (!$mailTemplate->getSentSuccess()) {

                //throw new Mage_Core_Exception('mail not send '  . __METHOD__);
            }
        }

        return $this;
    }

    /**
     * notify assigned admin
     *
     * @param Varien_Event_Observer $observer
     * @return TM_Helpmate_Model_Observer
     */
    public function ticketUserChange(Varien_Event_Observer $observer)
    {
        $ticketId = $observer->getEvent()->getTicketId();
        $ticket   = Mage::getModel('helpmate/ticket')->load($ticketId);

        /* @var $mailTemplate Mage_Core_Model_Email_Template */
        $mailTemplate = Mage::getModel('core/email_template');
        
        $this->_prepareEmailTemplate($mailTemplate);

        $emailTemplateId = Mage::getStoreConfig('helpmate/email/ticket_assigned');

        // send mail
        $department = $ticket->getDepartment();
        list($url) = explode('/key/', Mage::helper('adminhtml')->getUrl(
            'adminhtml/helpmate_ticket/edit/', array('id' => $ticket->getId())
        ));
        $vars = new Varien_Object(array(
            'number' => $ticket->getNumber(),
            'url'    => $url,
            'ticket' => $ticket->getTitle()
        ));

        $user = $ticket->getAssignedUser();

        $email = $user->getEmail();

        $archiveEmail = Mage::getStoreConfig('helpmate/email/archive_email');
        if (Zend_Validate::is($archiveEmail, 'EmailAddress')) {
            $name = array($email => $name, $archiveEmail => $archiveEmail);
            $email = array($email, $archiveEmail);
        }

        $mailTemplate->setDesignConfig(array(
                'area' => 'frontend',
                'store' => $ticket->getStoreId()
            ))
    //                ->setReplyTo($gateway->getEmail())
            ->sendTransactional(
                $emailTemplateId,
                $department->getSender(),
                $email,
                $user->getName(),
                array('vars' => $vars)
            );
    //        $mailTemplate->getSentSuccess();

        if (!$mailTemplate->getSentSuccess()) {
            throw new Mage_Core_Exception('mail not send '  . __METHOD__);
        }

        return $this;
    }


    /**
     *
     * @param Varien_Event_Observer $observer
     * @return TM_Helpmate_Model_Observer
     */
    public function addCreateTicketButton(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
//        Mage::dispatchEvent(
//            'adminhtml_widget_container_' . strtolower(get_class($block)) . '_html_before',
//            array('block' => $block)
//        );

        if ($block instanceof Mage_Adminhtml_Block_Customer_Edit) {
            $url = Mage::getModel('adminhtml/url')->getUrl(
                'adminhtml/helpmate_ticket/new',
                array('customer_id' => $block->getCustomerId())
            );
            $block->addButton('ticket', array(
                'label' => Mage::helper('helpmate')->__('Create Ticket'),
                'onclick' => 'setLocation(\'' . $url . '\')',
                'class' => 'add',
            ), 0, 11);
        }

        if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {
            $params = array('order_id' => $block->getOrderId());
            $customerId = $block->getOrder()->getCustomerId();
            if (!empty($customerId)) {
                $params['customer_id'] = $customerId;
            }
            $url = Mage::getModel('adminhtml/url')->getUrl(
                'adminhtml/helpmate_ticket/new',
                $params
            );
            $block->addButton('ticket', array(
                'label' => Mage::helper('helpmate')->__('Create Ticket'),
                'onclick' => 'setLocation(\'' . $url . '\')',
                'class' => 'add',
            ), 0, 11);
        }
    }

    /**
     * Check Captcha On Ticket Save
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function checkCaptchaOnTicketSave($observer)
    {
        $notCheck = (bool) Mage::getModel('core/session')->getDisableCheckCaptchaOnTicketSave();
        Mage::getModel('core/session')->setDisableCheckCaptchaOnTicketSave(null);
        if ($notCheck) {
            return $this;
        }

        $formId = 'helpmate_ticket_form';
        $helperClass = Mage::getConfig()->getHelperClassName('captcha');
        if (@!class_exists($helperClass)) {
            return $this;
        }
        $captchaModel = Mage::helper('captcha')->getCaptcha($formId);
        if ($captchaModel->isRequired()) {
            $controller = $observer->getControllerAction();
            $captchaParams = $controller->getRequest()->getPost(
                Mage_Captcha_Helper_Data::INPUT_NAME_FIELD_VALUE
            );
            if (!$captchaModel->isCorrect($captchaParams[$formId])) {
                Mage::getSingleton('core/session')->addError(
                    Mage::helper('captcha')->__('Incorrect CAPTCHA.')
                );
                $controller->setFlag(
                    '', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true
                );
                $controller->getResponse()->setRedirect(
                    Mage::getUrl('*/*/index')
                );
            }
        }
        return $this;
    }


    /**
     *
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function saveParamsFromRequestToSession($observer)
    {
        $controller = $observer->getControllerAction();
        $key = $controller->getFullActionName();
        $params = $controller->getRequest()->getParams();
        Mage::getSingleton('core/session')->setData($key, $params);
        return $this;
    }

    protected function _prepareEmailTemplate(&$mailTemplate)
    {
        $transportId = Mage::getStoreConfig('helpmate/email/transport');
        if (!empty($transportId)) {
            $_transport = Mage::getModel('tm_email/gateway_transport')
                ->load($transportId)
            ;
            if ($_transport) {
                $transport = $_transport->getTransport();
                if ($transport instanceof Zend_Mail_Transport_Abstract) {
                    $mailTemplate->setTransport($transport);
                }
            }
        }

        $queueId = Mage::getStoreConfig('helpmate/email/queue');
        if (!empty($queueId)) {
            $_queue = Mage::getModel('tm_email/queue_queue')
                ->load($queueId)
            ;

            if ($_queue) {
                $mailTemplate->setQueueName($_queue->getQueueName());
            }
        }

        return $mailTemplate;
    }

    /**
     * FIX redirect url
     *
     * for more see
     * Mage_Oauth_AuthorizeController.php line 86
     * Mage_Customer_AccountController::_loginPostRedirect
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_Captcha_Model_Observer
     */
    public function fixOauthAuthorizeRedirectUrl($observer)
    {
        $session = Mage::getSingleton('customer/session');
        $redirectUrl = $session->getBeforeAuthUrl();
        $session->setAfterAuthUrl($redirectUrl);
        return $this;
    }
}
