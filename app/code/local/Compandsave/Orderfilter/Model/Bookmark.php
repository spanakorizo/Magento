<?php
class Compandsave_OrderFilter_Model_Bookmark
    extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        //Set the resource model and set the collection
        //class as resource + '_collection'
        $this->_init('compandsave_orderfilter/bookmark');
    }
}
