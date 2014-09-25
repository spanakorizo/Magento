<?php
class Compandsave_Variable_Model_Observer
{
    protected $html = '';
    protected $json_value = '';

    public function variable_static_code_insert()
    {
        $block = new Mage_Catalog_Block_Category_View();
        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_write');
        $tableName = $coreResource->getTableName('compandsave_variable/brand');
        $category_model = Mage::getModel('catalog/category')->load(2);
        $subcategoryIds = $category_model->getChildren();
        $subCatIds = explode(',',$subcategoryIds);
        $brandcollection = Mage::getResourceModel('catalog/category_collection')->addAttributeToFilter('is_active', 1)->addFieldToFilter('entity_id',$subCatIds)->addAttributeToSelect(array('name','entity_id','image','description'))->load();

        foreach($brandcollection as $_category){
            $this->html = '';
            $_helper    = $block->helper('catalog/output');
            $currentCategoryId = $_category->getId();
            $_imgHtml   = '';
            if ($_imgUrl = $_category->getImageUrl()) {
                $_imgHtml = '<p class="category-image"><img src="'.$_imgUrl.'" alt="'.$block->escapeHtml($_category->getName()).'" title="'.$block->escapeHtml($_category->getName()).'" /></p>';
                $_imgHtml = $_helper->categoryAttribute($_category, $_imgHtml, 'image');
            }

            $this->html .=	'
						<div class="three_fourth">';
            if($block->IsRssCatalogEnable() && $block->IsTopCategory()){
                $this->html .= '<a href="'.$block->getRssLink().'" class="link-rss">'.$block->__('Subscribe to RSS Feed').'</a>';
            }
            $this->html .= '<h1 class="ti_green_head text_left">'.$_helper->categoryAttribute($_category, $_category->getName(), "name").'</h1>';//.$block->getMessagesBlock()->getGroupedHtml();

            if($_imgUrl){
                $this->html .= '<p>'.$_imgHtml;
                if($_description= $_category->getDescription())
                    $this->html .= '<p>'.$_helper->categoryAttribute($_category, $_description, "description").'</p>';
                $this->html .= '</p>';
            }
            else{
                if($_description = $_category->getDescription())
                    $this->html .= '<p>'.$_helper->categoryAttribute($_category, $_description, "description").'</p>';


            }
            $this->html .= '</div>
					<div class="one_fourth ti_ink_selector_holder">
                        <div class="ti_cms_gradient_block ti_block_inner">
    						<h2 class="ti_headerH1 ti_green_head">Ink Search</h2>
    						<div id="ti_ajax_cat_loading_ink"><img src="'.$block->getSkinUrl("images/ti_loading_small.gif").'" /></div>
    						<div class="ti_select_barContainerBorder btcf">
    							<div class="ti_select_barContainer btcf">
    								<select name="" id="ti_series_selector">
    									<option value="0" selected="">Series</option>';

                $category_model = Mage::getModel('catalog/category')->load($currentCategoryId);
                $subcategoryIds = $category_model->getChildren();
                $subCatIds = explode(',',$subcategoryIds);
                $collection = Mage::getResourceModel('catalog/category_collection')->addAttributeToFilter('is_active', 1)->addFieldToFilter('entity_id',$subCatIds)->addAttributeToSelect(array('name','entity_id','image'))->addAttributeToSort('name', 'ASC')->load();

                foreach($collection as $item){
                    $productCollection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                        ->addAttributeToSelect(array('name','url'))
                        ->addAttributeToFilter('category_id', $item->entity_id)
                        ->addAttributeToFilter('status', 1);

                    if($productCollection->count()){
                        $this->html .= '<option value="'.$item->getId().'">'.$item->getName().'</option>';
                    }
                }


                $this->html .= '</select>
    							</div>
    						</div>

    						<div class="ti_select_barContainerBorder btcf">
    							<div class="ti_select_barContainer btcf">

    								<select disabled="" name="" id="ti_cartridge_selector">
    									<option value="" selected="">Model</option>
    								</select>
    							</div>
    						</div>
                        </div>
					</div>
					<div class="one ti_hide_479">
						<div class="ti_input_bar">
							<div class="ti_input_barContainer">';

            $catalogSearchHelper =  $block->helper('catalogsearch');

            $this->html .= '<form id="search_mini_form3" action="'.$catalogSearchHelper->getResultUrl().'" method="get">
									<input type="text" name="'. $catalogSearchHelper->getQueryParamName() .'" id="search3" Placeholder="Find your cartridge" />
									<input type="submit" name="Submit" id="search_submit3" value="Search" title="'. $block->__("Search") .'">
									<div id="search_autocomplete3" class="search-autocomplete"></div>
									<script type="text/javascript">
									//<![CDATA[
										var searchForm3 = new Varien.searchForm("search_mini_form3", "search3", "'. $block->__("Find your cartridge") .'");
										searchForm3.initAutocomplete("'. $catalogSearchHelper->getSuggestUrl() .'", "search_autocomplete3");
									//]]>
									</script>
                            </form>
							</div>
						</div>
					</div>';

            $this->html .= '<div class="btcf">';
            foreach($collection as $series){
                $productCollection = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                    ->addAttributeToSelect(array('name','url'))
                    ->addAttributeToFilter('category_id', $series->entity_id)
                    ->addAttributeToFilter('status', 1);
                if($productCollection->count()){
                    $this->html .= '<div class="ti_cms_block_brandVB" id="'.$series->name.'-'.$series->entity_id.'">
							<div class="ti_block_inner ti_cms_border_block">';
                                    //********************** if we need add series pic uncomment below two lines
									//if($series->getImageUrl()) $this->html .= '<a href="#"><img src="'.$series->getImageUrl().'"/></a>';
                                    //else $this->html .= '<a href="#"><img src="'.$block->getSkinUrl("images/dummy-printer.jpg").'"/></a>';
                    $this->html .= '<p><a href="#">'.$series->getName().' Series</a></p>
							</div>
						</div>';
                }

            }
            $this->html .= '</div>
				<div class="btcf">
					<h1 id="ti_show_all_series-'.$currentCategoryId.'" class="ti_show_hide_series lgTxt ltWeight"><a href="#">+ Show All Series</a></h1>
				</div>
				<div id="ti_all_series_list">';
            foreach($collection as $item){

                $productCollection = Mage::getModel('catalog/product')
                    ->getCollection()
                    ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                    ->addAttributeToSelect(array('name','url'))
                    ->addAttributeToFilter('category_id', $item->entity_id)
                    ->addAttributeToFilter('status', 1)
                    ->addAttributeToSort('name', 'ASC');
                $flag_four = false;
                $flag_three = false;
                $flag_fifth = false;
                if($productCollection->count()){

                    $this->html .= '<div class="ti_cms_block_headerBar-green" id="ti_series_header_display-'. $item->getId().'">
							<div class="ti_series_name" id="ti_header_series_name_display-'. $item->getId().'">
								<h2 class="ti_headerH1 white">'.$item->getName().' Series</h2>
							</div>
							<div class="ti_series_header" id="ti_series_display-'. $item->getId().'" style="display: none">';
                    foreach($productCollection as $product_model_len){
                        $test_len = strlen($product_model_len->getName());
                        if($test_len > 15 and $test_len < 26)
                            $flag_fifth = true;
                        if($test_len > 25 and $test_len < 36)
                            $flag_four = true;
                        elseif($test_len > 35)
                            $flag_three = true;
                    }
                    if($flag_four == true and $flag_three == false){
                        foreach($productCollection as $product_model){
                                $this->html .= '<div class="ti_subcat_product_gid_one_fourth"><p><a href="'. $product_model->getProductUrl().'" >'.$product_model->getName().'</a></p></div>';
                        }
                    }
                    elseif($flag_three == true){
                        foreach($productCollection as $product_model){
                            $this->html .= '<div class="ti_subcat_product_gid_one_third"><p><a href="'. $product_model->getProductUrl().'" >'.$product_model->getName().'</a></p></div>';
                        }
                    }
                    elseif($flag_fifth == true and $flag_four == false and $flag_three == false){
                        foreach($productCollection as $product_model){
                            $this->html .= '<div class="ti_subcat_product_gid_one_fifth"><p><a href="'. $product_model->getProductUrl().'" >'.$product_model->getName().'</a></p></div>';
                        }
                    }
                    else{

                        foreach($productCollection as $product_model){
                            $this->html .= '<div class="ti_subcat_product_gid_one_sixth"><p><a href="'. $product_model->getProductUrl().'" >'.$product_model->getName().'</a></p></div>';

                        }

                    }



                    $this->html .='</div>
						</div>';
                }
            }
            $this->html .='</div>
			';
            $current_day_time = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
            $conn->delete($tableName, array('brand_id = ?' => $currentCategoryId));
            $conn->insert($tableName,array('brand_id' => $currentCategoryId , 'value' => $this->html , 'visibility' => '1','status' =>'1','created_at' => $current_day_time, 'updated_at' => $current_day_time));
        }

        return $this;

    }

    public function productselector_json_code_insert(){

        $coreResource = Mage::getSingleton('core/resource');
        $conn = $coreResource->getConnection('core_write');
        $conn_read = $coreResource->getConnection('core_read');
        $tableName = $coreResource->getTableName('compandsave_variable/productselectors');

        $CatPrdTable = $coreResource->getTableName('catalog_category_product');
        $brand_model = Mage::getModel('catalog/category')->load(2);
        $brandIds = $brand_model->getChildren();
        $subCatIds = explode(',',$brandIds);
        $brandcollection = Mage::getResourceModel('catalog/category_collection')->addAttributeToFilter('is_active', 1)->addFieldToFilter('entity_id',$subCatIds)->addAttributeToSelect(array('name','entity_id','image','description'))->load();

        foreach($brandcollection as $_category){ // $_category is brand collection
            $currentCategoryId = $_category->getId();
            $category_model = Mage::getModel('catalog/category')->load($currentCategoryId);
            $subcategoryIds = $category_model->getChildren();
            $seriesIds = explode(',',$subcategoryIds);

            $collection = Mage::getResourceModel('catalog/category_collection')->addAttributeToFilter('is_active', 1)->addFieldToFilter('entity_id',$seriesIds)->addAttributeToSelect(array('name','entity_id'))->addAttributeToSort('name', 'ASC')->load();
            $series = array();
            $model = array();

            if(!empty($collection)){
                foreach($collection as $item){

                    $productCollection = Mage::getModel('catalog/product')
                        ->getCollection()
                        ->joinField('category_id', 'catalog/category_product', 'category_id', 'product_id = entity_id', null, 'left')
                        ->addAttributeToSelect(array('name','url'))
                        ->addAttributeToFilter('category_id', $item->entity_id)
                        ->addAttributeToFilter('status', 1);
                    if($productCollection->count()){
                        $series_len = strlen($item->name);
                        $select = $conn_read->select()
                            ->from($CatPrdTable,array('product_id'))
                            ->where('category_id = ?',$item->entity_id)
                            ->where('position = ? ', '1');
                        $result = $conn_read->fetchCol($select);

                        if(!empty($result)){

                            $collection = Mage::getModel('catalog/product')
                                ->getCollection()
                                ->addFieldToFilter('entity_id', $result)
                                ->addAttributeToFilter('status', 1)
                                ->addAttributeToSelect(array('name','entity_id'))
                                ->addAttributeToSort('name', 'ASC');


                            foreach($collection as $product_model){
                                $model_name = $product_model->getName();
                                if(substr_count($model_name,$item->name)){
                                    $model_len = strlen($model_name);
                                    $model_name = substr($model_name,$series_len, $model_len - $series_len + 1);
                                    $model_name = str_replace('-','', $model_name);
                                    $model_name = trim($model_name);
                                }

                                $model[] = array("id" => $product_model->getId(), "name" => $model_name , "url" => $product_model->getProductUrl() );



                            }

                        }

                        $series["$item->entity_id"]  =  array("name" => $item->name ,"level" => "3","sub" => $model );

                        unset($model);//for flash model array and initialize to empty
                    }



                }

                $this->json_value = json_encode($series );


            }

            $current_day_time = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
            $conn->delete($tableName, array('brand_id = ?' => $currentCategoryId));
            $conn->insert($tableName,array('brand_id' => $currentCategoryId , 'value' => $this->json_value , 'visibility' => '1','status' =>'1','created_at' => $current_day_time, 'updated_at' => $current_day_time));
            unset($series);
            unset($model);

        }//end brand category collection

        return $this;

    }

}