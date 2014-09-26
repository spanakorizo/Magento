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

class MageWorkshop_DetailedReview_Block_RecentlyReviews extends Mage_Core_Block_Template
{

    protected $_collection;

    public function getProductCollection()
    {
        if (is_null($this->_collection)) {

            $this->_collection = Mage::getModel('review/review')->getProductCollection()
                ->addAttributeToSelect('url_key')
                ->addFilter("rt.status_id",array('eq' => 1))
                ->addStoreFilter()
                ->setPageSize(Mage::getStoreConfig('detailedreview/category_options/qty_items'))
                ->setOrder('rt.created_at','DESC');

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($this->_collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($this->_collection);

            if (($category = Mage::registry('current_category')) && $this->getIsPerCategory()) {
                $this->_collection->addCategoryFilter($category)
                    ->addUrlRewrite($category->getId());
            }

            if (!$this->getIsPerCategory()) {
                $this->_collection->addUrlRewrite();
            }
        }
        return $this->_collection;
    }

    protected function _beforeToHtml()
    {
        if (!Mage::getStoreConfig('detailedreview/category_options/all_reviews') && !$this->getIsPerCategory()) {
            $this->setTemplate('');
        } elseif (!Mage::getStoreConfig('detailedreview/category_options/category_reviews') && $this->getIsPerCategory()) {
            $this->setTemplate('');
        } else {
            Mage::helper('detailedreview')->applyTheme($this);
        }
        $this->getProductCollection()
            ->load()
            ->addReviewSummary();
        return parent::_beforeToHtml();
    }
}
