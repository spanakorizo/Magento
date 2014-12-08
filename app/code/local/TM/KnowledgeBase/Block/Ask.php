<?php
class TM_KnowledgeBase_Block_Ask extends TM_KnowledgeBase_Block_Abstract
{

    public function _prepareLayout()
    {
        $breadcrumbs = $this->getLayout()->getBlock('breadcrumbs');
        if ($breadcrumbs) {
            $breadcrumbs->addCrumb(
                'home', array(
                    'label' => Mage::helper('cms')->__('Home'),
                    'title' => Mage::helper('cms')->__('Home Page'),
                    'link' => Mage::getBaseUrl()
                )
            );
            $breadcrumbs->addCrumb(
                'knowledgebase_home',
                array('label' => Mage::helper('knowledgebase')->__('Knowledge Base'),
                    'title'   => Mage::helper('knowledgebase')->__('Knowledge Base'),
                    'link'    => Mage::getUrl("knowledgebase/index/index")
                )
            );
        }

        return parent::_prepareLayout();
    }

    protected function _beforeToHtml()
    {
        $identifier = $this->getRequest()->getParam('category', null);
        $this->setCategory($identifier);
    }

    public function getQuery()
    {
        if (null !== ($query = Mage::registry('knowledgebase_query'))) {
            return $query;
        }
    }

    public function getAction()
    {
        return $this->getUrl('knowledgebase/index/result');
    }

    public function getAjaxAction()
    {
        return $this->getUrl('knowledgebase/index/ajax');
    }
}
