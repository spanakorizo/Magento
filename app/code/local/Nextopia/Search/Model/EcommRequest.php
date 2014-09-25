<?php

class Nextopia_Search_Model_EcommRequest extends Varien_Object
{

    private $_dataForResults = null;
    public $_usedQueryString = '';
    private $response = null;

	public function setLastResponse($response) {
		$this->_response = $response;
	}
	public function getLastResponse() {
		return $this->_response;
	}

    static function getRpp() {
        // # of results to show
        if (isset($_GET['limit'])) {
            $shown = 'limit';
        } else {
            $shown = 'shown';
        }

        if (!isset($_GET[$shown])) {
            $toolbar = new Mage_Catalog_Block_Product_List_Toolbar();
            foreach ($toolbar->getAvailableLimit() as $limit) {
                if ($toolbar->isLimitCurrent($limit)) {
                    $RESULTS_PER_PAGE = $limit;
                    break;
                }
            }
        } else if ($_GET[$shown] == 'all') {
            $RESULTS_PER_PAGE = 100;
        } else if (intval($_GET[$shown]) > 100) {
            $RESULTS_PER_PAGE = 100;
        } elseif (intval($_GET[$shown]) > 0) {
            $RESULTS_PER_PAGE = intval($_GET[$shown]);
        }

        return $RESULTS_PER_PAGE;
    }

	public function getQueryStringArray() {
		static $EcommQS;
		if (is_null($EcommQS)) {
			$search_options = Mage::getStoreConfig('nsearch_options');
			$CLIENT_ID = $search_options['settings']['private_client_id'];

            $load_mode = $search_options['settings']['load_method'];
			
			//Getting the IP and User Agent of the user performing the search
			$IPAddress = $_SERVER['REMOTE_ADDR'];
			$UserAgent = $_SERVER['HTTP_USER_AGENT'];
			$requested_refines = ""; // This overrides settings in the admin panel
			if ($load_mode == 'Sku') {
				$requested_fields = "Sku,Url"; // need url for redirection
            } elseif ($load_mode == 'Product ID') {
                $requested_fields = "Sku,Url," . $search_options['settings']['productid_field_name'];
            }

            $RESULTS_PER_PAGE = Nextopia_Search_Model_EcommRequest::getRpp();
			
			$EcommQS = $_GET;
            if (isset($EcommQS['lp'])) {
                $EcommQS['nxt_landing_page'] = $EcommQS['lp'];
                unset($EcommQS['lp']);
            }
			unset($EcommQS['shown']);
			$EcommQS['xml'] = '1';
			$EcommQS['refine'] = 'y';
			$EcommQS['searchtype'] = '1';
			$EcommQS['client_id'] = $CLIENT_ID;
			$EcommQS['res_per_page'] = $RESULTS_PER_PAGE;
			$EcommQS['ip'] = ($IPAddress);
			$EcommQS['user_agent'] = ($UserAgent);
			$EcommQS['requested_refines'] = ($requested_refines);
			$EcommQS['requested_fields'] = ($requested_fields);
            $EcommQS['related_searches'] = 1;
            $EcommQS['no_metaphones'] = '1:0';

			if (empty($EcommQS['keywords']) && !empty($EcommQS['q'])) {
				$EcommQS['keywords'] = $EcommQS['q'];
			}
			
			if (isset($EcommQS['keywords']) && preg_match('/\d|\-/', $EcommQS['keywords']) && substr_count($EcommQS['keywords'], ' ') == 0) {
			   $EcommQS['force_sku_search'] = "1:0";
			}
			if (empty($EcommQS['page'])) {
				$EcommQS['page'] = '1';
			}
			if (isset($_GET['p'])) {
				$EcommQS['page'] = $_GET['p'];
				unset($EcommQS['p']);
			}
			if (isset($_GET['limit'])) {
				$EcommQS['res_per_page'] = $_GET['limit'];
				unset($EcommQS['limit']);
			}
			if (isset($_GET['dir'])) {
				$dir = $_GET['dir'];
				if ($dir == 'asc' || $dir == 'desc') {
					if (isset($_GET['order'])) {
						$order_map = array('name' => 'Name', 'price' => 'Price');
						if (isset($order_map[$_GET['order']])) {
							$EcommQS['sort_by_field'] = $order_map[$_GET['order']] . ':' . strtoupper($dir);
                        } else {
                            $order = ucfirst(strtolower(str_replace('_', '', $_GET['order'])));
                            $EcommQS['sort_by_field'] = $order . ':' . strtoupper($dir);
						}
					}
					unset($EcommQS['order'], $order, $order_map);
				}
				unset($EcommQS['dir']);
			}
		}
		return $EcommQS;
	}
	
	public function getDataForResults()
	{
		if (empty($this->_dataForResults)) {
			$sites = array(
				'http://ecommerce-search.nextopiasoftware.com/return-results.php',
				'http://ecommerce-search.nextopiasoftware.net/return-results.php',
				'http://ecommerce-search-dyn.nextopia.net/return-results.php'
			);

			$EcommQS = $this->getQueryStringArray();
			$URLToXML = "?" . http_build_query($EcommQS);

			//Send the XML Query String
			$connected = false;

			for ($i = 0; $i < count($sites); $i++) {
				$full_url = $sites[$i] . $URLToXML;

				$ch = curl_init($full_url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				$XML = curl_exec($ch);
				curl_close($ch);

				if (!(strpos($XML, "<xml_feed_done>1</xml_feed_done>") == false)) {
					$XMLHeader = '<?xml version="1.0" encoding="ISO-8859-1" ?>';
					$XML = substr($XML, strpos($XML, $XMLHeader));
					$connected = true;
					break;
				}
			}
				
			// to roll to local search
			if (!$connected) {
				header("Location: /catalogsearch/result/?q=" . urlencode($EcommQS['keywords']));
				exit;
			}

			$XMLData = new SimpleXMLElement($XML);
			$this->_dataForResults = $XMLData;
		}
		
		$response = new Nextopia_Search_Model_EcommResponse();
		$response->setDataForResults($XMLData);
		$response->setUsedQueryString($full_url);
		$this->setLastResponse($response);
	}
}