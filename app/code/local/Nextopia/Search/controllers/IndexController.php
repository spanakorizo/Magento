<?php

class Nextopia_Search_IndexController extends Mage_Core_Controller_Front_Action {
	
	public function indexAction() {
		$this->loadLayout();


        // make the call to nextopia's search results
        $EcommRequest = new Nextopia_Search_Model_EcommRequest();
        $EcommRequest->getDataForResults();
        $EcommResponse = $EcommRequest->getLastResponse();

        $this->handleRedirects($EcommResponse);
		$results_block_page = $this->getLayout()->getBlock('search.result');	
        $results_block = $this->getLayout()->getBlock('search_result_list');
        $head_block = $this->getLayout()->getBlock('head');
        $data = $EcommResponse->getDataForResults();



        $meta_description = Nextopia_Search_Model_EcommResponse::getMetaDescription($data);
        if (!empty($meta_description)) {
            $head_block->setDescription($meta_description);
        }
        $meta_keywords = Nextopia_Search_Model_EcommResponse::getMetaKeywords($data);
        if (!empty($meta_keywords)) {
            $head_block->setKeywords($meta_keywords);
        }
        $title = Nextopia_Search_Model_EcommResponse::getTitle($data);
        if (!empty($title)) {
            $head_block->setTitle($title);
        } else if (isset($_GET['keywords']) || isset($_GET['q'])) {
            $keywords = isset($_GET['keywords']) ? $_GET['keywords'] : $_GET['q'];
            $head_block->setTitle('Search results for "' . htmlentities($keywords) . '"');
        }

		if (intval((string) $data->pagination->total_products) === 0) {
			$results_block->noResults = true;
		}

		// give the blocks the data to work with
		$results_block->setDataForResults($data);
		$results_block_page->setDataForResults($data);
		$this->getLayout()->getBlock('catalogsearch.leftnav')->setDataForResults($data);

        $notices = $EcommResponse->getNotices();
        foreach($notices as $notice) {
            Mage::getSingleton('customer/session')->addNotice($notice);
            $storage = Mage::getSingleton('customer/session');
            $block = $this->getLayout()->getMessagesBlock();

            $block->addMessages($storage->getMessages(true));
        }

		// render messages
        $this->_initLayoutMessages('catalog/session');
        $this->_initLayoutMessages('checkout/session');

        $this->renderLayout();
	}
	
	private function handleRedirects($EcommResponse) {
		// handle both possible redirects
		$nextopiasearch_options = Mage::getStoreConfig('nsearch_options');
		$REDIRECT_ON_ONE_MATCH = true;
		if (!empty($nextopiasearch_options['settings']['redirect_on_one_match'])) {
			$REDIRECT_ON_ONE_MATCH = $nextopiasearch_options['settings']['redirect_on_one_match'];
		}
		
		$merchandizing = $EcommResponse->getMerchandizing();
		$redirect_url = '';
		if (strpos($merchandizing, "link:") === 0) {
			$redirect_url = str_replace("link:", "", $merchandizing);
		} else if ($REDIRECT_ON_ONE_MATCH && $EcommResponse->getResultsCount() === 1) {
			$redirect_url = $EcommResponse->getFirstProductUrl();
		}
		
		if (!empty($redirect_url)) {
			header("Location: $redirect_url");
			exit();
		}	
	}
}
