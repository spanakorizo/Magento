<?php
/**
 * MageWorkshop
 * Copyright (C) 2012  MageWorkshop <mageworkshophq@gmail.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://opensource.org/licenses/gpl-3.0.html.
 *
 * @category   MageWorkshop
 * @package    MageWorkshop_DetailedReview
 * @copyright  Copyright (c) 2012 MageWorkshop Co. (http://mage-workshop.com)
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License, version 3 (GPL-3.0)
 * @author     MageWorkshop <mageworkshophq@gmail.com>
 */

class MageWorkshop_DetailedReview_Model_Mysql4_Review_Proscons extends Mage_Core_Model_Mysql4_Abstract
{
    protected $_entityType;
    protected $_entityName;
    protected $_className;

    protected function _construct()
    {
        $this->_entityName = MageWorkshop_DetailedReview_Model_Source_EntityType::getEntityNameByType($this->_entityType);
        $this->_className = MageWorkshop_DetailedReview_Model_Source_EntityType::getClassNameByType($this->_entityType);
        $this->_init('detailedreview/review_proscons', 'entity_id');
    }

    public function loadStoreIds($object)
    {
        $storeIds = array();
        if ($object->getId()) {
            $storeIds = $this->lookupStoreIds($object->getId(), $object->getEntityType());
        }
        $object->setStoreIds($storeIds);
    }

    /**
     * Get store ids to which specified item is assigned
     *
     * @param int $id
     * @return array
     */
    public function lookupStoreIds($id, $entityType)
    {
        return $this->_getReadAdapter()->fetchCol($this->_getReadAdapter()->select()
                ->from($this->getTable('detailedreview/review_proscons_store'), 'store_id')
                ->where("entity_id = ?", $id)
                ->where("entity_type = ?", $entityType)
        );
    }


    public function _afterSave(Mage_Core_Model_Abstract $object)
    {
        /** stores */
        if($storeIds = $object->getStoreIds()) {
            $deleteWhere = array();
            $deleteWhere[] = $this->_getWriteAdapter()->quoteInto('entity_id = ?', $object->getEntityId());
            $deleteWhere[] = $this->_getWriteAdapter()->quoteInto('entity_type = ?', $object->getEntityType(), 'string');

            $this->_getWriteAdapter()->delete($this->getTable('detailedreview/review_proscons_store'), $deleteWhere);

            if(is_array($storeIds)) {
                foreach ($storeIds as $storeId) {
                    $this->_addStore($object, $storeId);
                }
            } else {
                $this->_addStore($object, $storeIds);
            }
        }
        return $this;
    }

    /**
     * Insert store info for the object
     *
     * @param Mage_Core_Model_Abstract $object
     * @param int $storeId
     */
    protected function _addStore(Mage_Core_Model_Abstract $object, $storeId) {
        $pollStoreData = array(
            'entity_id'     => $object->getEntityId(),
            'entity_type'   => $object->getEntityType(),
            'store_id'      => $storeId
        );
        $this->_getWriteAdapter()->insert($this->getTable('detailedreview/review_proscons_store'), $pollStoreData);
    }

    /**
     * Perform actions after object delete
     *
     * @param Varien_Object $object
     */
    protected function _afterDelete(Mage_Core_Model_Abstract $object)
    {
        $this->_getWriteAdapter()
            ->delete($this->getTable('detailedreview/review_proscons_store'), array(
            'entity_id=?' => $object->getEntityId(),
            'entity_type=?' => $object->getEntityType()
        ));
        return parent::_afterDelete($object);
    }

    public function setType($type){
        $this->_entityType = $type;
        $this->_entityName = MageWorkshop_DetailedReview_Model_Source_EntityType::getEntityNameByType($this->_entityType);
        $this->_className = MageWorkshop_DetailedReview_Model_Source_EntityType::getClassNameByType($this->_entityType);
        return $this;
    }
}
