<?php

class TM_Helpmate_Block_Ticket_New extends Mage_Customer_Block_Account_Dashboard // Mage_Core_Block_Template
{
    public function getAction()
    {
        return $this->getUrl('helpmate/index/save');
    }

    public function getDepartmentHtmlSelect($defValue=null, $name='department_id', $id='department', $title='Department')
    {
        $options = Mage::getModel('helpmate/department')->getOptionArray(true);
        return $this->getLayout()->createBlock('core/html_select')
            ->setName($name)
            ->setId($id)
            ->setTitle(Mage::helper('directory')->__($title))
            ->setClass('validate-select')
            ->setValue($defValue)
            ->setOptions($options)
            ->getHtml();
    }


    public function getPriorityHtmlSelect($defValue=null, $name='priority', $id='priority', $title='Priority')
    {
        $options = Mage::getModel('helpmate/priority')->getOptionArray();
        return $this->getLayout()->createBlock('core/html_select')
            ->setName($name)
            ->setId($id)
            ->setTitle(Mage::helper('directory')->__($title))
            ->setClass('validate-select')
            ->setValue($defValue)
            ->setOptions($options)
            ->getHtml();
    }
}
