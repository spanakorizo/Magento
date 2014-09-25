<?php

class Nextopia_Catalog_Model_Resource_Product_Collection extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection {
	/*  This extends an abstract class Mage_Eav_Model_Entity_Collection_Abstract, but the class name suggests
		that it is for a specific version of mysql, so this may need to be changed at some point.
		This class exists to allow reordering skus returned from magento's query to the order specified
		by nextopia.
	*/
	private $size = 0;
    private $unloaded_skus = array();

    /**
     * Reorder the products in the collection based on the order of the $skus array.
     * @param $skus
     */
    public function reorderBySkus($skus) {
		$new_items_by_id = array();
		$new_items = array();

        $found_id = array();
        $found_item = array();

        $id_to_sku = array();
        foreach($this->_items as $id => $product) {
            $id_to_sku[$id] = $product->getSku();

        }

		foreach($skus as $sku) {
            $sku_loaded = false;
            foreach($this->_itemsById as $id => $products) {
                if (isset($found_id[$id])) continue;
                $product = array_shift($products);
                if ($id_to_sku[$id] == $sku) {
					$new_items_by_id[$id] = array($product);
                    $sku_loaded = true;
                    $found_id[$id] = true;
                    break;
				}
			}

			foreach($this->_items as $id => $product) {
                if (isset($found_item[$id])) continue;
                if ($id_to_sku[$id] == $sku) {
					$new_items[$id] = $product;
                    $sku_loaded = true;
                    $found_item[$id] = true;
                    break;
				}
			}

            if (!$sku_loaded) {
                $this->unloaded_skus[] = $sku;
            }
		}

		unset($this->_items);
		unset($this->_itemsById);
		$this->_items = $new_items;
		$this->_itemsById = $new_items_by_id;

	}
	public function setSize($size) {

        $this->size = $size;
	}

    /**
     * The total number of results across all pages.
     */

	public function getSize() {

        return $this->size;
	}

    /**
     * The number of results currently loaded in the current page of results.
    */

    public function count() {
        return count($this->_items);

    }


    /**
     * @return array
     */
    public function getUnloadedSkus() {
        return $this->unloaded_skus;
    }
}