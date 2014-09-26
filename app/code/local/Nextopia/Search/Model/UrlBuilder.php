<?php

class Nextopia_Search_Model_UrlBuilder {
	private $prefix = '';
	private $vars = array();
	public function __construct() {
		$this->prefix = preg_replace('/\?.+/', '', $_SERVER['REQUEST_URI']);
		$this->vars = $_GET;
	}
	public function drop($k) {
		unset($this->vars[$k]);
		return $this;
	}
	public function set($k, $v) {
		$this->vars[$k] = $v;
		return $this;
	}
	public function toUrl() {
		return $this->prefix . '?' . http_build_query($this->vars);
	}
}