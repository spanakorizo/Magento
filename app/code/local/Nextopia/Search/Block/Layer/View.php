<?php

class Nextopia_Search_Block_Layer_View extends Mage_Catalog_Block_Layer_View
{
    /* handle creation and display of refinements and selected refinements */
	private $_dataForResults = null;
	
	function getRefineOrder() {
		$nextopiasearch_options = Mage::getStoreConfig('nsearch_options');
		$refine_order = array();
		if (!empty($nextopiasearch_options['settings']['refine_order'])) {
			$refine_order = array_flip(explode(',', $nextopiasearch_options['settings']['refine_order']));
		}
		return $refine_order;
	}
	function getVisibleRefineNames() {
		$nextopiasearch_options = Mage::getStoreConfig('nsearch_options');
		$visible_refine_names = array();

		if (!empty($nextopiasearch_options['settings']['refine_mappings'])) {
			$refine_mappings_parts = explode("\n", $nextopiasearch_options['settings']['refine_mappings']);

			foreach($refine_mappings_parts as $refine_mappings_part) {
				$refine_parts = explode(',', $refine_mappings_part);
				
				$visible_refine_name_key = trim($refine_parts[0]);
				unset($refine_parts[0]);
				$visible_refine_names[$visible_refine_name_key] = trim(implode(',', $refine_parts), "\"\n\r\t "); // the idea here is to handle something in CSV format like >Field,"My Field, With a Comma"<
			}
		}
		return $visible_refine_names;	
	}
	function setDataForResults($data) {
		$this->_dataForResults = $data;
	}
	function getDataForResults()
	{
		return $this->_dataForResults;
	}
	public function getFilters() {
		$refine_order = $this->getRefineOrder();
		
		$visible_refine_names = $this->getVisibleRefineNames();

		$filters = array();
		$data = $this->getDataForResults();
		$refines = $data->refinables;
		$refines_ordered = array();
		$refines_nonordered = array();
		foreach($refines->refinable as $refine) {
			$name = (string) $refine->name;
			if (isset($refine_order[$name])) {
				$refines_ordered[$refine_order[$name]] = $refine;
			} else {
				$refines_nonordered[] = $refine;
			}
		}
		ksort($refines_ordered);
		$refines = array_merge($refines_ordered, $refines_nonordered);
		foreach($refines as $refine) {
			$url_builder = new Nextopia_Search_Model_UrlBuilder();
			$filters[] = new Nextopia_Search_Model_RefineFilter($refine, $url_builder, $visible_refine_names);
		}
		return $filters;
	}
	public function canShowBlock() {
		return true;
	}
	public function getStateHtml() {
		$data = $this->getDataForResults();
		$return_html = '';
		
		if (isset($data->user_search_depth->item)) {
			$return_html = '<div class="currently"><p class="block-subtitle">Currently Shopping by:</p><ol>';
			
			$depth = $data->user_search_depth;
			$clear_all_url_builder = new Nextopia_Search_Model_UrlBuilder();
			$clear_all_url_builder->drop('p');
			foreach($depth->item as $item) {
				$url_builder = new Nextopia_Search_Model_UrlBuilder();
				$key = (string) $item->key;
				$visible_refine_names = $this->getVisibleRefineNames();
				if (isset($visible_refine_names[$key])) {
					$shown_key = $visible_refine_names[$key];
				} else {
					$shown_key = $key;
				}
				$value = (string) $item->value;
				$url_builder->drop($key);
				$url_builder->drop('p');
				$clear_all_url_builder->drop($key);
				$return_html .= '<li>
            <a class="btn-remove" title="Remove This Item" href="' . $url_builder->toUrl() . '">Remove This Item</a>
            <span class="label">' . $shown_key . ':</span> ' . $value . '        </li>';
			}
			$return_html .= '</ol>';
			$return_html .= '<div class="actions"><a href="' . $clear_all_url_builder->toUrl() . '">Clear All</a></div>';
			$return_html .= '</div>';
		}
		return $return_html;
	}
}