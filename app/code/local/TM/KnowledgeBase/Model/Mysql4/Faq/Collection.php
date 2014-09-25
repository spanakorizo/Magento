<?php

class TM_KnowledgeBase_Model_Mysql4_Faq_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    protected $_addCategoryNamesFlag = false;
    protected $_addCategoriesFlag    = false;
    protected $_addStoresFlag        = false;
    protected $_addAuthorDataFlag    = false;
    protected $_storeId              = null;
    protected $_categotyNameFilter   = null;
    protected $_limit                = null;
    protected $_addUsedFlagByProduct = null;
    protected $_usedFilter           = null;

    public function _construct()
    {
        parent::_construct();
        $this->_init('knowledgebase/faq');
    }

    protected function _beforeLoad()
    {
        parent::_beforeLoad();

        // order set
        $this->getSelect()->order('main_table.sort_order ASC');
//        Zend_Debug::dump($this->getSelect()->__toString());
    }

    protected function _afterLoad()
    {
        parent::_afterLoad();

        if ($this->_addCategoryNamesFlag) {
            $this->_addCategoryNamesData();
        }

        if ($this->_addAuthorDataFlag) {
            $this->_addAuthorData();
        }

        if ($this->_addCategoriesFlag) {
            $this->_addCategoriesData();
        }

        if ($this->_addStoresFlag || null !== $this->_storeId) {
            $this->_addStoresData();
        }

        if (null !== $this->_storeId) {

            foreach ($this as $key => $row) {
                $stores = $row->getStores();
                if (in_array(0, $stores) || in_array($this->_storeId, $stores)) {
                    continue;
                }
                $this->removeItemByKey($key);
            }
        }

        if (null !== $this->_limit) {
            $i = 0;
            foreach ($this as $key => $row) {
                if ($i < $this->_limit) {
                    $i++;
                    continue;
                }
                $this->removeItemByKey($key);
            }
        }

        if (null !== $this->_addUsedFlagByProduct) {

            $_ids = $this->_addUsedFlagByProduct->getData('knowledgebase_faq');
            if (empty($_ids)) {
                $_ids = array();
            }

            foreach ($this as $key => $row) {
                 $value = 0;
                if (in_array($row->getId(), $_ids)) {
                    $value = 1;
                }
                $row->setUsed($value);
            }
            if (null !== $this->_usedFilter) {
                foreach ($this as $key => $row) {
                    if ($row->getUsed() != $this->_usedFilter) {
                        $this->removeItemByKey($key);
                    }
                }
            }
        }

        return $this;
    }

    public function addUsedDataByProduct($product)
    {
        $this->_addUsedFlagByProduct = $product;
        return $this;
    }

    public function addUsedFilter($value)
    {
        $this->_usedFilter = $value ? 1 : 0;
        return $this;
    }

    public function addCategoryNamesData($flag = true)
    {
        $this->_addCategoryNamesFlag = (bool) $flag;
        return $this;
    }

//    protected  function _addCategoryStoreData()
//    {
//        $categoryToStore = array();
//        foreach (Mage::getModel('knowledgebase/category')->getCollection() as $category) {
//            $categoryToStore[$category->id] = (int)$category->store_id;
//        }
//        $categories = array();
//        $rowset = Mage::getModel('knowledgebase/faq_category')->getCollection();
//
//        foreach ($rowset as $row) {
//            $categories[$row->faq_id][$row->category_id] = $categoryToStore[$row->category_id];
//        }
//        foreach ($this as $row) {
//            if (isset($categories[$row->getId()])) {
//                $row->setData('stores', $categories[$row->getId()]);
//            }
//        }
////        return $this;
//    }

    public function addCategoryNameFilter($value)
    {
        $this->_categotyNameFilter = $value;
        return $this;
    }

    protected  function _addCategoryNamesData()
    {
        $categoryNames = array();
        $collection = Mage::getModel('knowledgebase/category')->getCollection();
        if (null !== $this->_categotyNameFilter) {
            $collection->addNameFilter($this->_categotyNameFilter);
        }
//        Zend_Debug::dump($collection->getSelect()->__toString());
        foreach ($collection as $category) {
            $categoryNames[$category->id] = $category->name;
        }
        $categories = array();
        $rowset = Mage::getModel('knowledgebase/faq_category')->getCollection();
        foreach ($rowset as $row) {
            if (false === isset($categories[$row->faq_id])) {
                $categories[$row->faq_id] = '';
            }
            if (false === empty($categories[$row->faq_id])) {
                $categories[$row->faq_id] .= ', ';
            }
            $categories[$row->faq_id] .= $categoryNames[(int)$row->getCategoryId()];
        }
        foreach ($this as $row) {
            if (isset($categories[$row->getId()])) {
                $row->setData('categorynames', $categories[$row->getId()]);
            }
        }
//        return $this;
    }

    protected  function _addCategoriesData()
    {
        $categorySet = array();
        foreach (Mage::getModel('knowledgebase/category')->getCollection() as $category) {
            $categorySet[$category->id] = $category;
        }
        $categories = array();
        $rowset = Mage::getModel('knowledgebase/faq_category')->getCollection();
        foreach ($rowset as $row) {
            $categories[$row->getFaqId()][]= $categorySet[(int)$row->getCategoryId()];
        }
        foreach ($this as $row) {
            if (isset($categories[$row->getId()])) {
                $row->setData('categories', $categories[$row->getId()]);
            }
        }
//        return $this;
    }

    public function addCategoriesData($flag = true)
    {
        $this->_addCategoriesFlag = (bool) $flag;
        return $this;
    }

    protected  function _addStoresData()
    {
        $stores = array();
        $rowset = Mage::getModel('knowledgebase/faq_store')->getCollection();
        foreach ($rowset as $row) {
            $stores[$row->getFaqId()][]= $row->getStoreId();
        }
        foreach ($this as $row) {
            if (isset($stores[$row->getId()])) {
                $row->setData('stores', $stores[$row->getId()]);
            }
        }
//        return $this;
    }

    public function addStoresData($flag = true)
    {
        $this->_addStoresFlag = (bool) $flag;
        return $this;
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
        return $this;
    }
//    /**
//     *
//     * @param string $term
//     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
//     */
//    public function addTerm($term)
//    {
//        $this->getSelect()->orWhere(
//            "title LIKE ? OR content LIKE ? OR identifier LIKE ?", "%{$term}%"
//        );
//        return $this;
//    }

    public function addSearchQuery($query)
    {
        $attributes = array('title', 'content');
        $andWhere = array();
        foreach ($attributes as $attribute) {
            foreach (explode(' ', trim($query)) as $word) {
                $andWhere[] = $this->_getConditionSql(
                    $attribute, array('like' => '%' . $word . '%')
                );
            }
            $this->getSelect()->orWhere(implode(' AND ', $andWhere));
            $andWhere = array();
        }
        $this->getSelect()->columns($attributes);
        return $this;
    }

    public function addMatchSearchQuery($query)
    {
        $score = $this->getResource()->getReadConnection()
            ->quoteInto("MATCH (title, content) AGAINST (?) AS score", $query);
        $this->getSelect()
            ->where('MATCH (title, content) AGAINST (?)', $query)
            ->columns($score)
            ->order('score DESC')
            ;
        return $this;
    }

    /**
     *
     * @param string $identifier
     * @param bool $active
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addCategoryIdentifierFilter($identifier, $active = true)
    {
        $this->getSelect()->join(
            array('fc' => $this->getTable('knowledgebase/faq_category')),
            'fc.faq_id = main_table.id',
            array()
        )->join(
            array('c' => $this->getTable('knowledgebase/category')),
            'c.id = fc.category_id',
            array()
        )->where('c.identifier = ?', $identifier);

        if (true === $active) {
            $this->getSelect()->where('c.active = 1');
        } else {
            $this->getSelect()->where('c.active <> 1');
        }
        return $this;
    }

    /**
     *
     * @param mixed $ids
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addCategoryIdFilter($ids)
    {
        if (!is_array($ids)) {
            $ids = array($ids);
        }
        $this->getSelect()->join(
            array('fc' => $this->getTable('knowledgebase/faq_category')),
            'fc.faq_id = main_table.id',
            array()
        )->join(
            array('c' => $this->getTable('knowledgebase/category')),
            'c.id = fc.category_id',
            array()
        )->where('c.id IN (?)', $ids)
        ->group('id');

        return $this;
    }

    /**
     *
     * @param bool $enable
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addEnableFilter($enable = true)
    {
        if (true === $enable) {
            $this->getSelect()->where('main_table.status = 1');
        } else {
            $this->getSelect()->where('main_table.status <> 1');
        }
        return $this;
    }

    /**
     * @param strine $sortOrder [ASC|DESC]
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function setRateOrder($sortOrder = 'DESC')
    {
        $this->getSelect()->order('main_table.rate ' . $sortOrder);
        return $this;
    }

    /**
     *
     * @param int $limit
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addLimit($limit = 6)
    {
        $this->_limit = $limit;
//        $this->getSelect()->limit($limit);

        return $this;
    }

    /**
     *
     * @param bool $flag
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addAuthorData($flag = true)
    {
        $this->_addAuthorDataFlag = (bool) $flag;
        return $this;
    }

    protected function _addAuthorData()
    {
        $modelAuthor = Mage::getModel('admin/user');
        foreach ($this as $row) {
            $authorRow = $modelAuthor->load($row->getUserId());
            $row->setData('author_data', $authorRow);
//            foreach ($authorRow->getData() as $key => $value) {
//                $row->setData('author_' . $key, $value);
//            }
        }
        return $this;
    }


    /**
     * @param int| array $id
     * @return TM_KnowledgeBase_Model_Mysql4_Faq_Collection
     */
    public function addIdFilter($id)
    {
        if (!is_array($id)) {
            $id = array($id);
        }
        $this->getSelect()->where('main_table.id IN (?)', $id);
        return $this;
    }

}