<?php
class TM_Helpmate_Model_Api2_Department_Rest_Customer_V1 extends TM_Helpmate_Model_Api2_Ticket
{

    /**
     * Get items list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $data = $this->_getCollectionForRetrieve()->load()->toArray();

//        Zend_Debug::dump($data['items']);
        return isset($data['items']) ? $data['items'] : $data;
    }

    /**
     * Retrieve items collection
     *
     * @return TM_Helpmate_Model_Mysql4_Ticket_Collection
     */
    protected function _getCollectionForRetrieve()
    {
        /* @var $collection TM_Helpmate_Model_Mysql4_Department_Collection */
        $collection = Mage::getModel('helpmate/department')->getCollection();
        $this->_applyCollectionModifiers($collection);

        $collection->addActiveFilter();

        return $collection;
    }
}