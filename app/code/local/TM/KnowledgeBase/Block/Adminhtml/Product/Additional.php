<?php
class TM_KnowledgeBase_Block_Adminhtml_Product_Additional extends Mage_Adminhtml_Block_Widget
{
    protected function _prepareLayout()
    {
        $product = Mage::registry('product');
        if ($product->getId()) {
            $this->getLayout()
                ->getBlock('product_tabs')
                ->addTab('knowledgebase_faq', array(
                    'label' => Mage::helper('knowledgebase')->__('Knowledgebase'),
                    'url'   => $this->getUrl('knowledgebase_admin/adminhtml_faq/product', array('_current' => true)),
                    'class' => 'ajax'
                ));
        }

        return parent::_prepareLayout();
    }

}