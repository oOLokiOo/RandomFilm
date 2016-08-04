<?php

namespace Helpers;
use stdClass;

require_once 'Lib/curl.php';
require_once 'Lib/simple_html_dom.php';

class Page {
	private $curl = null;

	// result -> response
	// result -> error
	// result -> dom	
	private $result;


	public function __construct() {
		$this->curl = new \Curl();

		$this->reset();
	}


	private function reset() {
		$this->result = new stdClass();
	}


	public function get($url = '') {
		$this->reset();

		try {
			$this->result->response = $this->curl->get($url);
		} catch (Exception $e) {
			$this->result->error = 'CurlException: '.$e->getMessage();
			return $this->result;
		}

		$this->result->dom = str_get_html($this->result->response);

		return $this->result;
	}

	public function find($selector = '', $count = null) {
		return ($this->result->dom != null ? $this->result->dom->find($selector, $count) : null);
	}
}
