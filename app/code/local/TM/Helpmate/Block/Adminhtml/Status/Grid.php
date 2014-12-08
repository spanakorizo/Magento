<?php

class TM_Helpmate_Block_Adminhtml_Status_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('helpmate_status_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');

        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('helpmate/status')->getCollection();

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
          'header'    => Mage::helper('helpmate')->__('Name'),
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

        $this->addColumn('sort_order', array(
          'header'    => Mage::helper('helpmate')->__('Sort Order'),
          'align'     => 'left',
          'index'     => 'sort_order',
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

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}