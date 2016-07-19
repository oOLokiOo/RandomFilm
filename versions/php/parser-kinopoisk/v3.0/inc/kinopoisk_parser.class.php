<?php

/**
 * kinopoisk_parser.class.php
 * 
 * @author Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com>
 * @version 3.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v3.0
 * @see https://github.com/RubtsovAV/php-curl-lib
 * @see http://simplehtmldom.sourceforge.net
 */

require_once 'curl.php';
require_once 'simple_html_dom.php';


class KinopoiskParser {
	private $_dom = null;
	private $_film_id = 0;
	private $_project_root = '';

	private $main_domen 		= 'http://www.kinopoisk.ru';
	private $search_page_url 	= '/index.php?first=no&what=&kp_query=';
	private $film_prefix_in_url	= '/film/';
	private $no_image_prefix 	= 'poster_none.png';
	private $errors_arr = array(
		'0' => 'Enter Film Title',
		'1' => 'Nothing was found...',
		'2' => 'Can\'t find Film ID when parsing URL...',
		'3' => 'Can\'t open page with founded Film ID when parsing page by URL...',
		'4' => 'Enter Film URL',
		);

	private $result = array(
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


	public $css_selector_top_search_result 	= '.search_results .name a';
	public $css_selector_starring 			= '#actorList ul';
	public $css_selector_image 				= '#photoBlock .popupBigImage img, #photoBlock img';

	public $wait_to_redirect_time 	= 5; // in sec. to avoid blocking of the frequent requests.
	public $save_result 			= false;
	public $result_folder 			= '/result/';

	public $logging 	= true;
	public $log_result 	= '/logs/result.log';
	public $log_error 	= '/logs/error.log';


	public function __construct($project_root = '') {
		$this->_project_root = ($project_root != '') ? $project_root : __DIR__.'/../';
	}


	/**
	 * @return 	array 	array of kinopoisk.ru all parsed result.
	 */
	private function getParserResult() {
		return $this->result;
	}

	/**
	 * @param 	string $url 	kinopoisk.ru full film url. default - ''.
	 * @return 	boolean 		TRUE, OR FALSE if bad $url.
	 */
	private function getPageDom($url = '') {
		if ($url == '') {
			$this->setError(4);
			return false;
		}

		try {
			$curl = new Curl();
			$response = null;
			$response = $curl->get($url);
		} catch (Exception $e) {
			$this->result['error'] = 'CurlException: '.$e->getMessage();
			return false;
		}

		$this->_dom = str_get_html($response);

		return true;
	}

	/**
	 * @param 	string $url 	kinopoisk.ru full film url. default - ''.
	 * @return 	int 			film ID if found OR 0.
	 */
	private function getFilmIdFromUrl($url = '') {
		$film_id = 0;
		$tmp_arr = array();

		$tmp_arr = explode('/film/', $url);
		if (isset($tmp_arr[1])) $film_id = explode('/', $tmp_arr[1])[0];
		$film_id = (is_numeric($film_id) ? $film_id : 0);

		return $film_id;
	}

	/**
	 * @param 	string $str 	kinopoisk.ru parsed category title. default - ''.
	 * @return 	string 			decoded title.
	 */
	private function decode($str = '') {
		$str = mb_convert_encoding($str, 'UTF-8', 'Windows-1251');
		$str = html_entity_decode($str);
		$str = trim($str);
		$str = preg_replace('/\s+/', ' ', $str);

		return $str;
	}

	/**
	 * @param 	string 			$str 	record for the log file. default - ''.
	 * @param 	string/tinyint 	$label 	type of record - 'error' OR 'result'. default - 'error'.
	 */
	private function log($str = '', $label = 'error') {
		if ($this->logging === true && $str != '') {
			if ($label == 'result') file_put_contents($this->_project_root.$this->log_result, $str);
			else file_put_contents($this->_project_root.$this->log_error, 
				strtoupper($label).': ( '.date('H:i:s d.m.Y', time()).' ) ::: '
				.$str."\n\n",
				FILE_APPEND
				);
		}
	}

	/**
	 * @param 	int 	$error_number 	number from project errors array.
	 * @return 	boolean 				TRUE, if error was found in project array and setted OR FALSE.
	 */
	private function setError($error_number) {
		if (is_numeric($error_number) && isset($this->errors_arr[$error_number])) {
			$this->result['error'] = $this->errors_arr[$error_number];

			switch ($error_number) {
				case '2':
					$this->log('Cant find Film ID when parsing URL... URL - '.$this->result['detail_page_url']);
					return true;
				case '3':
					$this->log('Cant open page with founded Film ID when parsing page by URL... URL - '.$this->result['detail_page_url']);
					return true;
			}
		}

		return false;
	}


	private function setup($url) {
		return (($this->_dom == null && $url != '') ? $this->getPageDom($url) : false);
	}

	private function process() {
		if ($this->_film_id > 0) {
			// get DOM of detail page
			$this->getPageDom($this->result['detail_page_url']);

			if ($this->_dom != null) {
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
				$starring = $this->findStarring($this->result['detail_page_url']);

				// parse image
				$img = $this->findImage($this->result['detail_page_url']);

				// save all data to DB & HDD
				if ($this->save_result === true) {
					// check for 404 page
					if ($this->result['en'] == '' && $this->result['ru'] == '') {
						// OR <title> ($this->decode($this->_dom->find('title', 0)->innertext)) == '404: Страница не найдена - Кинопоиск.ru'
						// TODO: check on top of process() method
						$this->log('Film not found! 404 ERROR! URL - '.$this->result['detail_page_url']);
					} else {
						// save image to DB
						$image_path = $this->_project_root.$this->result_folder.$this->_film_id.'.jpg';
						if ($img) {
							$tmp = explode('/', $img->src);
							if ($tmp[count($tmp)-1] != $this->no_image_prefix) file_put_contents($image_path, file_get_contents($img->src));
						}

						// add all parsed data to DB
						$this->result['_id'] = $this->result['id'];
						
						//$this->d($this->result, 1);
						
						$mongo = new MongoClient();
						$collection = $mongo->kinopoisk->movies->films;
						try {
							$collection->save($this->result);
						} catch (MongoException $e) {
							$this->d($this->result);
							die('<h1>MongoException Error: code - '.$e->getCode().'. ObjectID: '.$this->_film_id.'</h1>');
						}
						$mongo->close();
					}
					// write ID of new added to the DB film to log result file
					$this->log($this->_film_id, 'result');
				}
			}
			else $this->setError(3);
		}
		else $this->setError(2);
	}


	/**
	 * My own Debug function.
	 *
	 * @param 	mixed 	$data 	data for debugging.
	 * @param 	boolean $die 	parameter for stop running script after debug. default - FALSE.
	 * @return 	boolean 		TRUE, OR Die().
	 */
	public function d($data, $die = false) {
		echo '<pre>';
		print_r($data);
		//var_dump($data);
		echo '</pre>';

		if ($die !== false) die();

		return true;
	}

	/**
	 * @param 	string 	$url 	kinopoisk.ru full film url. default - ''.
	 * @return 	SimpleHtmlDom  	DOM object, OR null	
	 */
	public function findStarring($url = '') {
		$starring = null;

		if ($this->setup($url)) {
			$starring = $this->_dom->find($this->css_selector_starring, 0);
			if ($starring) {
				$this->result['starring'] = $this->decode($starring->innertext);

				foreach ($starring->find('li') as $li_content) {
					$this->result['starring_arr'][] = $this->decode(strip_tags($li_content->innertext));
				}

				array_pop($this->result['starring_arr']);
				$this->result['starring'] = implode(',', $this->result['starring_arr']);
			}
		}

		return $starring;
	}

	/**
	 * @param 	string 	$url 	kinopoisk.ru full film url. default - ''.
	 * @return 	SimpleHtmlDom  	DOM object, OR null	
	 */
	public function findImage($url = '') {
		$img = null;

		if ($this->setup($url)) {
			$img = $this->_dom->find($this->css_selector_image, 0);
			if ($img) $this->result['img'] = $img->src;
		}

		return $img;
	}

	/**
	 * @param 	string 	$query 	film title for searching in top kinopoisk.ru results. default - ''.
	 * @return 	array 			array of kinopoisk.ru all parsed results.
	 */
 	public function getFilmBySearchQuery($query = '') {
		if ($query == '') $this->setError(0);
		else {
			$top_search_result = null;
			$this->getPageDom($this->main_domen.$this->search_page_url.urldecode($query));

			// get first film url from top search results by $search_query
			$top_search_result = $this->_dom->find($this->css_selector_top_search_result, 0);
			if (isset($top_search_result) && $top_search_result != null) {
				// get detail film page url
				$this->result['detail_page_url'] = $this->main_domen.$top_search_result->href;
				$this->_film_id = $this->getFilmIdFromUrl($this->result['detail_page_url']);

				// parse page data
				$this->process();
			}
			else $this->setError(1);
		}

		return $this->getParserResult();
 	}

	/**
	 * @param 	string 	$url 	kinopoisk.ru full film url. default - ''.
	 * @return 	array 			array of kinopoisk.ru all parsed results.
	 */
	public function getFilmByDirectUrl($url = '') {
		if ($url == '') $this->setError(4);
		else {
	 		// get film ID from direct url
			$this->_film_id = $this->getFilmIdFromUrl($url);
			$this->result['detail_page_url'] = $this->main_domen.$this->film_prefix_in_url.$this->_film_id;

			// parse page data
			$this->process();
		}

		return $this->getParserResult();
	}

	public function parseAllSite() {
		// get film ID from $this->log_result
		// TODO: check file_get_contents() for try-catch
		$this->_film_id = file_get_contents($this->_project_root.$this->log_result);
		$this->_film_id = ($this->_film_id == '' ? 1 : $this->_film_id+1);
		$this->result['detail_page_url'] = $this->main_domen.$this->film_prefix_in_url.$this->_film_id;

		// parse page data
		$this->process();

		if (isset($this->result['error']) && strlen($this->result['error'])) $this->d($this->result['error'], 1);

		// do redirect to index page with random parameter to avoid the ban by browser because of recursion
		//$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
		// TODO: check require_once() for try-catch & add redirect.tpl to public method
		require_once $this->_project_root.'/tpl/redirect.tpl';
		exit();
	}
}