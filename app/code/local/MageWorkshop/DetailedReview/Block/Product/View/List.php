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

class MageWorkshop_DetailedReview_Block_Product_View_List extends Mage_Review_Block_Product_View_List
{
    const XML_PATH_ALLOW_VIDEO_PREVIEW = 'detailedreview/show_settings/allow_video_preview';

    protected $_reviewsCountWithoutFilters;

    public function getReviewsCollection()
    {
        if (!Mage::getStoreConfig('detailedreview/settings/enable')) {
            return parent::getReviewsCollection();
        }
        if (null === $this->_reviewsCollection) {
            $params = $this->getRequest()->getParams();
            $this->_reviewsCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addOwnershipInfo()
                ->addEntityFilter('product', $this->getProduct()->getId());

            $this->_reviewsCountWithoutFilters = $this->_reviewsCollection->getSize();
            $this->_reviewsCollection->resetTotalRecords();


            if ( isset ($params['st']) ) {
                if ($params['st'] != 0 && $params['st'] != 999 && $params['st'] != 1) {
                    $this->_reviewsCollection->addDateRangeFilter($params['st']);
                }
                if ($params['st'] == 1) {
                    $this->_reviewsCollection->addUserReviewFilter();
                }
            }

            if ( isset ($params['keywords']) ) { $this->_reviewsCollection->addKeywordsFilter($params['keywords']); }

            if ( isset ($params['vb']) ) { $this->_reviewsCollection->addVerifiedBuyersFilter(); }
            if ( isset ($params['vr']) ) { $this->_reviewsCollection->addVideoFilter(); }
            if ( isset ($params['ir']) ) { $this->_reviewsCollection->addImagesFilter(); }
            if ( isset ($params['mr']) ) { $this->_reviewsCollection->addManuResponseFilter(); }
            if ( isset ($params['hc']) ) { $this->_reviewsCollection->addHighestContributorFilter(); }

            $this->_reviewsCollection->setCustomOrder(Mage::getSingleton('detailedreview/review_sorting')->getCurrentSorting());
        }

        return $this->_reviewsCollection;
    }

    protected function _beforeToHtml()
    {
        if (!Mage::getStoreConfig('detailedreview/settings/enable')) {
            return parent::_beforeToHtml();
        }

        $this->getReviewsCollection()
            ->addHelpfulInfo();
        Mage::helper('detailedreview')->applyTheme($this);
        return parent::_beforeToHtml();
    }

    protected function _getReviewsCountWithoutFilters() {
        if(isset($this->_reviewsCountWithoutFilters))
            return $this->_reviewsCountWithoutFilters;
        else
            return 0;
    }



}
