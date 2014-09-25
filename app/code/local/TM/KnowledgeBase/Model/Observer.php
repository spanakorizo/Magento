<?php
class TM_KnowledgeBase_Model_Observer
{
    /**
     *
     * @param Varien_Event_Observer $observer
     * @return TM_KnowledgeBase_Model_Observer
     */
    public function beforeFaqDelete(Varien_Event_Observer $observer)
    {
        //foreign key
        $faqId = $observer->getEvent()->getDataObject()->getId();
        $collection = Mage::getModel('knowledgebase/faq_category')->getCollection()
            ->addFaqFilter($faqId);
        foreach ($collection as $row) {
            $row->delete();
        }
        return $this;
    }

    /**
     * Prepare product data for saving
     *
     * @param Varien_Object $observer
     * @return TM_KnowledgeBase_Model_Observer
     */
    public function prepareProductKnowledgeBaseData($observer)
    {
        $event   = $observer->getEvent();
        $product = $event->getProduct();
        $request = $event->getRequest();

        if (null !== ($_data = $request->getPost('knowledgebase_faq'))) {

            $data = Mage::helper('adminhtml/js')->decodeGridSerializedInput(
                $_data
            );
            $product->setData('knowledgebase_faq', $data);
        }

        return $this;
    }
}
