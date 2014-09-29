<?php

class Compandsave_OrderFilter_Block_Adminhtml_Sales_Order_Grid
    extends Mage_Adminhtml_Block_Sales_Order_Grid
{

//    protected function _prepareColumns()
//    {
//        $this->addColumn('batch_id', array(
//            'header' => Mage::helper('sales')->__('Batch Ids'),
//            'index' => 'batch_id',
//            'type'  => 'options',
//            'width' => '70px',
//            'options' => Mage::getSingleton('sales/order_config')->getStatuses(),
//        ));
//        parent::_prepareColumns();
//    }

    protected function _getCollectionClass()
    {
        return 'compandsave_orderfilter/order_grid_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $filterId = intval($this->getRequest()->getParam('filter_id'));
        if($filterId  != 0) {
            $collection->addFilterId($filterId);
        }
        $this->setCollection($collection);
    }
}
