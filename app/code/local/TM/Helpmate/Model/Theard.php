<?php
class TM_Helpmate_Model_Theard extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('helpmate/theard');
    }

    public function getTicket()
    {
        return Mage::getModel('helpmate/ticket')->load(
            $this->getTicketId()
        );
    }

    public function getPrecessedText($variables = array())
    {
        $storeId = $this->getTicket()->getStoreId();

        $appEmulation = Mage::getSingleton('core/app_emulation');
        $initialEnvironmentInfo = $appEmulation->startEnvironmentEmulation(
            $storeId, Mage_Core_Model_App_Area::AREA_FRONTEND
        );

        $text = $this->getText();
        // cms filter
        $processor = Mage::helper('cms')->getBlockTemplateProcessor();
        $text = $processor->filter($text);
        
        // email filter
        $emailProcessor = Mage::getModel('core/email_template_filter')
            ->setUseAbsoluteLinks(true)
            ->setStoreId($storeId)
        ;
        if (!empty($variables)) {
            $emailProcessor
                ->setVariables($variables);
        }
        $text = $emailProcessor->filter($text);

        $appEmulation->stopEnvironmentEmulation($initialEnvironmentInfo);

        return $text;
    }
}