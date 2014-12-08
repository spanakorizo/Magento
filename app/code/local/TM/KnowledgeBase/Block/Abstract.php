<?php
abstract  class TM_KnowledgeBase_Block_Abstract extends Mage_Core_Block_Template
{
    public function getArticleUrl($identifier)
    {
        return $this->getUrl('knowledgebase/faq/' . $identifier);
    }

    public function getCategoryUrl($identifier)
    {
        return $this->getUrl('knowledgebase/category/' . $identifier);
    }
}