<?php
class Compandsave_Variable_Model_Productselectors
    extends Mage_Core_Model_Abstract
{
	const VISIBILITY_HIDDEN = '0';
    const VISIBILITY_VISIBLE = '1';
	
	const STATUS_ACTIVE = '1';
    const STATUS_INACTIVE = '0';
	
	//const DEFAULT_VARIABLE_TYPE = 'Brand';
	
    protected function _construct()
    {
       $this->_init('compandsave_variable/productselectors');
    }
	/** 
	* get variable type
	*/
    public function getCategory()
    {
        $check = array();
        $check1 = array();
        $category_model = Mage::getModel('catalog/category')->load(2);
        $subcategoryIds = $category_model->getChildren();
        $subCatIds = explode(',',$subcategoryIds);
        $collection = Mage::getResourceModel('catalog/category_collection')->addFieldToFilter('entity_id',$subCatIds)->addAttributeToSelect(array('name','entity_id'))->load();

        foreach($collection as $item){


            $check[] .= $item->entity_id;
            $check1[] .= $item->name;

        }
        //var_dump($check);
        return array_combine($check,$check1);//key, value by array combine
    }

	public function getAvailableVisibilies()
    {
        return array(
            self::VISIBILITY_VISIBLE
                => Mage::helper('compandsave_variable')
                       ->__('Visible'),
			self::VISIBILITY_HIDDEN
                => Mage::helper('compandsave_variable')
                       ->__('Hidden'),
        );
    }
	
	public function getStatus()
    {
        return array(
            self::STATUS_ACTIVE
                => Mage::helper('compandsave_variable')
                       ->__('Active'),
            self::STATUS_INACTIVE
                => Mage::helper('compandsave_variable')
                       ->__('Inactive'),
        );
    }
	protected function _beforeSave()
    {
        parent::_beforeSave();

        /**
         * Perform some actions just before a Coupon is saved.
         */
        $this->_updateTimestamps();
        return $this;
    }

    protected function _updateTimestamps()
    {
        $timestamp = now();

        /**
         * Set the last updated timestamp.
         */
        $this->setUpdatedAt($timestamp);

        /**
         * If we have a new object, set the created timestamp.
         */
        if ($this->isObjectNew()) {
            $this->setCreatedAt($timestamp);
        }
		
		
        return $this;
    }
}