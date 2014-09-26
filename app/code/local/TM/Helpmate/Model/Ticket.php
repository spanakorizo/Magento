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
    
    /**
     *
     * @param string $email
     * @return  string the hash as a 32-character hexadecimal number. 
     */
    public function generateNumberByEmail($email = null) 
    {
        if (null === $email) {
            $email = $this->getEmail();
        }
        return md5(time() . $email);
    }
    
    public function setNumber($number = null) 
    {
        if (null === $number) {
            $number = $this->generateNumberByEmail();
        }
        return $this->setData('number', $number);
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

}