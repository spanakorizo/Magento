<?php

class TM_Helpmate_Block_Adminhtml_Gateway_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('helpmate_gateway_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');

        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('helpmate/gateway')->getCollection();

        $this->setCollection($collection);
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

        $this->addColumn('name', array(
          'header'    => Mage::helper('helpmate')->__('Title'),
          'align'     => 'left',
          'index'     => 'name',
        ));

        $this->addColumn('status', array(
          'header'    => Mage::helper('helpmate')->__('Status'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
                1     => Mage::helper('helpmate')->__('Enabled'),
                0     => Mage::helper('helpmate')->__('Disabled')
            )
        ));

        $this->addColumn('type', array(
          'header'    => Mage::helper('helpmate')->__('Type'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'type',
          'type'      => 'options',
          'options'   => array(
                1     => Mage::helper('helpmate')->__('Pop3'),
                2     => Mage::helper('helpmate')->__('Imap')
            )
        ));
        
        $this->addColumn('email', array(
          'header'    => Mage::helper('helpmate')->__('Email'),
          'align'     => 'left',
          'index'     => 'email',
        ));

        $this->addColumn('host', array(
          'header'    => Mage::helper('helpmate')->__('Host'),
          'align'     => 'left',
          'index'     => 'host',
        ));

        $this->addColumn('user', array(
          'header'    => Mage::helper('helpmate')->__('User'),
          'align'     => 'left',
          'index'     => 'user',
        ));

        $this->addColumn('port', array(
          'header'    => Mage::helper('helpmate')->__('Port'),
          'align'     => 'left',
          'index'     => 'port',
        ));

        $this->addColumn('secure', array(
          'header'    => Mage::helper('helpmate')->__('Secure'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'secure',
          'type'      => 'options',
          'options'   => array(
                0     => Mage::helper('helpmate')->__('None'),
                1     => Mage::helper('helpmate')->__('SSL/TLS'),
                2     => Mage::helper('helpmate')->__('STARTTLS')
            )
        ));

        /*
        $this->addColumn('remove', array(
          'header'    => Mage::helper('helpmate')->__('Remove'),
          'align'     => 'left',
          'width'     => '80px',
          'index'     => 'remove',
          'type'      => 'options',
          'options'   => array(
                1     => Mage::helper('helpmate')->__('Enabled'),
                0     => Mage::helper('helpmate')->__('Disabled')
            )
        ));
        */
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