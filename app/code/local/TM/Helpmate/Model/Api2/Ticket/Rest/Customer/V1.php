<?php
class TM_Helpmate_Model_Api2_Ticket_Rest_Customer_V1 extends TM_Helpmate_Model_Api2_Ticket
{
    /**
     * Create customer
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        $customerId = $this->getApiUser()->getUserId();
        $customer = Mage::getModel('customer/customer')->load($customerId);

        $data['status']      = TM_Helpmate_Model_Status::STATUS_OPEN;
        $data['store_id']    = Mage::app()->getStore()->getId();
        $data['customer_id'] = $customer->getId();
        $data['email']       = $customer->getEmail();
        $data['number']      = Mage::getModel('helpmate/ticket')->generateNumberByEmail($data['email']);
        $data['created_at']  = $data['modified_at'] = now();

        $adminUser = Mage::getModel('helpmate/department_user')
            ->getCollection()
            ->addDepartmentFilter($data['department_id'])
            ->getFirstItem()
        ;
        $data['user_id'] = null;
        if ($adminUser) {
            $data['user_id'] = $adminUser->getUserId();
        }

        /** @var $customer Mage_Customer_Model_Customer */
        $ticket = Mage::getModel('helpmate/ticket');
        $ticket->setData($data);

//        throw new Exception('xxxxx' . $data['store_id']);
        try {
            $ticket->save();
            $_data = array(
                'ticket_id'     => $ticket->getId(),
                'text'          => $data['text'],
                'created_at'    => now(),
                'files'         => '',
                'user_id'       => null,
                'status'        => $data['status'],
                'priority'      => $data['priority'],
                'department_id' => $data['department_id']
            );
            $theard = Mage::getModel('helpmate/theard');
            $theard->setData($_data)
                ->save();

             try {
                Mage::dispatchEvent('helpmate_notify_customer_ticket_create', array(
                    'ticket'  => $ticket
                ));

                Mage::dispatchEvent('helpmate_notify_admin_ticket_change', array(
                    'theard'  => $theard
                ));
            } catch (Mage_Core_Exception $e) {
            }

        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return $this->_getLocation($ticket);
    }

    /**
     * Retrieve information about specified item
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        /* @var $item TM_Helpmate_Model_Ticket */
        $item = $this->_loadItemById(
            $this->getRequest()->getParam('id')
        );
        $data = $item->getData();
        if ($data['customer_id'] != $this->getApiUser()->getUserId()) {
            throw new Exception(self::RESOURCE_NOT_FOUND);
        }
        $userCollection = Mage::getModel('admin/user')->getCollection();
        $user = $userCollection->getItemById($item->getUserId());
        if ($user) {
            $data['user_name'] = $user->username;
        }
        return $data;
    }

    /**
     * Get items list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();

        return isset($data['items']) ? $data['items'] : $data;
    }

    /**
     * Retrieve items collection
     *
     * @return TM_Helpmate_Model_Mysql4_Ticket_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /* @var $collection TM_Helpmate_Model_Mysql4_Ticket_Collection */
        $collection = Mage::getModel('helpmate/ticket')->getCollection();
        $this->_applyCollectionModifiers($collection);

        $collection->addCustomerFilter($this->getApiUser()->getUserId());

        $userCollection = Mage::getModel('admin/user')->getCollection();
        foreach ($collection as &$ticket) {
            $user = $userCollection->getItemById($ticket->getUserId());
            if ($user) {
                $ticket->setUserName($user->getName());
            }
        }

        return $collection;
    }
}