<?php

class Nextopia_Search_Block_Result extends Mage_Core_Block_Template {

	private $_dataForResults;
	
	function setDataForResults($data) {
		$this->_dataForResults = $data;
	}
	
	function getDataForResults() {
		return $this->_dataForResults;
    }

	function getResultCount() {
		return 1;
    }

    /**
     * Set available view mode
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListModes()
    {
        return $this;
    }

    /**
     * Set Search Result collection
     *
     * @return Mage_CatalogSearch_Block_Result
     */
    public function setListCollection()
    {
        return $this;
    }

    /**
     * Retrieve Search result list HTML output
     *
     * @return string
     */
    public function getProductListHtml()
    {
        return $this->getChildHtml('search_result_list');
    }

    /**
     * Retrieve No Result or Minimum query length Text
     *
     * @return string
     */
    public function getNoResultText()
    {
        return '';
    }

} 