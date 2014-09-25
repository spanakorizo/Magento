<?php
class TM_KnowledgeBase_Block_Categories extends Mage_Core_Block_Template
{
    protected function _prepareLayout()
    {
        if (null === $this->getIdentifier()) {
            return $this;
        }
        $category = $this->getCollection()->getFirstItem();

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'knowledgebase_category',
                array(
                    'label' => $category->getName(),
                    'title' => $category->getName(),
                    'link'  => Mage::getUrl("knowledgebase/index/view/category/{$category->getIdentifier()}")
                )
            );
        }
        return $this;
    }

    public function  __construct()
    {
        parent::__construct();
        $identifier = $this->getRequest()->getParam('category', null);
        $this->setIdentifier($identifier);

        $collection = Mage::getModel('knowledgebase/category')->getCollection()
            ->addIdentifierFilter($identifier)
            ->addEnabledFilter()
            ->addStoreFilter()
            ->addFaqData()
            ;

        $this->setCollection($collection);
    }

    public function getItems()
    {
        return $this->getCollection();
    }
}