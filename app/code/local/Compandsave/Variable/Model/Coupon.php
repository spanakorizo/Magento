<?php
class Compandsave_Variable_Model_Coupon
    extends Mage_Core_Model_Abstract
{
	const VISIBILITY_HIDDEN = '0';
    const VISIBILITY_VISIBLE = '1';
	
	const STATUS_ACTIVE = '1';
    const STATUS_INACTIVE = '0';
	
	const DEFAULT_VARIABLE_TYPE = 'Coupon';
	
    protected function _construct()
    {
       $this->_init('compandsave_variable/coupon');
    }
	/** 
	* get variable type
	*/
	
	public function getType()
	{
		return array(
		self::DEFAULT_VARIABLE_TYPE
                => Mage::helper('compandsave_variable')
                       ->__('Coupon'),
		
		);
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