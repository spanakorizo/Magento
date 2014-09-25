<?php

class Nextopia_Search_Model_RefineFilter {
	private $_itemCount = 0;
	private $_name = '';
	private $_shownName = '';
	private $_html = '';
	private $_numRefinesBeforeHide = 10;
	public function __construct($node, $url_builder, $visible_refine_names) {
		$nextopiasearch_options = Mage::getStoreConfig('nsearch_options');
		$refines_sorted_numerically = array_flip(explode(',', $nextopiasearch_options['settings']['refines_sorted_numerically']));

		$name = (string) $node->name;
		$this->_name = $name;
		if (isset($visible_refine_names[$name])) {
			$this->_shownName = $visible_refine_names[$name];
		} else {
			$this->_shownName = $name;
		}
		$html = '<ol id="nxtRefine'. $this->_name .'">';
		$count = 0;
		
		$values = array();
		foreach( $node->values->value as $leaf) {
			$values[] = array(
				'num' => (string) $leaf->num,
				'name' => (string) $leaf->name,
			);
		}
		if (isset($refines_sorted_numerically[$this->_name])) {
			if (!function_exists('sort_refine_values')) {
				function sort_refine_values($a, $b) {
					if ($a['num'] == $b['num']) {
						return strcmp($a['name'], $b['name']);
					}
					return $a['num'] < $b['num'];
				}
			}
			usort($values, "sort_refine_values");
		}
		foreach($values as $index => $pair) {
			$nodeCount = (string) $pair['num'];
			$nodeName = (string) $pair['name'];
			$count += $nodeCount;
			$url = $url_builder->drop('p')->drop('dir')->drop('order')->set($this->_name, $nodeName)->toUrl();
			if ($index >= $this->_numRefinesBeforeHide) {
				$attributes = ' class="nxtHiddenRefines nxtHiddenRefines'. $this->_name .'" style="display:none;"';
			} else {
				$attributes = '';
			}

			$html .= "<li$attributes><a href=\"$url\"> $nodeName </a> ($nodeCount)</li>
				";
		}
		if (count($values) > $this->_numRefinesBeforeHide) {
			$html .= '<li class="nxtHiddenRefinesShowHide"><a id="showHide' . $this->_name . '" onclick="showHide(\'' . $this->_name . '\')">Show More</a></li>';
		}
		$html .= '</ol>';
		$this->_itemCount = $count;
		$this->_html = $html;
	}
	public function getHtml() {
		return $this->_html;
	}
	
	public function getName() {
		return $this->_shownName;
	}
	public function getItemsCount() {
		return $this->_itemCount;
	}
}