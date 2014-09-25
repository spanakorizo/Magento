<?php
class TM_KnowledgeBase_Block_Helpmate_Autosearch extends Mage_Core_Block_Template
{
   
    public function getAjaxAction()
    {
        return $this->getUrl('knowledgebase/index/ajax');
    }
}