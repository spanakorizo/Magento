<?php
class TM_KnowledgeBase_Block_Faq extends TM_KnowledgeBase_Block_Abstract
{
    public function  __construct()
    {
        $params = $this->getRequest()->getParams();
        parent::__construct();

        $_identifier = $this->getRequest()->getParam('category', null);
        if (null !== $_identifier) {
            $category = Mage::getModel('knowledgebase/category')->getCollection()
                ->addIdentifierFilter($_identifier)
                ->addEnabledFilter()
                ->addStoreFilter()
                ->getFirstItem()
                ;
            if ($category) {
                $this->setCategory($category);
            }
        }

        $identifier = $this->getRequest()->getParam('faq', null);
        $faq = Mage::getModel('knowledgebase/faq')->loadByIdentifier($identifier);

        if (!$faq->getId()) {
            return;
        }
        if (!$faq->getStatus()) {
            return;
        }
//        $html = $faq->getContent();
//        $processor = new Mage_Cms_Model_Template_Filter();
//        $faq->setContent($processor->filter($html));
        $faq->setData(
            'author_data', Mage::getModel('admin/user')->load($faq->getAuthor())
        );
        // add stores
        $stores = $faq->getStores();
        $storeId = Mage::app()->getStore()->getId();
        if (!in_array(0, $stores) && !in_array($storeId, $stores)) {
            return;
        }

        $this->setFaq($faq);
    }

    protected function _prepareLayout()
    {
        $faq = $this->getFaq();

        if (!$faq || !$faq->getId()) {
            $category = $this->getCategory();
            $head = $this->getLayout()->getBlock('head');

            if ($head && $category) {
                $head->setTitle($category->getName());
//                $head->setKeywords($category->getMetaKeywords());
//                $head->setDescription($category->getMetaDescription());
            }
            return;
        }

        $head = $this->getLayout()->getBlock('head');
        if ($head && $faq) {
            $head->setTitle($faq->getTitle());
            $head->setKeywords($faq->getMetaKeywords());
            $head->setDescription($faq->getMetaDescription());
        }

        $_categoryId = Mage::getModel('knowledgebase/faq_category')
            ->getCollection()
            ->addFaqFilter($faq->getId())
            ->getFirstItem()
            ->getCategoryId()
            ;

        $category = Mage::getModel('knowledgebase/category')->load($_categoryId);
        if (!$category) {
            return $this;
        }

        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $_url = $this->getCategoryUrl($category->getIdentifier());

            $breadcrumbs->addCrumb('knowledgebase_category', array(
                'label' => $category->getName(),
                'title' => $category->getName(),
                'link'  => $_url
            ));

            $breadcrumbs->addCrumb('knowledgebase_faq', array(
                'label' => $faq->getTitle(),
                'title' => $faq->getTitle(),
            ));
        }

        return $this;

    }

    public function getRateAction()
    {
        return $this->getUrl('*/*/rate');
    }

    public function isRated()
    {
        $faqId = $this->getFaq()->getId();
        $key = 'is knowledgebase_faq_' . $faqId . '_rated';
        return (bool) Mage::getSingleton('customer/session')->getData($key);
    }
}