<?php
/**
 * MageWorkshop_DetailedReview
 *
 * @category   MageWorkshop
 * @package    MageWorkshop_DetailedReview
 * @author     MageWorkshop <mageworkshophq@gmail.com>
 */

class MageWorkshop_DetailedReview_Block_Adminhtml_Catalog_Category_Helper_Fields_Available
    extends Varien_Data_Form_Element_Multiselect
{
    /**
     * Retrieve Element HTML fragment
     *
     * @return string
     */
    public function getElementHtml()
    {
        $disabled = false;
        if (!$this->getValue()) {
            $this->setData('disabled', 'disabled');
            $disabled = true;
        }
        $html = parent::getElementHtml();
        $htmlId = 'use_config_' . $this->getHtmlId();
        $html .= '<input id="'.$htmlId.'" name="use_config[]" value="' . $this->getId() . '"';
        $html .= ($disabled ? ' checked="checked"' : '');

        if ($this->getReadonly()) {
            $html .= ' disabled="disabled"';
        }

        $html .= ' onclick="toggleValueElements(this, this.parentNode);" class="checkbox" type="checkbox" />';

        $html .= ' <label for="'.$htmlId.'" class="normal">'
            . Mage::helper('detailedreview')->__('Use Parent Category Settings').'</label>';
        $html .= '<script type="text/javascript">toggleValueElements($(\''.$htmlId.'\'), $(\''.$htmlId.'\').parentNode);</script>';

        return $html;
    }
}
