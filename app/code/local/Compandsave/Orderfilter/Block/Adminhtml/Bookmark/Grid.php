<?php
class Compandsave_OrderFilter_Block_Adminhtml_Bookmark_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('compandsave_orderfilter_list');
        $this->setDefaultSort('id');
        /*
        * Override method getGridUrl() in this class to provide URL for AJAX
        */
        $this->setUseAjax(true);
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('compandsave_orderfilter/bookmark_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => $this->__('ID'),
            'sortable' => true,
            'width' => '60px',
            'index' => 'entity_id'
        ));
        $this->addColumn('name', array(
            'header' => $this->__('Name'),
            'index' => 'name',
            'column_css_class' => 'name'
        ));
        $this->addColumn('action', array(
            'header' => $this->__('Action'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => $this->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id',
                ),
                array(
                    'caption' => $this->__('Delete'),
                    'url' => array('base' => '*/*/delete'),
                    'field' => 'id',
                ),
            ),
            'filter' => false,
            'sortable' => false,
        ));
        return parent::_prepareColumns();
    }
}