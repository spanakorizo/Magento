<?php

class TM_Helpmate_Model_Mysql4_Department_User_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/department_user');
    }

    public function addDepartmentFilter($departmentId)
    {
        if (!is_array($departmentId)) {
            $departments = array($departmentId);
        }
        $this->getSelect()->where('main_table.department_id IN (?)', $departments);
        return $this;
    }

    public function addUserFilter($users)
    {
        if (!is_array($users)) {
            $users = array($users);
        }
        $this->getSelect()->where('main_table.user_id IN (?)', $users);
        return $this;
    }

    public function addEmailFilter($emails)
    {
        if (!is_array($emails)) {
            $emails = array($emails);
        }
        $this->getSelect()->join(
            array('u' => $this->getTable('admin/user')),
            'u.user_id = main_table.user_id',
            'email'
        );
        $this->getSelect()->where('u.email IN (?)', $emails);
        return $this;
    }

    public function addEmailData()
    {
        $this->getSelect()->join(
            array('u' => $this->getTable('admin/user')),
            'u.user_id = main_table.user_id',
            'email'
        );
        return $this;
    }
}