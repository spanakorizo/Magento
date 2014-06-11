<?php
class TM_Helpmate_Model_Department_User extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/department_user');
    }
}