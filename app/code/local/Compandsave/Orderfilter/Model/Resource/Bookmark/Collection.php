<?php
class Compandsave_OrderFilter_Model_Resource_Bookmark_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected function _construct()
    {
        /* Typically only the first arguments is given and used for
        both the resource model and data model arguments. */
        $this->_init('compandsave_orderfilter/bookmark');
    }
}
