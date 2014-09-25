<?php

class Nextopia_Search_Model_EcommResponse extends Varien_Object
{
    private $_dataForResults = null;
    private $_usedQueryString = '';
	public function setDataForResults($data) {
		$this->_dataForResults = $data;
	}
	public function setUsedQueryString($qs) {
		$this->_usedQueryString = $qs;
	}
	public function getUsedQueryString() {
		return $this->_usedQueryString;
	}
	public function getDataForResults() {
		return $this->_dataForResults;
	}
	public function getResultsCount() {
		$count = 0;
		if (!is_null($this->_dataForResults)) {
			$pagination = $this->_dataForResults->pagination;
			$count = intval($pagination->total_products);
		}
		return $count;
	}
    public function getNotices() {

        $notices = array();
        $notices_node = $this->_dataForResults->notices;
        if ($notices_node->related_added) {
            $related = (string) $notices_node->related_added;
            if (!empty($related) && $related != "1") {
                $notices[] = $related;
            }
        }
        if ($notices_node->sku_match) {
            $sku_match = (string) $notices_node->sku_match;
            if (!empty($sku_match) && $sku_match != "1") {
                $notices[] = $sku_match;
            }
        }
        if ($notices_node->or_switch) {
            $or_switch = (string) $notices_node->or_switch;
            if (!empty($or_switch)) {
                $notices[] = 'There were no products that contained <b>all</b> of the words you searched for. The below results contain <b>some</b> of the words.';
            }
        }
        return ($notices);
    }
	public function getFirstProductUrl() {
		$url = '';
		$products = $this->_dataForResults->results;
		$product = current($products);
		$url = (string) $product->Url;
		$url_key = (string) $product->Url;
		
		if ((empty($url) || $url == '/') && !empty($url_key) && $url_key != '/') {
			$url = '/' . $url_key;
		} else if (empty($url_key) || $url_key == '/') {
			$sku = (string) $product->Sku;
            $url = Mage::getModel('catalog/product')->loadByAttribute('sku',$sku)->getProductUrl();
		}
		
		reset($products);
		return $url;
	}
	public function getMerchandizing() {
		return (string) $this->_dataForResults->merchandizing->html_code;
	}
    static function getMetaDescription($data) {
        return (string) $data->landing_page_info->meta_description;
    }
    static function getMetaKeywords($data) {
        return (string) $data->landing_page_info->meta_keywords;
    }
    static function getTitle($data) {
        return (string) $data->landing_page_info->page_title;
    }
}