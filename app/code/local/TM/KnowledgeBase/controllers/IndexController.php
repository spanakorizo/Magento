<?php
class TM_KnowledgeBase_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function resultAction()
    {
        $this->_forward('index');
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    private function _sendJson(array $data = array())
    {
        @header('Content-type: application/json');
        echo json_encode($data);
        exit();
    }

    public function ajaxAction()
    {
        $query = $this->getRequest()->getParam('query', null);
        $this->getRequest()->setParam('query', $query);

        $collection = Mage::getModel('knowledgebase/faq')->getCollection()
            ->addStoreFilter()
            ->setRateOrder()
            ->addMatchSearchQuery($query);

        $category = $this->getRequest()->getParam('category', null);

        if (null !== $category) {
            $collection->addCategoryIdentifierFilter($category);
        }
        $collection->addEnableFilter()
            ->addCategoriesData()
//            ->setRateOrder()
            ;
        if (!$collection->count()) {
            $collection = Mage::getModel('knowledgebase/faq')->getCollection()
                ->addStoreFilter()
                ->setRateOrder()
                ->addSearchQuery($query);

            $category = $this->getRequest()->getParam('category', null);

            if (null !== $category) {
                $collection->addCategoryIdentifierFilter($category);
            }
            $collection->addEnableFilter()
                ->addCategoriesData();
        }
//        Zend_Debug::dump($collection->getSelect()->__toString());
        $suggestions = array();
        $processor = new Mage_Cms_Model_Template_Filter();

        $term = current(explode(
            ' ', preg_replace(array("/\r/", "/\,/", "/\./", "/\n/"), ' ', $query)
        ));
        $period = 100;
        foreach ($collection as $item) {
            $_url = Mage::getModel('core/url')->getUrl(
                'knowledgebase/index/view',
                array('faq' => $item->getIdentifier())
            );
            $_description = $item->getContent();
            $_description = $processor->filter($_description);
            $termPosition = strpos($_description, $term);
            $startTermPosition = $termPosition - $period/2;
            if ($startTermPosition < 0) {
                $startTermPosition = 0;
            }

            $_description = substr($_description, $startTermPosition, $period);
            $_description =  '<span class="ajaxsearchdescription">' .
                strip_tags($_description) .
            '</span>';

            $suggestions[] = array(
                'name'        => $item->getTitle(),
                'url'         => $_url,
//                'image'       => $_image,
                'description' => $_description
            );
        }
        $this->_sendJson(array(
			'query'       => $processor->filter($query),
            'suggestions' => $suggestions,
        ));

//        $this->getResponse()->setBody(
//            $this->getLayout()->createBlock('knowledgebase/suggest')->toHtml()
//        );
    }

    public function rateAction()
    {
        $faqId = $this->getRequest()->getParam('faq', null);
        $rate = $this->getRequest()->getParam('rate', 0);

        $key = 'is knowledgebase_faq_' . $faqId . '_rated';
        $isRated = (bool) Mage::getSingleton('customer/session')->getData($key);
        if (true === $isRated) {
            return $this->_redirectReferer();
        }
        $faq = Mage::getModel('knowledgebase/faq')->load($faqId);
        if ($faq->getId()) {
            $faq->rate++;
            $faq->save();
            Mage::getSingleton('customer/session')->setData($key, true);
        }

        $this->_redirectReferer();
    }
}