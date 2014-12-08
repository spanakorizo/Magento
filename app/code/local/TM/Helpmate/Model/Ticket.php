<?php
class TM_Helpmate_Model_Ticket extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/ticket');
    }

    public function getDepartment()
    {
        return Mage::getModel('helpmate/department')->load(
            $this->getDepartmentId()
        );
    }

    public function getLastMessageId()
    {
        $collection = Mage::getModel('helpmate/theard')->getCollection()
            ->addTicketFilter($this->getId())
            ;
        $messageId = null;
        foreach ($collection as $theard) {
            $currentMessageId = $theard->getMessageId();
            if (false === empty($currentMessageId)) {
                $messageId = $currentMessageId;
            }
        }
        return $messageId;
    }

    /**
     * Load customer by email
     *
     * @param   string $number
     * @return TM_Helpmate_Model_Ticket
     */
    public function loadByNumber($number)
    {
        $this->_getResource()->loadByNumber($this, $number);
        return $this;
    }

    public function getAssignedUser()
    {
        return Mage::getModel('admin/user')->load($this->getUserId());
    }

//    public function getTheards()
//    {
//        return $rowset = Mage::getModel('helpmate/theard')->getCollection()
//            ->addTicketFilter($this->getId())
//            ;
//    }

    protected function _beforeSave()
    {
        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(now());
        }
        $this->setModifiedAt(now());

        return parent::_beforeSave();
    }

    protected function _generateNumber()
    {
        if (null == $this->getId()) {
            throw new Exception;
        }
        $prefix = '';
        for ($i = 0; $i < 3; $i++) {
          $prefix .= chr(rand(ord('a'), ord('z')));
        }
        $id = (string) $this->getId();

        for ($i = 0; strlen($id) - 5; $i++){
            $id = '0' . $id;
        }
        return strtoupper($prefix) . '-' . $id;
    }

    protected function _afterSave()
    {
        $number = $this->getNumber();
        if (empty($number)) {
            $number =  $this->_generateNumber();
            $this->setNumber($number);

            $this->_getResource()->save($this);
        }

        return parent::_afterSave();
    }

    /**
     * Load visitor info
     *
     * @param   string $number
     * @return TM_Helpmate_Model_Ticket
     */
    public function getVisitorInfo()
    {
        return $this->_getResource()->getVisitorInfo($this);
    }
}
