<?php
class TM_KnowledgeBase_Model_System_Config_Source_Category
{

    public function toOptionArray()
    {
        $collection = Mage::getModel('knowledgebase/category')->getCollection();
        $array  = array(
//            array(
//                'label' => 'Have not category',
//                'value' => null
//            )
        );
        foreach ($collection as $row) {
            $array[$row->getId()] = array(
                'value' => $row->getId(),
                'label' => $row->getName()
            );
        }
        return $array;
    }
}