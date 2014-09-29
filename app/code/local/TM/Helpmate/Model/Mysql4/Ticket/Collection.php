<?php

class TM_Helpmate_Model_Mysql4_Ticket_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_addDepartmentName = false;
    protected $_addPriorityName   = false;
    protected $_addStatusName     = false;

    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/ticket');
    }

    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('main_table.customer_id=?', $customerId);
        return $this;
    }

    public function addCustomerData()
    {
        $customer   = Mage::getModel('customer/customer');
        // alias => attribute_code
        $attributes = array(
            'customer_lastname'     => 'lastname',
            'customer_firstname'    => 'firstname',
            'customer_email'        => 'email'
        );

        foreach ($attributes as $alias => $attributeCode) {
            $attribute = $customer->getAttribute($attributeCode);
            /* @var $attribute Mage_Eav_Model_Entity_Attribute_Abstract */

            if ($attribute->getBackendType() == 'static') {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackend()->getTable()),
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    array($alias => $attribute->getAttributeCode())
                );

                $this->_fields[$alias] = sprintf('%s.%s', $tableAlias, $attribute->getAttributeCode());
            } else {
                $tableAlias = 'customer_' . $attribute->getAttributeCode();

                $joinConds  = array(
                    sprintf('%s.entity_id=main_table.customer_id', $tableAlias),
                    $this->getConnection()->quoteInto($tableAlias . '.attribute_id=?', $attribute->getAttributeId())
                );

                $this->getSelect()->joinLeft(
                    array($tableAlias => $attribute->getBackend()->getTable()),
                    join(' AND ', $joinConds),
                    array($alias => 'value')
                );

                $this->_fields[$alias] = sprintf('%s.value', $tableAlias);
            }
        }
        $this->setFlag('has_customer_data', true);
        return $this;
    }

    public function addStoreFilter($store)
    {
        if ($store instanceof Mage_Core_Model_Store) {
            $store = array($store->getId());
        }
        if (!is_array($store)) {
            $store = array($store);
        }
        $this->getSelect()->where('main_table.store_id IN (?)', $store);
        return $this;
    }

    public function addFieldToFilter($field, $condition=null)
    {
        if (isset($this->_fields[$field])) {
            $field = $this->_fields[$field];
        }

        return parent::addFieldToFilter($field, $condition);
    }

    public function addStatusFilter($status = true)
    {
        $this->getSelect()->where('main_table.status = ?', $status);
        return $this;
    }

    public function addLessModifiedAtFilter(Zend_Date $time)
    {
        $time = $time->toString('YYYY-MM-dd HH:mm:ss', 'iso');
        $this->getSelect()->where('main_table.modified_at < ?', $time);
        return $this;
    }

    public function addGreateModifiedAtFilter(Zend_Date $time)
    {
        $time = $time->toString('YYYY-MM-dd HH:mm:ss', 'iso');
        $this->getSelect()->where('main_table.modified_at > ?', $time);
        return $this;
    }

    public function addDepartmentName($flag = true)
    {
        $this->_addDepartmentName = (bool) $flag;
        return $this;
    }

    public function addPriorityName($flag = true)
    {
        $this->_addPriorityName = (bool) $flag;
        return $this;
    }

    public function addStatusName($flag = true)
    {
        $this->_addStatusName = (bool) $flag;
        return $this;
    }

    protected function _afterLoad() {

        parent::_afterLoad();

        if ($this->_addDepartmentName) {
            $departments = Mage::getModel('helpmate/department')->getOptionArray();
        }
        if ($this->_addPriorityName) {
            $priorities  = Mage::getModel('helpmate/priority')->getOptionArray();
        }
        if ($this->_addStatusName) {
            $statusses   = Mage::getModel('helpmate/status')->getOptionArray();
        }

        foreach ($this as &$row) {
            if ($this->_addDepartmentName) {
                $row->setDepartmentName($departments[$row->getDepartmentId()]);
            }
            if ($this->_addPriorityName) {
                $row->setPriority($priorities[$row->getPriority()]);
            }
            if ($this->_addStatusName) {
                $row->setStatus($statusses[$row->getStatus()]);
            }

            if ($this->getFlag('has_customer_data')) {
                $email = $row->getCustomerEmail();
                if (empty($email)) {
                    $email = $row->getData('email');
                }

                $concatData = implode(' ', array(
                    $email,
                    $row->getData('customer_firstname'),
                    $row->getData('customer_lastname')
                ));
//                Zend_Debug::dump($row->getData());
                $row->setCustomerConcatData($concatData);
            }
        }

        return $this;
    }

    public function addCustomerConcatDataFilter($value)
    {

        $value = '%' . $value . '%';
        $this->getSelect()
            ->orWhere("main_table.email LIKE ?", $value)
            ->orWhere("customer_firstname.value LIKE ?", $value)
            ->orWhere("customer_lastname.value LIKE ?", $value)
            ;
        return $this;
    }
}