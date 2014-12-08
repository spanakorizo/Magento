<?php

/**
 * Adminhtml dashboard most active buyers
 *
 * @category   TM
 * @package    TM_Helpmate
 */

class TM_Helpmate_Block_Adminhtml_Dashboard_Tab_Ticket extends Mage_Adminhtml_Block_Dashboard_Grid
{

   public function __construct()
    {
        parent::__construct();
        $this->setId('helpmate_ticket_grid');
        $this->setDefaultSort('modified_at');
        $this->setDefaultDir('DESC');

        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('helpmate/ticket')->getCollection();
        $collection->addCustomerData();

        $this->setCollection($collection);
        $return = parent::_prepareCollection();

        foreach ($collection as &$row) {
            $storeId = $row->getData('store_id');
            $row->setData('store_id', array($storeId));
        }
        return $return;
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

        $this->addColumn('text', array(
          'header'    => Mage::helper('helpmate')->__('Title'),
          'align'     => 'left',
          'index'     => 'title',
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

        $this->addColumn('department', array(
          'header'    => Mage::helper('helpmate')->__('Department'),
          'align'     => 'left',
          'index'     => 'department_id',
          'type'      => 'options',
          'options'   => Mage::getSingleton('helpmate/department')->getOptionArray(null, null)
        ));


        $users = array();

        foreach (Mage::getModel('admin/user')->getCollection() as $user) {
            $users[$user->user_id] = $user->username;
        }
        $this->addColumn('user_id', array(
          'header'    => Mage::helper('helpmate')->__('Assigned'),
          'align'     => 'left',
          'index'     => 'user_id',
          'type'      => 'options',
          'options'   => $users
        ));


        $this->addColumn('priority', array(
            'header'         => Mage::helper('helpmate')->__('Priority'),
            'align'          => 'left',
            'width'          => '80px',
            'index'          => 'priority',
            'type'           => 'options',
            'options'        => Mage::getSingleton('helpmate/priority')->getOptionArray(),
            'frame_callback' => array($this, 'decorateStatus')
        ));

        $this->addColumn('status', array(
            'header'  => Mage::helper('helpmate')->__('Status'),
            'align'   => 'left',
            'width'   => '80px',
            'index'   => 'status',
            'type'    => 'options',
            'options' => Mage::getSingleton('helpmate/status')->getOptionArray()
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

        return parent::_prepareColumns();
    }

    protected function _filterCustomerConcatDataCondition($collection, $column)
    {
        $value = $column->getFilter()->getValue();
        $collection->addCustomerConcatDataFilter($value);
    }

    /**
     * Decorate status column values
     *
     * @return string
     */
    public function decorateStatus($value, $row, $column, $isExport)
    {
        Mage::getSingleton('helpmate/status')->getOptionArray();
//        Zend_Debug::dump($row);die;
        switch ($row->priority) {
            case TM_Helpmate_Model_Priority::PRIORITY_EMERGENCY:
            case TM_Helpmate_Model_Priority::PRIORITY_CRITICAL:
                $cell = '<span class="grid-severity-critical"><span>'.$value.'</span></span>';
                break;
            case TM_Helpmate_Model_Priority::PRIORITY_HIGHT:
                $cell = '<span class="grid-severity-major"><span>'.$value.'</span></span>';
                break;
            case TM_Helpmate_Model_Priority::PRIORITY_MEDIUM:
                $cell = '<span class="grid-severity-minor"><span>'.$value.'</span></span>';
                break;
            case TM_Helpmate_Model_Priority::PRIORITY_LOW:
            default:
                $cell = '<span class="grid-severity-notice"><span>'.$value.'</span></span>';
                break;
        }

        return $cell;
    }


////////////////////
//    protected function _afterLoadCollection()
//    {
//        $this->getCollection()->walk('afterLoad');
//        parent::_afterLoadCollection();
//    }
//
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
////////////////
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
