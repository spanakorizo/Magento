<?php

class TM_KnowledgeBase_Model_Mysql4_Category_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected  $_addFaqFlag = false;
    protected  $_storeId = null;


    public function _construct()
    {
        parent::_construct();
        $this->_init('knowledgebase/category');
    }
    
    /**
     *
     * @param int $storeId
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addStoreFilter($storeId = null)
    {
        if (null === $storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }
        $this->_storeId = $storeId;
        $this->getSelect()->where(
            'main_table.store_id IN (?)', 
            array_unique(array(0, $this->_storeId))
        );
        return $this;
    }
    
    /**
     *
     * @param string $identifier
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addIdentifierFilter($identifier)
    {
        if (!empty($identifier)) {
            $this->getSelect()->where('main_table.identifier = ?', $identifier);
        }
        return $this;
    }

    /**
     *
     * @param bool $active
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addEnabledFilter($active = true)
    {
        if (true === $active) {
            $this->getSelect()->where('main_table.active = 1');
        } else {
            $this->getSelect()->where('main_table.active <> 1');
        }
        return $this;
    }
    
    protected function _beforeLoad() 
    {
        parent::_beforeLoad();
         
        // order set
        $this->getSelect()->order('main_table.sort_order ASC');
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        if ($this->_addFaqFlag) {
            $this->_addFaqData();
        }
        return $this;
    }

    public function addFaqData($flag = true)
    {
        $this->_addFaqFlag = $flag;
        return $this;
    }

    protected  function _addFaqData()
    {
        $faqrowset = array();
        $collection = Mage::getModel('knowledgebase/faq')->getCollection()
            ->addEnableFilter();
        if (null !== $this->_storeId) {
            $collection->addStoreFilter($this->_storeId);
        }
        foreach ($collection as $faq) {
            $faqrowset[$faq->id] = $faq;
        }
        $faqs = array();
        $rowset = Mage::getModel('knowledgebase/faq_category')->getCollection();
        foreach ($rowset as $row) {
            if (isset($faqrowset[(int)$row->getFaqId()])) {
                $faqs[$row->getCategoryId()][] = $faqrowset[(int)$row->getFaqId()];
            }
        }
        foreach ($this as $row) {
            $_faqs = array();
            if (isset($faqs[$row->getId()])) {
                $_faqs =  $faqs[$row->getId()];
            }
            $row->setData('faqs', $_faqs);
        }
        return $this;
    }
    
    public function addNameFilter($value) 
    {
        $this->getSelect()->where('main_table.name LIKE ?', '%' . $value . '%');
        return $this;
    }
}