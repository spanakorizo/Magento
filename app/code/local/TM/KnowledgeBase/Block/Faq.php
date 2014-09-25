<?php
class TM_KnowledgeBase_Block_Faq extends Mage_Core_Block_Template
{
    public function  __construct()
    {
        parent::__construct();
        $identifier = $this->getRequest()->getParam('faq', null);
        $faq = Mage::getModel('knowledgebase/faq')->loadByIdentifier($identifier);
        if (!$faq->getId()) {
            return;
        }
        if (!$faq->getStatus()) {
            return;
        }
        $html = $faq->getContent();
        $processor = new Mage_Cms_Model_Template_Filter();
        $faq->setContent($processor->filter($html));
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
            return;
        }
        $head = $this->getLayout()->getBlock('head');
        if ($head && $faq) {
            $head->setTitle($faq->getTitle());
            $head->setKeywords($faq->getMetaKeywords());
            $head->setDescription($faq->getMetaDescription());
        }
        $category = current(Mage::getModel('knowledgebase/faq_category')->getCollection()
            ->addFaqFilter($faq->getId())->getData())
            ;
        $category = Mage::getModel('knowledgebase/category')->load(
            $category['category_id']
        );
        if (!$category) {
            return $this;
        }

//        die;
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');

        if ($breadcrumbs) {
//            $breadcrumbs->addCrumb('home', array('label'=>Mage::helper('cms')->__('Home'), 'title'=>Mage::helper('cms')->__('Home Page'), 'link'=>Mage::getBaseUrl()));
            $breadcrumbs->addCrumb(
                'knowledgebase_category',
                array(
                    'label' => $category->getName(),
                    'title' => $category->getName(),
                    'link'  => Mage::getUrl("knowledgebase/index/view/category/{$category->getIdentifier()}")
                )
            );

            $breadcrumbs->addCrumb(
                'knowledgebase_faq',
                array(
                    'label' => $faq->getTitle(),
                    'title' => $faq->getTitle(),
    //                'link'  => Mage::getUrl("knowledgebase/index/view/faq/{$faq->getIdentifier()}")
                )
            );
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