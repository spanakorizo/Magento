<?php 
class Compandsave_Catalog_Model_Convert_Adapter_Category
    extends Mage_Eav_Model_Convert_Adapter_Entity
{
    protected $_categoryCache = array();

    protected $_stores;

    /**
     * Category display modes
     */
    protected $_displayModes = array( 'PRODUCTS', 'PAGE', 'PRODUCTS_AND_PAGE');

    public function parse()
    {
        $batchModel = Mage::getSingleton('dataflow/batch');
        /* @var $batchModel Mage_Dataflow_Model_Batch */

        $batchImportModel = $batchModel->getBatchImportModel();
        $importIds = $batchImportModel->getIdCollection();

        foreach ($importIds as $importId) {
            //print '<pre>'.memory_get_usage().'</pre>';
            $batchImportModel->load($importId);
            $importData = $batchImportModel->getBatchData();

            $this->saveRow($importData);
        }
    }

    /**
     * Save category (import)
     *
     * @param array $importData
     * @throws Mage_Core_Exception
     * @return bool
     */
    public function saveRow(array $importData)
    {
        if (empty($importData['store'])) {
            if (!is_null($this->getBatchParams('store'))) {
                $store = $this->getStoreById($this->getBatchParams('store'));
            } else {
                $message = Mage::helper('catalog')->__('Skip import row, required field "%s" not defined', 'store');
                Mage::throwException($message);
            }
        } else {
            $store = $this->getStoreByCode($importData['store']);
        }

        if ($store === false) {
            $message = Mage::helper('catalog')->__('Skip import row, store "%s" field not exists', $importData['store']);
            Mage::throwException($message);
        }

        $rootId = $store->getRootCategoryId();
        if (!$rootId) {
            return array();
        }
        $rootPath = '1/'.$rootId;
        if (empty($this->_categoryCache[$store->getId()])) {
            $collection = Mage::getModel('catalog/category')->getCollection()
                ->setStore($store)
                ->addAttributeToSelect('name');
            $collection->getSelect()->where("path like '".$rootPath."/%'");

            foreach ($collection as $cat) {
                $pathArr = explode('/', $cat->getPath());
                $namePath = '';
                for ($i=2, $l=sizeof($pathArr); $i<$l; $i++) {
                    $name = $collection->getItemById($pathArr[$i])->getName();
                    $namePath .= (empty($namePath) ? '' : '/').trim($name);
                }
                $cat->setNamePath($namePath);
            }

            $cache = array();
            foreach ($collection as $cat) {
                $cache[strtolower($cat->getNamePath())] = $cat;
                $cat->unsNamePath();
            }
            $this->_categoryCache[$store->getId()] = $cache;
        }
        $cache =& $this->_categoryCache[$store->getId()];

        $importData['categories'] = preg_replace('#\s*/\s*#', '/', trim($importData['categories']));
        if (!empty($cache[$importData['categories']])) {
            return true;
        }

        $path = $rootPath;
        $namePath = '';

        $i = 1;
        $categories = explode('/', $importData['categories']);
		
		//$thumb_image = $importData['Thumbnail Image'];
		$des = $importData['Description'];
		$short_des = $importData['Short Description'];
		//$image = $importData['Image'];
		$page_meta_title = $importData['Page Meta Title'];
		$meta_key = $importData['Meta Keywords'];
		$meta_des = $importData['Meta Description'];
		$url_key = $importData['url key'];
		$nav_menu = $importData['nav menu'];
		 //setImage('url')setThumbnailImage
        foreach ($categories as $catName) {
            $namePath .= (empty($namePath) ? '' : '/').strtolower($catName);
            if (empty($cache[$namePath])) {

                $dispMode = $this->_displayModes[2];
				$cat_id = Mage::getModel('catalog/category');
                $cat = Mage::getModel('catalog/category')
                    ->setStoreId($store->getId())
                    ->setPath($path)
                    ->setName($catName)
					->setUrlKey($url_key)
					//->setThumbnail($thumb_image)
					->setDescription($des)
					->setShortDescription($short_des)
					//->setImage($image)
					->setMetaTitle($page_meta_title)
					->setMetaKeywords($meta_key)
					->setMetaDescription($meta_des)
                    ->setAttributeSetId($cat_id->getDefaultAttributeSetId())
                    ->setIsActive(1)
                    ->setIsAnchor(1)
                    ->setDisplayMode($dispMode)
					->setIncludeInMenu($nav_menu)
                    ->save();
                $cache[$namePath] = $cat;
            }
            $catId = $cache[$namePath]->getId();
            $path .= '/'.$catId;
            $i++;
        }

        return true;
    }

    /**
     * Retrieve store object by code
     *
     * @param string $store
     * @return Mage_Core_Model_Store
     */
    public function getStoreByCode($store)
    {
        $this->_initStores();
        if (isset($this->_stores[$store])) {
            return $this->_stores[$store];
        }
        return false;
    }

    /**
     *  Init stores
     *
     *  @param    none
     *  @return      void
     */
    protected function _initStores ()
    {
        if (is_null($this->_stores)) {
            $this->_stores = Mage::app()->getStores(true, true);
            foreach ($this->_stores as $code => $store) {
                $this->_storesIdCode[$store->getId()] = $code;
            }
        }
    }
}

?> 