<?php
class Compandsave_Variable_Block_Adminhtml_Productselectors_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    protected function _prepareCollection()
    {
        /**
         * Tell Magento which collection to use to display in the grid.
         */
        $collection = Mage::getResourceModel(
            'compandsave_variable/productselectors_collection'
        );
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    public function getRowUrl($row)
    {
        /**
         * When a grid row is clicked, this is where the user should
         * be redirected to, the method editAction of Adminhtml
         * CouponController.php in BrandDirectory module.
         */
        return $this->getUrl('compandsave_variable_admin/productselectors/edit',array('id' => $row->getId()));
    }

    protected function _prepareColumns()
    {
        /**
         * Here, we'll define which columns to display in the grid.
         */
		$ProductselectorsSingleton = Mage::getSingleton('compandsave_variable/productselectors');
		
        $this->addColumn('entity_id', array(
            'header' => $this->_getHelper()->__('ID'),
            'type' => 'number',
            'index' => 'entity_id',
        ));
		$this->addColumn('brand_id', array(
            'header' => $this->_getHelper()->__('Product Selectors ID'),
            'type' => 'options',
            'index' => 'brand_id',
			'options' => $ProductselectorsSingleton->getCategory()
        ));
		
		$this->addColumn('value', array(
			'header' => $this->_getHelper()->__('Value'),
			'type' => 'textarea',
			'index' => 'value',
		));
		$this->addColumn('created_at', array(
            'header' => $this->_getHelper()->__('Created'),
            'type' => 'datetime',
            'index' => 'created_at',
        ));

        $this->addColumn('updated_at', array(
            'header' => $this->_getHelper()->__('Updated'),
            'type' => 'datetime',
            'index' => 'updated_at',
        ));

        $this->addColumn('visibility', array(
            'header' => $this->_getHelper()->__('Visibility'),
            'type' => 'options',
            'index' => 'visibility',
            'options' => $ProductselectorsSingleton->getAvailableVisibilies()
        ));
		$this->addColumn('status', array(
            'header' => $this->_getHelper()->__('Status'),
            'type' => 'options',
            'index' => 'status',
            'options' => $ProductselectorsSingleton->getStatus()
        ));

        /**
         * Finally, we'll add an action column with an edit link.
         */
        $this->addColumn('action', array(
            'header' => $this->_getHelper()->__('Action'),
            'width' => '50px',
            'type' => 'action',
            'actions' => array(
                array(
                    'caption' => $this->_getHelper()->__('Edit'),
                    'url' => array(
                        'base' => 'compandsave_variable_admin'
                                  . '/productselectors/edit',
                    ),
                    'field' => 'id'
                ),
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'entity_id',
        ));

        return parent::_prepareColumns();
    }

    protected function _getHelper()
    {
        return Mage::helper('compandsave_variable');
    }
}