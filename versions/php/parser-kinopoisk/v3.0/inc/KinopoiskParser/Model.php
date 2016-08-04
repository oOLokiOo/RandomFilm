<?php

namespace KinopoiskParser;

class Model {
	public $_id 			= 0; // just MongoDB feature
	public $id 				= 0;
	public $detail_page_url = '';
	public $ru 				= '';
	public $en 				= '';
	public $year 			= '';
	public $country 		= '';
	public $country_arr 	= array();
	public $producer 		= '';
	public $producer_arr 	= array();
	public $genre 			= '';
	public $genre_arr 		= array();
	public $budget 			= '';
	public $budget_usa 		= '';
	public $budget_world 	= '';
	public $premiere_world 	= '';
	public $premiere_rf 	= '';
	public $time 			= '';
	public $starring 		= '';
	public $starring_arr 	= array();
	public $img 			= '';


	public function __construct() {
		// do something...
	}


	public function __set($key, $val) {
		throw new \Exception('Can\'t push property!');
	}

	public function __get($key) {
		throw new \Exception('Property does not exist!');
	}
}
