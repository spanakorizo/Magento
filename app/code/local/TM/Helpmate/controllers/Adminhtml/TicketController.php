<?php

class TM_Helpmate_Adminhtml_TicketController extends Mage_Adminhtml_Controller_Action
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

    public function indexAction()
    {
        $this->_initAction();
        $this->renderLayout();
    }

    public function storageAction()
    {
        $timestamp = Zend_Date::now()->subMinute(2);

        Mage::getModel('helpmate/observer')->sheduledAddEmailedTicket(
//      Mage::getModel('helpmate/observer')->autoCloseTicketAfterXDay(
            new Varien_Object()
        );

        $count = Mage::getModel('helpmate/ticket')->getCollection()
            ->addGreateModifiedAtFilter($timestamp)
            ->count();

        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('helpmate')->__(
                'Email storage successfully refreshed. %s ticket was updated.', $count
            )
        );
        $this->_redirect('*/*/index');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id', false);
        $ticket = Mage::getModel('helpmate/ticket')->load($id);
        if (!$ticket->getId()) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $ticket->setData($data);
            }
        }
        $theards = array();
        $rowset = Mage::getModel('helpmate/theard')
            ->getCollection()
            ->addTicketFilter($ticket->getId());
        foreach ($rowset as $row) {
            $theards[] = $row->toArray();
        }
        $ticket->setTheards($theards);

        Mage::register('helpmate_ticket_data', $ticket);

        $this->loadLayout();
        $this->_setActiveMenu('helpmate/ticket');


        $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

        $this->_addContent(
            $this->getLayout()->createBlock(
                'helpmate/adminhtml_ticket_edit'
            )
        )->_addLeft(
            $this->getLayout()->createBlock(
                'helpmate/adminhtml_ticket_edit_tabs'
            )
        );
        $this->renderLayout();
    }

    public function newAction()
    {
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (empty($data)) {
            $data = array();
        }
        $_data = $this->getRequest()->getParams();
        $data = array_merge($data, $_data);
        Mage::getSingleton('adminhtml/session')->setFormData($data);
        $this->_redirect('*/*/edit');
    }

    public function saveAction()
    {
        $dataTicket = $this->getRequest()->getPost();
        try  {
            if (!$dataTicket) {
                throw new Exception(
                    Mage::helper('helpmate')->__('Unable to find item to save')
                );
            }

            $ticket = Mage::getModel('helpmate/ticket');
            $previosData = null;
            if (empty($dataTicket['id'])) {
                unset($dataTicket['id']);
            } else {
                $previosData = Mage::getModel('helpmate/ticket')
                    ->load($dataTicket['id'])
                    ->toArray();
            }
            if (empty($dataTicket['customer_id'])) {
                $dataTicket['customer_id'] = null;
            }
            if (isset($dataTicket['enabled'])) {
                $dataTicket['enabled'] = false;
            }  else  {
                $dataTicket['enabled'] = true;
            }
            $ticket->setData($dataTicket);
            $isNewTicket = !$previosData;

            $ticket->setModifiedAt(now());

            $isAssigned = (bool) Mage::getModel('helpmate/department_user')
                ->getCollection()
                ->addDepartmentFilter($dataTicket['department_id'])
                ->addUserFilter($dataTicket['user_id'])
                ->count();

            if (!$isAssigned) {
                throw new Exception(
                    Mage::helper('helpmate')->__('Your admin user has not been assigned to the chosen Department and has no access'
                ));
            }

            $isHasAccess = (bool) Mage::getModel('helpmate/department_user')
                ->getCollection()
                ->addDepartmentFilter($dataTicket['department_id'])
                ->addUserFilter(Mage::getSingleton('admin/session')->getUser()
                                        ->getId())
                ->count();
            if (!$isHasAccess) {
//                throw new Exception(
                Mage::getSingleton('adminhtml/session')->addNotice(
                    Mage::helper('helpmate')->__(
                        'This is not your area of ​​responsibility'
                ));
            }

            if ($isNewTicket) {
                $email = $customer = null;
                if (isset($dataTicket['customer_id'])) {
                    $customer = Mage::getModel('customer/customer')->load(
                        $dataTicket['customer_id']
                    );
                }
                if ($customer && $customer->getId()) {
                    $email = $customer->getEmail();
                }
                if (empty ($email) && isset($dataTicket['customer_id_query'])) {
                    $email = $dataTicket['customer_id_query'];
                }
                $ticket->setEmail($email)
                    ->setCreatedAt(now())
                    ->setNumber(
                        $ticket->generateNumberByEmail(
                            $ticket->getEmail()
                ));
            }
            $ticket->save();

            // add new theard
            if (!empty($dataTicket['text'])
                || ($previosData
                        && ($previosData['department_id'] != $dataTicket['department_id']
                            || $previosData['priority'] != $dataTicket['priority']
                            || $previosData['status'] != $dataTicket['status']
                ))
                ) {

                $theard = Mage::getModel('helpmate/theard');
                // set priority, department
                $dataTheard = array_merge($dataTicket, array(
                    'id'         => null,
                    'ticket_id'  => $ticket->getId(),
                    'user_id'    => Mage::getSingleton('admin/session')->getUser()
                                        ->getId(),
                    'file'       => $this->_saveFile(),
                    'created_at' => now()
                ));

                $theard->setData($dataTheard);

                if (!empty($dataTheard['text']) && $dataTheard['enabled']) {
                    $theard->setStatus(TM_Helpmate_Model_Status::STATUS_REPLIED);
                    $ticket->setStatus(TM_Helpmate_Model_Status::STATUS_REPLIED)
                        ->save();
                    $theard->save();
                    Mage::dispatchEvent('helpmate_send_ticket_answer', array(
                        'theard' => $theard
                    ));
                } else {
                    $theard->save();
                }
            }

            if ($previosData && $previosData['user_id'] !== $dataTicket['user_id']) {

                Mage::dispatchEvent('helpmate_ticket_user_changed', array(
                    'ticket_id' => $ticket->getId()
                ));
            }

            //success
            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('helpmate')->__('Item was successfully saved')
            );

        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
        }
        if ($ticket) {
            $dataTicket = $ticket->getData();
        }
        Mage::getSingleton('adminhtml/session')->setFormData($dataTicket);

        $this->_redirectReferer();
    }

    protected function _saveFile()
    {
//        if (true != Mage::getStoreConfig('helpmate/general/enabledAttached')) {
//            return;
//        }

        if (empty($_FILES)) {
            return;
        }


        //secure bug/feature here
        if (empty($_FILES['file']['name'])) {
            return;
        }
        $path = Mage::getBaseDir('media') . DS . 'helpmate' . DS;
        $uploader = new Varien_File_Uploader('file');
        $uploader->setAllowedExtensions(explode(
            ',',
            Mage::getStoreConfig('helpmate/general/attachedAllowedExtensions')
        ));

        $uploader->setAllowRenameFiles(true);
        $uploader->save($path);
        $fileName = $uploader->getUploadedFileName();

        unset($_FILES['file']);

        return str_replace(DS, '/', $fileName);
    }

    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if($id > 0 ) {
            try {
                $model = Mage::getModel('helpmate/ticket');

                $model->setId($id)
                ->delete();

                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__('Item was successfully deleted')
                );
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $id));
            }
        }
        $this->_redirect('*/*/');
    }
//

    public function mergeAction() {
        $ticketIds = $this->getRequest()->getParam('helpmate');
        if(!is_array($ticketIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('adminhtml')->__('Please select item(s)')
            );
            $this->_redirect('*/*/index');
            return;
        }
        try {
            if (2 !== count($ticketIds)) {
                Mage::getSingleton('adminhtml/session')->addError(
                    'Sorry but only 2 tickets can be merged.'
                );
                $this->_redirect('*/*/index');
                return;
            }
            $ticketId0 = min($ticketIds);
            $ticketId1 = max($ticketIds);


            $rowset = Mage::getModel('helpmate/theard')->getCollection()
                ->addTicketFilter($ticketId1)
            ;
            foreach ($rowset as $_theard) {
                $_theard->setTicketId($ticketId0)
                    ->save();
            }
            $oldTicket = Mage::getModel('helpmate/ticket')->load($ticketId1);
            $oldTicket->delete();

            Mage::getSingleton('adminhtml/session')->addSuccess(
                Mage::helper('adminhtml')->__(
                    'Ticket %s was merged with ticket %s', $ticketId0, $ticketId1
                )
            );
        } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError(
                $e->getMessage()
            );
        }
        $this->_redirect('*/*/index');
    }

    public function massDeleteAction() {
        $ticketIds = $this->getRequest()->getParam('helpmate');
        if(!is_array($ticketIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('adminhtml')->__('Please select item(s)')
            );
        } else {
            try {
                foreach ($ticketIds as $ticketId) {
                    $ticket = Mage::getModel('helpmate/ticket')->load($ticketId);
                    $ticket->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted',
                        count($ticketIds)
                    )
                );
            } catch (Exception $e) {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function massStatusAction()
    {
        $ticketIds = $this->getRequest()->getParam('helpmate');
        if(!is_array($ticketIds)) {
            Mage::getSingleton('adminhtml/session')->addError(
                Mage::helper('helpmate')->__(
                    'Please select item(s)'
            ));
        } else {
            try {
                foreach ($ticketIds as $tickewtId) {
                    $helpmate = Mage::getSingleton('helpmate/ticket')
                    ->load($tickewtId)
                    ->setStatus($this->getRequest()->getParam('status'))
                    ->setIsMassupdate(true)
                    ->save();
                }
                $this->_getSession()->addSuccess(
                    Mage::helper('helpmate')->__(
                        'Total of %d record(s) were successfully updated',
                        count($ticketIds)
                    )
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function exportCsvAction()
    {
        $fileName   = 'helpmate_tickets.csv';
        $content    = $this->getLayout()->createBlock('helpmate/adminhtml_ticket_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'helpmate_tickets.xml';
        $content    = $this->getLayout()->createBlock('helpmate/adminhtml_ticket_grid')
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

    public function customerAction()
    {
        $query = $this->getRequest()->getParam('query');
        $collection = Mage::getModel('customer/customer')->getCollection()
            ->addNameToSelect()
            ->addAttributeToFilter(array(
                array('attribute' => 'email', 'like' => "%{$query}%"),
                array('attribute' => 'name',  'like' => "%{$query}%")
            ));
        $collection->getSelect()->limit(7);

        $html = '';
        foreach ($collection as $_customer) {
            $html .= "<li id=\"{$_customer->getId()}\" title=\"{$_customer->email}\">
                <strong>{$_customer->getName()}</strong><br/>
                <span class=\"informal\">{$_customer->email}</span>
            </li>"
            ;
        }
        echo '<ul>' . $html . '</ul>';

    }

    public function orderAction()
    {
        $query = $this->getRequest()->getParam('query');
        $collection = Mage::getModel('sales/order')->getCollection();
        $collection->getSelect()
            ->where('increment_id LIKE ?', "%{$query}%")
            ->limit(7);

        $html = '';
        foreach ($collection as $_order) {
            $html .= "<li id=\"{$_order->getId()}\" title=\"{$_order->increment_id}\">
                <strong>{$_order->increment_id}</strong><br/>
                <span class=\"informal\">placed on {$_order->created_at} by {$_order->customer_email}</span>
            </li>"
            ;
        }
        echo '<ul>' . $html . '</ul>';
    }
}
