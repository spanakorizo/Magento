<?php
class TM_Helpmate_Model_Api2_Priority_Rest_Customer_V1 extends TM_Helpmate_Model_Api2_Ticket
{

    /**
     * Get items list
     *
     * @return array
     */
    protected function _retrieveCollection()
    {
        $_data = Mage::getModel('helpmate/priority')->getOptionArray();
        $data = array();
        foreach ($_data as $key => $value) {
            $data[] = array('id' => $key, 'name' => $value);
        }
        return $data;
    }
}
