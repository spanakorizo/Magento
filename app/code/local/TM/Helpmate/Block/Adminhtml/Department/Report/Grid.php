<?php


class TM_Helpmate_Block_Adminhtml_Department_Report_Grid extends Mage_Adminhtml_Block_Report_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('departmentReportGrid');
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $this->getCollection()
            ->initReport('helpmate/report_collection')
        ;

//        $this->getCollection()->load();

//        $this->_collection = Mage::getModel('helpmate/department')->getCollection()
//            ->addReport()
//        ;
//
//        $this->setCollection($this->_collection);
//
//        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('department_id', array(
            'header'    => $this->__('Department'),
            'sortable'  => false,
            'index'     => 'department_id',
            'type'      => 'options',
            'options'   => Mage::getModel('helpmate/department')->getOptionArray()
//            'renderer'  => 'adminhtml/dashboard_searches_renderer_searchquery',
        ));

//        $this->addColumn('gateway_id', array(
//            'header'    => Mage::helper('helpmate')->__('Gateway'),
//            'align'     => 'left',
//            'width'     => '90px',
//            'index'     => 'gateway_id',
//            'type'      => 'options',
//            'options'   => Mage::getModel('tm_email/gateway_storage')->getOptionArray()
//        ));

        $this->addColumn('ticket_count', array(
            'header'    => $this->__('Total Tickets'),
            'sortable'  => false,
            'index'     => 'ticket_count',
            'type'      => 'number',
            'total'     => 'sum',
        ));

        $statusses = Mage::getModel('helpmate/status')->getOptionArray();
        foreach ($statusses as $status) {
            $_index = 'ticket_' . strtolower($status) . '_count';
            $this->addColumn($_index, array(
                'header'    => $this->__($status),
                'sortable'  => false,
                'index'     => $_index,
                'type'      => 'number',
                'total'     => 'sum',
            ));
        }

        $this->setFilterVisibility(false);
        $this->setPagerVisibility(false);

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
