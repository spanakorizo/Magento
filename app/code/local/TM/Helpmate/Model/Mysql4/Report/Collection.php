<?php

class TM_Helpmate_Model_Mysql4_Report_Collection extends TM_Helpmate_Model_Mysql4_Ticket_Collection
{
    /**
     * Join fields
     *
     * @param string $from
     * @param string $to
     * @return TM_Helpmate_Model_Mysql4_Department_Report_Collection
     */
    protected function _joinFields($from = '', $to = '')
    {
        $this
            ->groupByDepartment()
            ->addReport()
            ->addFieldToFilter(
                'created_at', array('from' => $from, 'to' => $to, 'datetime' => true)
            );
        return $this;
    }

    /**
     * Add group By department attribute
     *
     * @return
     */
    public function groupByDepartment()
    {
        $this->getSelect()
            ->where('main_table.department_id IS NOT NULL')
            ->group('main_table.department_id');

        /*
         * Allow Analytic functions usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    /**
     * Set date range
     *
     * @param string $from
     * @param string $to
     * @return TM_Helpmate_Model_Mysql4_Department_Report_Collection
     */
    public function setDateRange($from, $to)
    {
        $this->_reset()
            ->_joinFields($from, $to);
        return $this;
    }

    /**
     * Set store filter collection
     *
     * @param array $storeIds
     * @return TM_Helpmate_Model_Mysql4_Department_Report_Collection
     */
    public function setStoreIds($storeIds)
    {
        $storeIds = (array)$storeIds;
        $allowedStoreIds = array_keys(Mage::app()->getStores());
        if (!count(array_diff($allowedStoreIds, $storeIds))) {
            $storeIds[] = 0;
        }
        if ($storeIds) {
            $this->addFieldToFilter('store_id', array('in' => $storeIds));
        }

        return $this;
    }

    public function addReport($status = true)
    {
        $this->_addReport = (bool) $status;
        return $this;
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        if ($this->_addReport) {
            $this->_addReport();
        }
        return $this;
    }

    protected function _addReport()
    {
        $statusses = Mage::getModel('helpmate/status')->getOptionArray();
        $_counts = array_fill_keys(array_keys($statusses), 0);

        $_data = array();
        foreach ($this as $_ticket) {
            $departmentId = (int)$_ticket->getDepartmentId();
            if (!isset($_data[$departmentId])) {
                $_data[$departmentId]= $_counts + array('ticket_count' => 0);
            }
            $_data[$departmentId]['ticket_count']++;

            $_status = (int) $_ticket->getStatus();
            $_data[$departmentId][$_status]++;

        }
        foreach ($this as $_ticket) {
            $departmentId = (int)$_ticket->getDepartmentId();
            $counts = $_data[$departmentId];
            $_ticket->setTicketCount($counts['ticket_count']);
            unset($counts['ticket_count']);

            foreach ($statusses as $id => $status) {
                $_index = 'ticket_' . strtolower($status) . '_count';
                $_ticket->setData($_index, $counts[$id]);
            }
        }

        return $this;
    }
}