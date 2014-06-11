<?php
class TM_KnowledgeBase_Model_Product_Attribute_Faq_Backend extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    //Mage_Eav_Model_Entity_Attribute_Backend_Array
    public function beforeSave($object)
    {
        $data = $object->getData($this->getAttribute()->getAttributeCode());
        if (is_array($data)) {
            $object->setData($this->getAttribute()->getAttributeCode(), implode(',', $data));
        }
        return parent::beforeSave($object);
    }

    public function afterLoad($object) {
        $attributeCode = $this->getAttribute()->getName();
        if ('knowledgebase_faq' === $attributeCode) {
            $data = $object->getData($attributeCode);
            if (is_string($data) && !empty($data)) {
                $object->setData($attributeCode, explode(',', $data));
            }
        }
        return $this;
    }
}
