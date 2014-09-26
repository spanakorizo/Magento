<?php

class TM_KnowledgeBase_Model_Mysql4_Faq extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {    
        // Note that the id refers to the key field in your database table.
        $this->_init('knowledgebase/faq', 'id');
    }

    /**
     * Load ticket by number
     *
     * @param TM_KnowledgeBase_Model_Faq $faq
     * @param string $identifier
     * @return TM_KnowledgeBase_Model_Mysql4_Faq
     * @throws Mage_Core_Exception
     */
    public function loadByIdentifier(TM_KnowledgeBase_Model_Faq $faq, $identifier)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getMainTable(), array($this->getIdFieldName()))
            ->where('identifier=:identifier');

        if ($id = $this->_getReadAdapter()->fetchOne($select, array('identifier' => $identifier))) {
            $this->load($faq, $id);
        } else {
            $faq->setData(array());
        }
        return $this;
    }
}