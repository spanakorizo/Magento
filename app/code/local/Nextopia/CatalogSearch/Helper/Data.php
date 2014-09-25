<?php

class Nextopia_CatalogSearch_Helper_Data extends Mage_CatalogSearch_Helper_Data
{
	/**
     *  If the nextopia search extension is enabled, we rewrite the search form url to refer to
		{local path}/nsearch.
	*/
	private function isNextopiaSearchEnabled() {
		$nextopia_search_options = Mage::getStoreConfig('nsearch_options');
		$search_enabled = false;
		if (isset($nextopia_search_options['settings'])) {
			$nextopia_settings = $nextopia_search_options['settings'];
			if (isset($nextopia_settings['searchstatus']) && $nextopia_settings['searchstatus'] === '1')
			{
				$search_enabled = true;
			}
		}
		return $search_enabled;
	}

    /**
     * @param null $query
     * @return mixed
     *
     *  If the nextopia search magento extension is enabled the following method will change the search form
     * action accordingly.
     *
     */
    public function getResultUrl($query = null) {
		$search_enabled = $this->isNextopiaSearchEnabled();
		if ($search_enabled) {
			$url = $this->_getUrl('nsearch', array(
                '_query' => array(self::QUERY_VAR_NAME => $query),
                '_secure' => Mage::app()->getFrontController()->getRequest()->isSecure()
            ));
		} else {
			$url = parent::getResultUrl();
		}
		return $url;
	}
}
