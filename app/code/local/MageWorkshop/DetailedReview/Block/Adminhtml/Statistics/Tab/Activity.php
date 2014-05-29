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

class MageWorkshop_DetailedReview_Block_Adminhtml_Statistics_Tab_Activity extends Mage_Adminhtml_Block_Dashboard_Graph
{
    /**
     * Initialize object
     *
     * @return void
     */
    public function __construct()
    {
        $this->setHtmlId('activity');
        parent::__construct();
        $this->setTemplate('detailedreview/graph.phtml');
    }

    public function setWidth($width){
        $this->_width = $width;
    }

    /**
     * Prepare chart data
     *
     * @return void
     */
    protected function _prepareData()
    {
        $this->setDataHelperName('detailedreview/adminhtml_statistics_activity');
        $this->getDataHelper()->setParam('store', $this->getRequest()->getParam('store'));
        $this->getDataHelper()->setParam('website', $this->getRequest()->getParam('website'));
        $this->getDataHelper()->setParam('group', $this->getRequest()->getParam('group'));

        $this->setDataRows('quantity');
        $this->_axisMaps = array(
            'x' => 'range',
            'y' => 'quantity'
        );

        parent::_prepareData();
    }

    public function getDataForRows(){

        $this->_allSeries = $this->getRowsData($this->_dataRows);

        foreach ($this->_axisMaps as $axis => $attr){
            $this->setAxisLabels($axis, $this->getRowsData($attr, true));
        }

        $timezoneLocal = Mage::app()->getLocale()->getTimezone();
//        $timezoneLocal = Mage::app()->getStore()->getConfig(Mage_Core_Model_Locale::XML_PATH_DEFAULT_TIMEZONE);

        list ($dateStart, $dateEnd) = Mage::getResourceModel('reports/order_collection')
            ->getDateRange($this->getDataHelper()->getParam('period'), '', '', true);

        $dateStart->setTimezone($timezoneLocal);
        $dateEnd->setTimezone($timezoneLocal);

        $dates = array();
        $datas = array();

        while($dateStart->compare($dateEnd) < 0){
            switch ($this->getDataHelper()->getParam('period')) {
                case '24h':
                    $d = $dateStart->toString('yyyy-MM-dd HH:00');
                    $dateStart->addHour(1);
                    break;
                case '7d':
                case '1m':
                    $d = $dateStart->toString('yyyy-MM-dd');
                    $dateStart->addDay(1);
                    break;
                case '1y':
                case '2y':
                    $d = $dateStart->toString('yyyy-MM');
                    $dateStart->addMonth(1);
                    break;
            }
            foreach ($this->getAllSeries() as $index=>$serie) {
                if (in_array($d, $this->_axisLabels['x'])) {
                    $datas[$index][] = (float)array_shift($this->_allSeries[$index]);
                } else {
                    $datas[$index][] = 0;
                }
            }
            $dates[] = $d;
        }

        $result = array();
        foreach ($dates as $k => $d) {
            if ($d != '') {
                switch ($this->getDataHelper()->getParam('period')) {
                    case '24h':
                        $result[$k]['date'] = $this->formatTime(
                            new Zend_Date($d, 'yyyy-MM-dd HH:00'), 'short', false
                        );
                        break;
                    case '7d':
                    case '1m':
                    $result[$k]['date'] = $this->formatDate(
                            new Zend_Date($d, 'yyyy-MM-dd')
                        );
                        break;
                    case '1y':
                    case '2y':
                        $formats = Mage::app()->getLocale()->getTranslationList('datetime');
                        $format = isset($formats['yyMM']) ? $formats['yyMM'] : 'MM/yyyy';
                        $format = str_replace(array("yyyy", "yy", "MM"), array("Y", "y", "m"), $format);
                        $result[$k]['date'] = date($format, strtotime($d));
                        break;
                }
            } else {
                $result[$k]['date'] = '';
            }
            $result[$k]['data'] = $datas[$index][$k];
        }

        return $result;
    }
}
