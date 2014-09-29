<?php

class Compandsave_OrderFilter_Block_Adminhtml_Sales_Order_Grid_Massaction
    extends Enterprise_SalesArchive_Block_Adminhtml_Sales_Order_Grid_Massaction
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('orderfilter/widget/grid/massaction.phtml');
    }

    public function getFilterItems()
    {
        return Mage::getResourceModel('compandsave_orderfilter/bookmark_collection');
    }
}
