<?php

class TM_KnowledgeBase_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * Retrieve  url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_getUrl('knowledgebase/index/index');
    }
    
}