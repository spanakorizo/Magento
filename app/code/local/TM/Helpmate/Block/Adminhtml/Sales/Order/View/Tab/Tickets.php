<?php

/**
 * Order Shipments grid
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class TM_Helpmate_Block_Adminhtml_Sales_Order_View_Tab_Tickets
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('order_helpdesk_tickets');
        $this->setUseAjax(true);
    }

    /**
     * Retrieve collection class
     *
     * @return string
     */
    protected function _getCollectionClass()
    {
        return 'helpmate/ticket_collection';
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel($this->_getCollectionClass())
            ->addCustomerData()
            ->addOrderIdFilter($this->getOrder()->getId())
        ;
        $this->setCollection($collection);
//        Zend_Debug::dump($collection->getFirstItem()->getData());
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
//
//        $this->addColumn('number', array(
//          'header'    => Mage::helper('helpmate')->__('Number #'),
//          'align'     => 'right',
//          'width'     => '150px',
//          'index'     => 'number',
////          'type'      => 'number'
//        ));

        $this->addColumn('title', array(
          'header'    => Mage::helper('helpmate')->__('Title'),
          'align'     => 'right',
          'width'     => '150px',
          'index'     => 'title',
//          'type'      => 'number'
        ));

//        $this->addColumn('created_at', array(
//            'header'        => Mage::helper('helpmate')->__('Created date'),
//            'align'         => 'left',
//            'type'          => 'datetime',
//            'width'         => '100px',
////            'filter_index'  => 'rt.created_at',
//            'index'         => 'created_at',
//        ));

        $this->addColumn('modified_at', array(
            'header'        => Mage::helper('helpmate')->__('Modified date'),
            'align'         => 'left',
            'type'          => 'datetime',
            'width'         => '100px',
            'index'         => 'modified_at',
        ));

        $this->addColumn('name', array(
          'header'    => Mage::helper('helpmate')->__('Customer'),
          'index'     => 'customer_concat_data',
          'filter_condition_callback' => array($this, '_filterCustomerConcatDataCondition'),
        ));

        $this->addColumn('department', array(
          'header'    => Mage::helper('helpmate')->__('Department'),
          'align'     => 'left',
          'index'     => 'department_id',
          'type'      => 'options',
          'options'   => Mage::getSingleton('helpmate/department')->getOptionArray(null, null)
        ));

        return parent::_prepareColumns();
    }

    protected function _filterCustomerConcatDataCondition($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $collection->addCustomerConcatDataFilter($value);
    }

    /**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('adminhtml/helpmate_ticket/edit', array(
            'id' => $row->getId(),
            'order_id'  => $row->getOrderId()
        ));
    }

    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/helpmate_ticket/index', array('_current' => true));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('helpmate')->__('Assigned Tickets');
    }

    public function getTabTitle()
    {
        return Mage::helper('helpmate')->__('Assigned Tickets');
    }

    public function canShowTab()
    {
//        $this->_prepareCollection();
//        $collection = $this->getCollection();
//
//        if (!$collection->count()) {
//            return false;
//        }
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
