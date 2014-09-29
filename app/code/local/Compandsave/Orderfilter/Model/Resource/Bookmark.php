<?php
class Compandsave_OrderFilter_Model_Resource_Bookmark
    extends Mage_Core_Model_Resource_Db_Abstract
{
    protected function _construct()
    {
        //Pass the tablename (handle) and primary key
        $this->_init('compandsave_orderfilter/orderfilter','entity_id');
    }
}
