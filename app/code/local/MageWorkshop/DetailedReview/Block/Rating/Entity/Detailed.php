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

class MageWorkshop_DetailedReview_Block_Rating_Entity_Detailed extends Mage_Core_Block_Template
{

    protected $_reviewCollections = array();
    protected $_ratingCollection;
    protected $_qtyMarks = array();
    protected $_availableSorts = array();

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('detailedreview/rating/detailed.phtml');
    }

    protected function _toHtml()
    {
        $entityId = Mage::app()->getRequest()->getParam('id');
        if (intval($entityId) <= 0) {
            return '';
        }

        $reviewsCount = Mage::getModel('review/review')
            ->getTotalReviews($entityId, true, Mage::app()->getStore()->getId());
        if ($reviewsCount == 0) {
            $this->setTemplate('detailedreview/rating/empty.phtml');
            return parent::_toHtml();
        }

        $ratingCollection = Mage::getModel('rating/rating')
            ->getResourceCollection()
            ->addEntityFilter('product')
            ->setPositionOrder()
            ->setStoreFilter(Mage::app()->getStore()->getId())
            ->addRatingPerStoreName(Mage::app()->getStore()->getId())
            ->load();

        if ($entityId) {
            $ratingCollection->addEntitySummaryToItem($entityId, Mage::app()->getStore()->getId());
        }
        $this->calculateSummary();
        $this->_ratingCollection = $ratingCollection;
        $this->assign('collection', $ratingCollection);
        Mage::helper('detailedreview')->applyTheme($this);
        return parent::_toHtml();
    }

    public function calculateSummary()
    {
        $summary = $sum = 0;
        foreach ($this->getQtyMarks() as $key => $value) {
            if (!$key) continue;
            $summary += $key * $value * 20;
            $sum += $value;
        }
        if($sum) {
            $this->setSummary(round($summary / $sum))
                ->setCountReviewsWithRating($sum);
        }
    }

    public function getQtyMarks($range = 0)
    {
        if (!isset($this->_qtyMarks[$range])) {
            $reviewsIds = array();
            foreach ($this->getReviewCollection($range) as $review) {
                $reviewsIds[] = $review->getId();
            }
            $this->_qtyMarks[$range] = Mage::getModel('detailedreview/rating_option_vote')->getQtyMarks($reviewsIds);
        }
        return $this->_qtyMarks[$range];
    }
    
    public function getQtyByRange($range = 0) {
        return $this->getReviewCollection($range)->count();
    }

    public function getAverageSizing()
    {
        return $this->getReviewCollection()->getAverageSizing();
    }

    public function getReviewCollection($range = 0)
    {
        $params = Mage::app()->getRequest()->getParams();
        $range = ($range != 0) ? $range : ((isset($params['st'])) ? $params['st'] : 0);
        if (!isset($this->_reviewCollections[$range])) {

            $reviewCollection = Mage::getModel('review/review')->getCollection()
                ->addStoreFilter(Mage::app()->getStore()->getId())
                ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
                ->addEntityFilter('product', $this->getCurrentProduct()->getId());

            if ($range != 0 && $range != 999 && $range != 1) {
                $reviewCollection->addDateRangeFilter($range);
            }
            if($range == 1) {
                $reviewCollection->addUserReviewFilter();
            }
            if (isset($params['keywords'])) {
                $reviewCollection->addKeywordsFilter($params['keywords']);
            }
            if (isset($params['vb'])) {
                $reviewCollection->addVerifiedBuyersFilter();
            }
            if (isset($params['vr'])) {
                $reviewCollection->addVideoFilter();
            }
            if (isset($params['ir'])) {
                $reviewCollection->addImagesFilter();
            }
            if (isset($params['mr'])) {
                $reviewCollection->addManuResponseFilter();
            }
            if (isset($params['hc'])) {
                $reviewCollection->addHighestContributorFilter();
            }

            if (isset($params['sort'])) {
                $reviewCollection->setCustomOrder($params['sort']);
            } else {
                $reviewCollection->setCustomOrder();
            }
            $this->_reviewCollections[$range] = $reviewCollection;
        }
        return $this->_reviewCollections[$range];
    }

    public function getCurrentProduct()
    {
        return Mage::registry('current_product');
    }

    public function getAvailableSorts($ratingsEnabled)
    {
        $options = Mage::getSingleton('detailedreview/review_sorting')->getAvailableOptions();
        if (!$ratingsEnabled){
            unset($options['rate_desc']);
            unset($options['rate_asc']);
        }
        return $options;
    }

    public function getCurrentSorting()
    {
        return Mage::getSingleton('detailedreview/review_sorting')->getCurrentSorting();
    }

    public function getAvailableFilterAtts()
    {
        $helper = Mage::helper('detailedreview');
        $availableFilterAtts = array(
            'vb' => $helper->__('Verified Buyers')
        );

        $helperDetailedreview = Mage::helper('detailedreview');
        if ($helperDetailedreview->checkFieldAvailable('image')) {
            $availableFilterAtts['ir'] = $helper->__('Reviews with Images');
        }
        if ($helperDetailedreview->checkFieldAvailable('video')) {
            $availableFilterAtts['vr'] = $helper->__('Reviews with Video');
        }
        if ($helperDetailedreview->checkFieldAvailable('response')) {
            $availableFilterAtts['mr'] = $helper->__('Administration Response');
        }
        $availableFilterAtts['hc'] = $helper->__('Highest Contributors');

        return $availableFilterAtts;
    }


    public function getAvailableDateRanges()
    {
        $helper = Mage::helper('detailedreview');
        return array(
            1 => $helper->__('My Reviews'),
            2 => $helper->__('Last Week'),
            3 => $helper->__('Last 4 Weeks'),
            4 => $helper->__('Last 6 Months'),
            999 => $helper->__('All Reviews')
        );
    }

    public function getClearFiltersUrl(){
        $coreUrl = $this->helper('core/url');
        $url = preg_replace('/\?.*/', '', $coreUrl->getCurrentUrl());
        $params = $_GET;
        $filters = array('st', 'vb', 'ir', 'vr', 'hc', 'mr', 'keywords');
        foreach ($params as $key => $value){
            if (in_array($key, $filters)) {
                unset($params[$key]);
            }
        }
        $params['feedback'] = 1;
        $url = Mage::helper('detailedreview')->addRequestParam($url, $params);
        return $url;
    }
}
