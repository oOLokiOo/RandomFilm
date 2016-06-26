<?php

require_once 'curl.php';
require_once 'simple_html_dom.php';


class KinopoiskParser {
	private $_dom = null;
	private $_film_id = 0;

	private $main_domen 		= 'http://www.kinopoisk.ru';
	private $no_image_prefix 	= 'poster_none.png';
	private $film_prefix 		= '/film/';
	private $search_page_url 	= '/index.php?first=no&what=&kp_query=';
	private $errors_arr = array(
		'0' => 'Nothing was found...',
		'1' => 'Enter Film Title',
		'2' => 'Cant find Film ID when parsing top results url by $search_query...',
		'3' => 'ERROR! Wrong parser settings in config... Film ID is - 0'
		);


	public $search_query 	= '';
	public $direct_url 		= '';

	public $web_version 	= false;
	public $save_result 	= true;
	public $result_folder 	= '/result/';
	//public $start_from_id 	= 0; // max - http://www.kinopoisk.ru/film/555400 ? in future plans...

	public $logging 		= true;
	public $log_result 		= '/logs/result.log';
	public $log_error 		= '/logs/error.log';

	public $wait_to_redirect_time = 5; // in sec. to avoid blocking of the frequent requests.

	public $result = array(
		'error'				=> '',
		'detail_page_url'	=> '',
		'id' 				=> '',
		'ru'				=> '',
		'en'				=> '',
		'year'				=> '',
		'country'			=> '',
		'country_arr' 		=> array(),
		'producer'			=> '',
		'producer_arr' 		=> array(),
		'genre'				=> '',
		'genre_arr'			=> array(),
		'budget' 			=> '',
		'budget_usa' 		=> '',
		'budget_world' 		=> '',
		'premiere_world' 	=> '',
		'premiere_rf' 		=> '',
		'time'				=> '',
		'starring'			=> '',
		'starring_arr'		=> array(),
		'img'				=> ''
		);


	public function __construct() {
		// do something...
	}

	public function setup() {
//var_dump('in setup');
		$curl = new Curl();
		$response = null;

		if ($this->direct_url != '') {
			$this->_film_id = $this->get_film_id_from_url($this->direct_url);
			$this->result['detail_page_url'] = $this->main_domen.$this->film_prefix.$this->_film_id;
		}
		else if ($this->web_version === true) { 
			if ($this->search_query == '') {
				$this->result['error'] = $this->errors_arr[1];
				//return;
			}

			// get first film url from top search results by $search_query
			$top_search_result = null;

			$response = $curl->get($this->main_domen.$this->search_page_url.urldecode($this->search_query));
			$this->_dom = str_get_html($response);

			$top_search_result = $this->_dom->find('.search_results .name a', 0);
			if (isset($top_search_result) && $top_search_result != null) {
				$this->result['detail_page_url'] = $this->main_domen.$top_search_result->href;

				$this->_film_id = $this->get_film_id_from_url($this->result['detail_page_url']);

				if ($this->_film_id == 0) {
					$this->log('Cant find Film ID when parsing top results url by $search_query... URL - '.$this->result['detail_page_url']);
					$this->result['error'] = $this->errors_arr[2];
				}

			} else $this->result['error'] = $this->errors_arr[0];
		}
		else if ($this->web_version === false) {
			$this->_film_id = file_get_contents(ROOT.$this->log_result);
			$this->_film_id = ($this->_film_id == '' ? 1 : $this->_film_id+1);

			$this->result['detail_page_url'] = $this->main_domen.$this->film_prefix.$this->_film_id;
		}

		// do pasing
		if ($this->_film_id > 0) {
			$response = $curl->get($this->result['detail_page_url']);
			$this->_dom = str_get_html($response);
		}
		//else $this->result['error'] = $this->errors_arr[3];
	}

	public function process() {
//var_dump('in process');
		if (!isset($this->_dom) || $this->_dom == null) $this->setup();

		$this->result['id'] = $this->_film_id;

		// parse left column info
		$ru = $this->_dom->find('#headerFilm h1, #headerPeople h1', 0);
		if ($ru) $this->result['ru'] = $this->decode($ru->innertext);

		$en = $this->_dom->find('#headerFilm span, #headerPeople span', 0);
		if ($en) $this->result['en'] = $this->decode($en->innertext);

		// parse middle column info
		foreach ($this->_dom->find('#infoTable table tr') as $tr_content) {
			$td_title = $tr_content->find('td', 0);
			$mb_td_title = $this->decode($td_title->innertext);

			$td_value = $tr_content->find('td', 1);
			$mb_td_value = $this->decode($td_value->innertext);

			// TODO: add the ability to parse each element separately. own class method for each element.  
			switch ($mb_td_title) {
				case 'год':
					$a = $td_value->find('a', 0);
					$this->result['year'] = $this->decode($a->innertext);
					break;

				case 'страна':
					foreach ($td_value->find('a') as $a) {
						$a_mb = $this->decode($a->innertext);
						$this->result['country_arr'][] = $a_mb;
					}
					$this->result['country'] = implode(',', $this->result['country_arr']);
					break;

				case 'режиссер':
					foreach ($td_value->find('a') as $a) {
						$a_mb = $this->decode($a->innertext);
						$this->result['producer_arr'][] = $a_mb;
					}
					$this->result['producer'] = implode(',', $this->result['producer_arr']);
					break;

				case 'жанр':
					foreach ($td_value->find('span a') as $a) {
						$a_mb = $this->decode($a->innertext);
						$this->result['genre_arr'][] = $a_mb;
					}
					$this->result['genre'] = implode(',', $this->result['genre_arr']);
					break;

				case 'бюджет':
					$a = $td_value->find('a', 0);
					if ($a) $this->result['budget'] = $this->decode($a->innertext);
					break;

				case 'сборы в США':
					$a = $td_value->find('a', 0);
					$this->result['budget_usa'] = $this->decode($a->innertext);
					break;

				case 'сборы в мире':
					$a = $td_value->find('a', 0);
					$this->result['budget_world'] = $this->decode($a->innertext);
					break;

				case 'премьера (мир)':
					$a = $td_value->find('a', 0);
					$a_mb = $this->decode($a->innertext);
					$this->result['premiere_world'] = $a_mb;
					break;

				case 'премьера (РФ)':
					$a = $td_value->find('a', 0);
					$a_mb = $this->decode($a->innertext);
					$this->result['premiere_rf'] = $a_mb;
					break;

				case 'время':
					$this->result['time'] = strip_tags($mb_td_value);
					break;
			}
		}

		// parse right column info
		$starring = $this->_dom->find('#actorList ul', 0);
		if ($starring) {
			$this->result['starring'] = $this->decode($starring->innertext);

			foreach ($starring->find('li') as $li_content) {
				$this->result['starring_arr'][] = $this->decode(strip_tags($li_content->innertext));
			}
			array_pop($this->result['starring_arr']);
			$this->result['starring'] = implode(',', $this->result['starring_arr']);
		}

		// parse image
		$img = $this->parse_image();

		// save all data to DB & HDD
		if ($this->save_result === true && $this->web_version == false) {
/*
var_dump($this->decode($this->_dom->find('title', 0)->innertext));
die();

// 28011 - result.txt
d($this->result['id']);
d($this->result, 1);
*/
			if ($this->result['en'] == '' && $this->result['ru'] == '') {
				// or <title> ($this->decode($this->_dom->find('title', 0)->innertext)) == '404: Страница не найдена - Кинопоиск.ru'
				$this->log('Film not found! 404 ERROR! URL - '.$this->result['detail_page_url']);
			} else {
				$image_path = ROOT.$this->result_folder.$this->_film_id.'.jpg';
				if ($img) {
					$tmp = explode('/', $img->src);
					if ($tmp[count($tmp)-1] != $this->no_image_prefix) file_put_contents($image_path, file_get_contents($img->src));
				}

				$this->result['_id'] = $this->result['id'];
//d($this->result, 1);
				$mongo = new MongoClient();
				$collection = $mongo->kinopoisk->movies->films;
				try {
					$collection->save($this->result);
				} catch (MongoException $e) {
					d($this->result);
					die('<h1>MongoException Error: code - '.$e->getCode().'. ObjectID: '.$this->_film_id.'</h1>');
				}
				$mongo->close();
			}
//die("loging to log file");
			$this->log($this->_film_id, 'result');
		}

		if ($this->web_version === false) $this->do_redirect();
	}

	public function parse_image() {
		$img = null;

		$img = $this->_dom->find('#photoBlock .popupBigImage img, #photoBlock img', 0);
		if ($img) $this->result['img'] = $img->src;

		return $img;
	}


	private function get_film_id_from_url($url = '') {
		$film_id = 0;
		$tmp_arr = array();

		$tmp_arr = explode('/film/', $url);
		$film_id = explode('/', $tmp_arr[1])[0];
		$film_id = (is_numeric($film_id) ? $film_id : 0);

		return $film_id;
	}

	private function decode($str = '') {
		$str = mb_convert_encoding($str, 'UTF-8', 'Windows-1251');
		$str = html_entity_decode($str);
		$str = trim($str);
		$str = preg_replace('/\s+/', ' ', $str);

		return $str;
	}

	private function log($str = '', $label = 'error') {
		if ($this->logging === true) {
			if ($label == 'result') file_put_contents(ROOT.$this->log_result, $str);
			else file_put_contents(ROOT.$this->log_error, 
				strtoupper($label).': ( '.date('H:i:s d.m.Y', time()).' ) ::: '
				.$str."\n\n",
				FILE_APPEND
				);
		}
	}

	private function do_redirect() {
		$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
		require_once ROOT.'/tpl/redirect.tpl';
		exit();
	}
}