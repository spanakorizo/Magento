<?php

class TM_Helpmate_Block_Adminhtml_Dashboard_Grids extends Mage_Adminhtml_Block_Dashboard_Grids
{
    /**
     * Prepare layout for dashboard bottom tabs
     *
     * @return Mage_Adminhtml_Block_Dashboard_Grids
     */
    protected function _prepareLayout()
    {
        $this->addTab('tickets', array(
            'label'     => Mage::helper('helpmate')->__('Tickets'),
            'url'       => $this->getUrl('adminhtml/helpmate_ticket/dashboard', array('_current' => true)),
            'class'     => 'ajax'
        ));

        return parent::_prepareLayout();
    }
}
