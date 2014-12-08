<?php
class TM_KnowledgeBase_Block_Helpmate_Autosearch extends TM_KnowledgeBase_Block_Abstract
{

    public function getAjaxAction()
    {
        return $this->getUrl('knowledgebase/index/ajax');
    }
}