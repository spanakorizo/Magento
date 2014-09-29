<?php

class TM_KnowledgeBase_Block_Adminhtml_Faq_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
//        new Mage_Adminhtml_Block_Widget_Grid_Container();
        parent::__construct();
        $this->setId('knowledgebase_faq_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');

        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('knowledgebase/faq')->getCollection();
        $collection->addCategoryNamesData();

        $this->setCollection($collection);
        $return = parent::_prepareCollection();

        return $return;
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
          'header'    => Mage::helper('knowledgebase')->__('ID'),
          'align'     => 'right',
          'width'     => '50px',
          'index'     => 'id',
          'type'      => 'number'
        ));

        $this->addColumn('title', array(
          'header'    => Mage::helper('knowledgebase')->__('Title'),
          'align'     => 'left',
          'index'     => 'title',
        ));
        $authors = array();
        foreach (Mage::getModel('admin/user')->getCollection() as $user) {
            $authors[$user->user_id] = $user->username;
        }

        $this->addColumn('author', array(
          'header'    => Mage::helper('knowledgebase')->__('Author'),
          'align'     => 'left',
          'index'     => 'author',
          'type'      => 'options',
          'options'   => $authors
        ));

        $this->addColumn('categories', array(
          'header'    => Mage::helper('knowledgebase')->__('Categories'),
          'align'     => 'left',
          'index'     => 'categorynames',
          'filter_condition_callback' => array($this, '_filterCategoryNameCondition'),
        ));

        $this->addColumn('rate', array(
          'header'    => Mage::helper('knowledgebase')->__('Rate'),
          'align'     =>'right',
          'width'     => '50px',
          'type'      => 'number',
          'index'     => 'rate',
        ));

        $this->addColumn('status', array(
          'header'    => Mage::helper('knowledgebase')->__('Status'),
          'align'     => 'left',
          'width'     => '70px',
          'index'     => 'status',
          'type'      => 'options',
          'options'   => array(
                1     => Mage::helper('knowledgebase')->__('Enabled'),
                0     => Mage::helper('knowledgebase')->__('Disabled')
            )
        ));

        $this->addColumn('created_at', array(
            'header'        => Mage::helper('helpmate')->__('Created date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
//            'filter_index'  => 'rt.created_at',
            'index'         => 'created_at',
        ));

        $this->addColumn('modified_at', array(
            'header'        => Mage::helper('helpmate')->__('Modified date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
//            'filter_index'  => 'rt.created_at',
            'index'         => 'modified_at',
        ));
//
        $this->addColumn('action', array(
            'header'    =>  Mage::helper('knowledgebase')->__('Action'),
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
                array(
                    'caption'   => Mage::helper('knowledgebase')->__('Edit'),
                    'url'       => array('base'=> '*/*/edit'),
                    'field'     => 'id'
                )
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'stores',
            'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('knowledgebase')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('knowledgebase')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('knowledgebase');

        $this->getMassactionBlock()->addItem('delete', array(
             'label'    => Mage::helper('knowledgebase')->__('Delete'),
             'url'      => $this->getUrl('*/*/massDelete'),
             'confirm'  => Mage::helper('knowledgebase')->__('Are you sure?')
        ));

//        $statuses = Mage::getSingleton('knowledgebase/status')->getOptionArray();
//
//        array_unshift($statuses, array('label'=>'', 'value'=>''));
//        $this->getMassactionBlock()->addItem('status', array(
//             'label'=> Mage::helper('knowledgebase')->__('Change status'),
//             'url'  => $this->getUrl('*/*/massStatus', array('_current'=>true)),
//             'additional' => array(
//                    'visibility' => array(
//                         'name' => 'status',
//                         'type' => 'select',
//                         'class' => 'required-entry',
//                         'label' => Mage::helper('knowledgebase')->__('Status'),
//                         'values' => $statuses
//                     )
//             )
//        ));
        return $this;
    }
////////////////////
//    protected function _afterLoadCollection()
//    {
//        $this->getCollection()->walk('afterLoad');
//        parent::_afterLoadCollection();
//    }
//
    protected function _filterCategoryNameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addCategoryNameFilter($value);
    }
////////////////
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}