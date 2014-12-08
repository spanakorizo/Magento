<?php

/**
 * API2 class for tickets
 *
 */
class TM_Helpmate_Model_Api2_Ticket extends Mage_Api2_Model_Resource
{
    /**
     * Load item by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return TM_Helpmate_Model_Ticket
     */
    protected function _loadItemById($id)
    {
        /* @var $item TM_Helpmate_Model_Ticket */
        $item = Mage::getModel('helpmate/ticket')->load($id);
//        Zend_Debug::dump($item); die;

        if (!$item->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $item;
    }
}