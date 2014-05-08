<?php
class MageWorkshop_DetailedReview_Block_Adminhtml_Renderer_MultiImage
    extends Mage_Adminhtml_Block_Template implements Varien_Data_Form_Element_Renderer_Interface
{

    protected function _construct()
    {
        $this->setTemplate('detailedreview/multiImage.phtml');
    }

    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->assign(array(
            'element' => $element
        ));
        $html = $this->toHtml() . $element->getAfterElementHtml();
        return $html;
    }


}