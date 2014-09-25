<?php
class TM_KnowledgeBase_Block_Adminhtml_Product_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('knowledgebase_faq_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);

        $this->setDefaultFilter(array('used' => 1));
        $this->setSaveParametersInSession(true);
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('knowledgebase/faq')->getCollection();
        $collection->addCategoryNamesData();
        $product = Mage::registry('product');
        if ($product) {
            $collection->addUsedDataByProduct($product);
        }

        $this->setCollection($collection);
        $return = parent::_prepareCollection();

        return $return;
    }

    public function getSelectedUsedFaq()
    {
        return $this->_getProduct()->getData('knowledgebase_faq');
    }

    protected function _prepareColumns()
    {
        $this->addColumn('used', array(
            'header_css_class'  => 'a-center',
            'type'              => 'checkbox',
            'name'              => 'used',
            'values'            => array(1),
            'align'             => 'center',
            'index'             => 'used',
            'editable'          => true,
            'filter_condition_callback' => array($this, '_filterUsedCondition'),
        ));

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

    protected function _filterCategoryNameCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $collection->addCategoryNameFilter($value);
    }

    protected function _filterUsedCondition($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $collection->addUsedFilter($value);
    }
}