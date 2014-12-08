<?php
class TM_Helpmate_Model_Api2_Theard_Rest_Customer_V1 extends TM_Helpmate_Model_Api2_Theard
{
    /**
     * Create customer
     *
     * @param array $data
     * @return string
     */
    protected function _create(array $data)
    {
        $ticketId = $this->getRequest()->getParam('ticket_id');
        $ticket = Mage::getModel('helpmate/ticket')->load($ticketId);
        if ($ticket->getCustomerId() != $this->getApiUser()->getUserId()) {
            throw new Exception(self::RESOURCE_NOT_FOUND);
        }
        $ticket->setStatus(TM_Helpmate_Model_Status::STATUS_OPEN);
        try {
            $ticket->save();
            $_data = array(
                'ticket_id'     => $ticket->getId(),
                'text'          => $data['text'],
                'created_at'    => now(),
                'file'          => '',
                'user_id'       => null,
                'status'        => $ticket->getStatus(),
                'priority'      => $ticket->getPriority(),
                'department_id' => $ticket->getDepartmentId()
            );
            $theard = Mage::getModel('helpmate/theard');
            $theard->setData($_data)->save();
            try {
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
     * @return TM_Helpmate_Model_Mysql4_Theard_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        $ticketId = $this->getRequest()->getParam('ticket_id');
        $theard = Mage::getModel('helpmate/ticket')->load($ticketId);
        if ($theard->getCustomerId() != $this->getApiUser()->getUserId()) {
            throw new Exception(self::RESOURCE_NOT_FOUND);
        }
        /* @var $collection TM_Helpmate_Model_Mysql4_Theard_Collection */
        $collection = Mage::getModel('helpmate/theard')->getCollection()
            ->addEnabledFilter(1)
            ->addTicketFilter(
                $this->getRequest()->getParam('ticket_id')
            )
        ;
        $this->_applyCollectionModifiers($collection);

        $userCollection = Mage::getModel('admin/user')->getCollection();
        foreach ($collection as &$theard) {
            $user = $userCollection->getItemById($theard->getUserId());
            if ($user) {
                $theard->setUserName($user->getName());
            }
        }
        return $collection;
    }
}
