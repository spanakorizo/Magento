<?php

class Compandsave_OrderFilter_Model_Resource_Order_Grid_Collection
    extends Mage_Sales_Model_Resource_Order_Grid_Collection
{

    public function addFilterId($filterId)
    {
        $filterSql = Mage::getModel('compandsave_orderfilter/bookmark')
            ->load($filterId)
            ->getFilterSql();
        if (!empty($filterSql)) {
            $select = $this->getSelect();
            $select->where('entity_id in(?)', new Zend_Db_Expr($filterSql));
        }
    }
}
