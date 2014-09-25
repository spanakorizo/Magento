<?php


class Nextopia_Search_Model_CollectionWorker {
    public $noResults = false;
    public $skus = array();
    private $spotlights;
    private $page_data;

    public function getSkus(){
        return $this->skus;
    }

    /**
     * @param $collection
     * @return $collection
     * if you want to make the page load faster, you can remove the calls before the return. Obviously you
     * won't get the extra prices.
     */
    private function prepareCollection($collection) {

        $collection->addMinimalPrice();
        $collection->addFinalPrice();
        $collection->addTaxPercents();

        return $collection;
    }

    public function getSpotlights() {

        if (is_null($this->spotlights)) {

            $results = $this->page_data->results;
            $spotlights = array();

            foreach($results->result as $result) {

                $spotlights[] = (string) $result->nxt_spotlight;
            }
            $this->spotlights = $spotlights;

        }

        return $this->spotlights;
    }
    public function __construct($page_data) {
        $this->page_data = $page_data;
    }

    public function getCollectionBySkus() {
        $page_data = $this->page_data;
        $collection = Mage::getResourceModel('catalog/product_collection');

        $results = $page_data->results;

        $skus = array();

        foreach($results->result as $result) {
            $skus[] = (string) $result->Sku;
        }

        if (count($skus) > 0) {
            $this->skus = $skus;

            $collection = new Nextopia_Catalog_Model_Resource_Product_Collection($collection);
            $collection->addAttributeToSelect('*');
            $ids = array();
            $resource = Mage::getSingleton('core/resource');
            $readConnection = $resource->getConnection('core_read');
            $query = 'SELECT Sku, entity_id FROM ' . $resource->getTableName('catalog/product') .
                ' WHERE Sku in ("' . implode('","', $skus) . '");';
            $query_results = $readConnection->fetchAll($query);

            foreach($query_results as $query_result) {
                $ids[] = $query_result['entity_id'];
            }

            $collection->addIdFilter($ids);

            $collection = $this->prepareCollection($collection);
            $collection->load();
            $collection->reorderBySkus($skus);
            $total_products = (int) $page_data->pagination->total_products;
            $collection->setSize($total_products);
        } else {
            $this->noResults = true;
        }

        return $collection;
    }

    public function getCollectionByIds() {
        $page_data = $this->page_data;
        $collection = Mage::getResourceModel('catalog/product_collection');
        $search_options = Mage::getStoreConfig('nsearch_options');
		$idField = $search_options['settings']['productid_field_name'];

        $results = $page_data->results;

        $skus = array();
        $ids = array();

        foreach($results->result as $result) {
            $skus[] = (string) $result->Sku;
        }
        $this->skus = $skus;
        foreach($results->result as $result) {
            $ids[] = (string) $result->$idField;
        }

        if (count($skus) > 0) {
            $this->skus = $skus;

            $collection = new Nextopia_Catalog_Model_Resource_Product_Collection($collection);
            $collection->addAttributeToSelect('*');

            $collection->addIdFilter($ids);

            $collection = $this->prepareCollection($collection);
            $collection->load();
            $collection->reorderBySkus($skus);

            $total_products = (int) $page_data->pagination->total_products;
            $collection->setSize($total_products);
        } else {
            $this->noResults = true;
        }

        return $collection;
    }

}