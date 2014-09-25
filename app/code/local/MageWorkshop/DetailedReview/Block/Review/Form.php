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

class MageWorkshop_DetailedReview_Block_Review_Form extends Mage_Review_Block_Form
{
    protected $_category;
    protected $_prosConsCollection = array();

    protected function _toHtml()
    {
        Mage::helper('detailedreview')->applyTheme($this);
        return parent::_toHtml();
    }

    public function getProsConsCollection($_entityType)
    {
        if(!array_key_exists($_entityType, $this->_prosConsCollection)) {
            if(empty($this->_category)) {
                $helper = Mage::helper('detailedreview');
                $this->_category = $helper->getOptionalCategory(null, 'use_parent_proscons_settings');
            }
            $class = MageWorkshop_DetailedReview_Model_Source_EntityType::getClassNameByType($_entityType);
            $collection = Mage::getModel('detailedreview/review_proscons')->getCollection()
                ->setType($_entityType)
                ->addFieldToFilter('status', MageWorkshop_DetailedReview_Model_Source_Common_Status::STATUS_ENABLED)
                ->addStoreFilter();
            $collection->addFieldToFilter('main_table.entity_id', array(
                'in' => explode(',', $this->_category->getData($class))
            ));
           $this->_prosConsCollection[$_entityType] = $collection;
        }
//        $class = MageWorkshop_DetailedReview_Model_Source_EntityType::getClassNameByType($_entityType);
//        if (!$collection = Mage::registry($class . '_collection')) {
//            $collection = Mage::getModel("detailedreview/review_proscons")->getCollection()->setType($_entityType);
//            $collection->setOrder('sort_order','ASC')
//                ->addFieldToFilter('status', MageWorkshop_DetailedReview_Model_Source_Common_Status::STATUS_ENABLED)
//                ->addStoreFilter();
//            Mage::register($class . '_collection', $collection);
//        }
        return $collection;
    }
}
