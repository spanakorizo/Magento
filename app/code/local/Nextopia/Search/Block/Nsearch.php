<?php

class Nextopia_Search_Block_Nsearch extends Mage_Catalog_Block_Product_List
{
    private $_dataForResults = '';
    private $_usedQueryString = '';
    public $noResults = false;
	private $_skus = array();
    private $_spotlights = array();

    /*
    public function getColumnCount() {
        return 4;
    }
    */
	function _toHtml() {
		/*  this function shows no results text if no results are found, or injects the click tracking
			script if there are.
		*/

        if (isset($_GET['q'])) {
            $keywords = $_GET['q'];
        } else {
            $keywords = '';
        }

        $merchandizing = $this->getLandingPageBanner();

        if ($this->noResults) {
            $custom_expiry_html = $this->getLandingPageCustomExpiryHtml();
            if (!empty($custom_expiry_html)) {
                return $custom_expiry_html;
            } else if (isset($this->_dataForResults->custom_nrf_content)) {
				$nrfHtml = (string) $this->_dataForResults->custom_nrf_content;
			} else {
				$nrfHtml = '
					<div class="no-results-found">
						<div class="your-search">
							Your search - "[KEYWORDS]" - did not match any products
						</div>
						[DIDYOUMEAN]
						<div class="noresults_suggestions">
							<h4>Suggestions:</h4>
							<ul>
								<li>Make sure all words are spelled correctly.</li>
								<li>Try different keywords.</li>
								<li>Try more general keywords.</li>
							</ul>
						</div>
					</div>';
			}
			$spelling = (string) $this->_dataForResults->suggested_spelling;

            if (!empty($spelling)) {
                $did_you_url = Mage::helper('catalogsearch')->getResultUrl() . '?q=' . $spelling;
                $did_you_mean = 'Did you mean: <a href="' . $did_you_url . '">' . $spelling . '</a>?';
            } else {
                $did_you_mean = '';
            }

			$nrfHtml = str_replace('[DIDYOUMEAN]', $did_you_mean, $nrfHtml);
			$nrfHtml = str_replace('[KEYWORDS]', htmlentities($keywords), $nrfHtml);
			return $merchandizing . $nrfHtml;
		} else {
			$css = '<style>';
            if (!empty($_GET['lp'])) {
                $css .= 'div.page-title {display: none;}';
            }
			$css .= '</style>';
            $nextopiasearch_options = Mage::getStoreConfig('nsearch_options');
			$MD5_CLIENT_ID = $nextopiasearch_options['settings']['public_client_id'];

            if (empty($merchandizing)) {
			    $merchandizing = (string) $this->_dataForResults->merchandizing->html_code;
            }
			
			$script = '
			<!-- nextopia client-side script -->
			<script>
				var nxt_x = function(w,x) {
					info = new Image(1,1);
					var z = 1;
					info.src = "http://analytics.nextopia.net/x.php?r="' .
						'+ Math.floor(Math.random()*200) + ' .
						'"&v=' . urlencode($keywords) . '&w=" + w + ' .
						'"&x=" + x + "&z=" + z + ' .
						'"&y=' . $MD5_CLIENT_ID . '";
					return true;
				};
				var sku, skus = ["' . implode('", "', $this->_skus) . '"], i, L;
				var nodes = $$(".products-grid li.item");
				var a, as, j, M, href;
				for (i = 0, L = nodes.length; i < L; i++) {
					sku = skus[i];
					as = nodes[i].getElementsByTagName("a");
					for (j = 0, M = as.length; j < M; j++) {
						a = as[j];
						href = a.getAttribute("href");
						if (href.indexOf("/product/") == -1) {
							a.setAttribute("onclick", "return nxt_x(\'" + sku + "\', -100)");
						}
					}
				}
				';
			if (count($this->_spotlights) > 0) {
                $script .= '
                delete console;
                delete console.log;
                var img, spotlight, spotlights = ["' . implode('", "', $this->_spotlights) . '"];
				for (i = 0, L = nodes.length; i < L; i++) {
					spotlight = spotlights[i];
					if (!spotlight || spotlight === "") continue;
                    as = nodes[i].getElementsByTagName("a");
                    for (j = 0, M = as.length; j < M; j++) {
                        if (as[j].getElementsByTagName("img").length) {
                            a = as[j];
                            a.style.position = "relative";
                            img = document.createElement("img");
                            img.src = spotlight;
                            img.style.position = "absolute";
                            img.style.top = "0";
                            img.style.left = "0";
                            a.appendChild(img);
                        }
					}
				}
                ';
            }
			$script .= '</script>
			<script type="text/javascript">


				function showHide(str) {
					var ol = document.getElementById(\'nxtRefine\' + str);
					var lis = ol.getElementsByTagName(\'li\');
					var i, L = lis.length;
					var link = document.getElementById(\'showHide\' + str);
					if (link.innerHTML == \'Show Less\') {
						for (i = 0; i < L; i++) {
							if (lis[i].className.indexOf(\'nxtHiddenRefines\') > -1) {
								lis[i].style.display = \'none\';
							}
						}
						link.innerHTML = \'Show More\';
						} else {
						for (i = 0; i < L; i++) {
							if (lis[i].className.indexOf(\'nxtHiddenRefines\') > -1) {
								lis[i].style.display = \'block\';
							}
						}
						link.innerHTML = \'Show Less\';
					}
						}
			</script>
			<!-- / nextopia client-side script -->
			';
            $related_searches = $this->getRelatedSearchesHtml();
            return $css . $merchandizing . $related_searches . parent::_toHtml() . $script;
		}
	}

    function getRelatedSearchesHtml() {
        $related_links = array();
        $raw_related_string = (string) $this->getDataForResults()->related_searches;
        foreach(explode('||', $raw_related_string) as $related) {
            if (!empty($related)) {
                $related_links[] = '<a href="' .
                    Mage::helper('catalogsearch')->getResultUrl() . '?q=' . $related .
                    '">' . $related . '</a>';
            }
        }
        if (count($related_links) > 0) {
            $related_searches = '<div class="nxt-related"><label>Related Searches:</label> ' .
                implode(', ', $related_links) .
                '</div>';
        } else {
            $related_searches = '';
        }

        return $related_searches;

    }
	
	function setDataForResults($data) {
		$this->_dataForResults = $data;
	}
	
	function getDataForResults() {
		return $this->_dataForResults;
	}

	function get_cur_page() {
		return (isset($_REQUEST['p'])) ? intval($_REQUEST['p']) : 1;
	}

	protected function _getProductCollection() {
		/* 	Convert the products returned from nextopia to a magento collection, or flag the no
			results boolean.
		*/
		if (is_null($this->_productCollection)) {
			
            $collection_worker = new Nextopia_Search_Model_CollectionWorker($this->getDataForResults());

            $nextopiasearch_options = Mage::getStoreConfig('nsearch_options');
            if (isset($nextopiasearch_options['settings']['load_method'])) {
            	$load_mode = $nextopiasearch_options['settings']['load_method'];
			} else {
				$load_mode = 'Sku';
			}

            if ($load_mode == 'Sku') {
                $collection = $collection_worker->getCollectionBySkus();
            } elseif ($load_mode == 'Product ID') {
                $collection = $collection_worker->getCollectionByIds();
			}
            $this->_spotlights = $collection_worker->getSpotlights();

            $this->_skus = $collection_worker->getSkus();

            $this->_productCollection = $collection;
			
        }
        return $this->_productCollection;
	}
	
	public function getMode() {
		/* grid / list selection */
		if (isset($_GET['mode'])) {
			$view_mode = $_GET['mode'];
		} else {
			$nextopiasearch_options = Mage::getStoreConfig('nsearch_options');
			$view_mode = strtolower($nextopiasearch_options['settings']['default_display_type']);
		}
		return $view_mode;
		
	}
		
	protected function _beforeToHtml() {
		/* load product collection */
        if (!$this->noResults) {
            $collection = $this->_getProductCollection();
            $toolbar = $this->getToolbarBlock();



            if ($orders = $this->getAvailableOrders()) {
                $toolbar->setAvailableOrders($orders);
            }
            if ($sort = $this->getSortBy()) {
                $toolbar->setDefaultOrder($sort);
            }
            if ($dir = $this->getDefaultDirection()) {
                $toolbar->setDefaultDirection($dir);
            }
            if ($modes = $this->getModes()) {
                $toolbar->setModes($modes);
            }
            $toolbar->setCollection($collection);

            $this->setChild('product_list_toolbar', $toolbar);

            $collection->load();

            // the parent handles the creation of the toolbar. In some configurations this will throw an error
            // when there are no results.
            return parent::_beforeToHtml();
        }
    }
    public function getLandingPageBanner() {
        return (string) $this->_dataForResults->landing_page_info->banner_html;
    }
    public function getLandingPageCustomExpiryHtml() {
        return (string) $this->_dataForResults->landing_page_info->custom_expiry_html;
    }
}
