<?php
/**
 * MageWorkshop_DetailedReview
 *
 * @category   MageWorkshop
 * @package    MageWorkshop_DetailedReview
 * @author     MageWorkshop <mageworkshophq@gmail.com>
 */

class MageWorkshop_DetailedReview_Block_Adminhtml_Catalog_Category_Helper_Fields_UseParent
    extends Varien_Data_Form_Element_Select
{
    /**
     * Retrieve Element HTML fragment
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = parent::getElementHtml();

        $html .= '<div><span>' . $helper = Mage::helper('detailedreview')->__("This option will be ignored for root category."). '</span></div>';
        return $html;
    }
}
