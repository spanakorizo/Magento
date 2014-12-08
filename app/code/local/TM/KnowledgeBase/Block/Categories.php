<?php
class TM_KnowledgeBase_Block_Categories extends TM_KnowledgeBase_Block_Abstract
{
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

    protected function _prepareLayout()
    {
        if (null === $this->getIdentifier()) {
            return $this;
        }
        $category = $this->getCollection()->getFirstItem();

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $url = $this->getCategoryUrl($category->getIdentifier());
            $breadcrumbs->addCrumb('knowledgebase_category', array(
                'label' => $category->getName(),
                'title' => $category->getName(),
                'link'  => $url
            ));
        }
        return $this;
    }

    public function getItems()
    {
        return $this->getCollection();
    }
}