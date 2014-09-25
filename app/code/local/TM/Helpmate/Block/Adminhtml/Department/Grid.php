<?php

class TM_Helpmate_Block_Adminhtml_Department_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('helpmate_department_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');

        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('helpmate/department')->getCollection();

        $this->setCollection($collection);
        foreach ($collection as &$row) {
            $storeId = $row->getData('store_id');
            $row->setData('store_id', array($storeId));
        }
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
          'header'    => Mage::helper('helpmate')->__('ID'),
          'align'     => 'right',
          'width'     => '50px',
          'index'     => 'id',
          'type'      => 'number'
        ));

        $this->addColumn('created_at', array(
            'header'        => Mage::helper('helpmate')->__('Created date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
//            'filter_index'  => 'rt.created_at',
            'index'         => 'created_at',
        ));

        $this->addColumn('name', array(
          'header'    => Mage::helper('helpmate')->__('Department'),
          'align'     => 'left',
          'index'     => 'name',
        ));

        $this->addColumn('active', array(
          'header'    => Mage::helper('helpmate')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'active',
          'type'      => 'options',
          'options'   => array(
                1     => Mage::helper('helpmate')->__('Enabled'),
                0     => Mage::helper('helpmate')->__('Disabled')
            )
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('helpmate')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }

//        $this->addColumn('notified', array(
//          'header'    => Mage::helper('helpmate')->__('Notify'),
//          'align'     => 'left',
//          'width'     => '70px',
//          'index'     => 'notified',
//          'type'      => 'options',
//          'options'   => array(
//                1     => Mage::helper('helpmate')->__('Enabled'),
//                0     => Mage::helper('helpmate')->__('Disabled')
//            )
//        ));
//	$this->addColumn('email', array(
//          'header'    => Mage::helper('helpmate')->__('Email'),
//          'align'     => 'left',
//         'index'     => 'email',
//        ));

        $this->addColumn('gateway_id', array(
          'header'    => Mage::helper('helpmate')->__('Gateway'),
          'align'     => 'left',
          'width'     => '90px',
          'index'     => 'gateway_id',
          'type'      => 'options',
          'options'   => Mage::getModel('helpmate/gateway')->getOptionArray()
        ));

        $this->addColumn('action', array(
            'header'    =>  Mage::helper('helpmate')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('helpmate')->__('Edit'),
                    'url'       => array('base'=> '*/*/edit'),
                    'field'     => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('helpmate')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('helpmate')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('helpmate');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('helpmate')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('helpmate')->__('Are you sure?')
        ));

        return $this;
    }
////////////////////
//    protected function _afterLoadCollection()
//    {
//        $this->getCollection()->walk('afterLoad');
//        parent::_afterLoadCollection();
//    }
//
//    protected function _filterStoreCondition($collection, $column)
//    {
////        Zend_Debug::dump($column->getFilter()->getValue());
////        die;
//        if (!$value = $column->getFilter()->getValue()) {
//            return;
//        }
//
//        $this->getCollection()->addStoreFilter($value);
//    }
////////////////
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
