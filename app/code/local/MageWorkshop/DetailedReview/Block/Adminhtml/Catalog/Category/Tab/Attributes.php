<?php

class MageWorkshop_DetailedReview_Block_Adminhtml_Catalog_Category_Tab_Attributes extends Mage_Adminhtml_Block_Catalog_Category_Tab_Attributes {

    protected function _prepareForm()
    {
        parent::_prepareForm();
        if ($element = $this->getForm()->getElement('use_parent_proscons_settings')) {
            $element->setData('onchange', 'onUseParentChangedHandler(this)');
            $element->setData('class', 'use_parent_proscons_settings');
        }
        return $this;
    }

    protected function _toHtml()
    {
        $html = parent::_toHtml();
        if($this->getForm()->getElement('use_parent_review_settings')) {
            $html .= "<script type='text/javascript'>
                         onUseParentChangedHandler($$('.use_parent_proscons_settings').first());
                      </script>";
        }
        return $html;
    }

}