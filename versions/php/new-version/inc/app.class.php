<?php

/**
 * Common Class for my project - "random-film".
 * 
 * @author Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com>
 * @version 2.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php
 */

require_once 'curl.php';
require_once 'simple_html_dom.php';


class APP {
	private $errors_arr = array(
		'0' => 'User Films Xml file - not Found',
		'1' => 'String could not be parsed as XML'
		);

	/**
	 * @see Commented properties & methods are DEPRECATED! https://developers.google.com/web-search/docs/
	 */
	//private $GOOGLE_IMAGES_URL 		= 'http://ajax.googleapis.com/ajax/services/search/images?';
	//private $CURL_REQUEST_ATTEMPT 	= 5;
	//private $BLOCKED_RESOURCES = array(
	//	'www.impawards.com',
	//	'en.wikipedia.org'
	//);
	private $GOOGLE_IMAGES_URL 				= 'https://www.google.by/search?q=';
	private $GOOGLE_IMAGES_URL_END_PREFIX 	= '&source=lnms&tbm=isch&sa=X'; // &ved=???

	private $EN_SEARCH_PREFIX 	= 'kinopoisk.ru'; // film poster
	private $RU_SEARCH_PREFIX 	= 'kinopoisk.ru'; // фильм постер

	private $XML_PATH = '';
	
	private $random_movie 		= null;
	private $search_movie_title = '';


	public $get_large_images 	= true; // It makes the process slower...
	public $image_url 			= '';
	public $h1_title 			= '';
	public $error 				= '';


	/**
	 * @param string $XML_PATH path to XML file with movies.
	 */
	public function __construct($XML_PATH = '') {
		$this->XML_PATH = $XML_PATH;
		$this->random_movie = $this->get_random_movie();
		
		if ($this->random_movie != null) {
			$this->search_movie_title = $this->get_search_movie_title();
			$this->image_url = $this->get_image_url();
			$this->h1_title = $this->get_h1_title();
		}
	}

	/**
	 * @throws 	FALSE & Echo CURL WARNING in case of error.
	 * 
	 * @param 	string 	$search_words 	title for Google Search.
	 * 
	 * @return 	array|boolean 	result of decoded json array or FALSE.
	 *
	 * @see Commented properties & methods are DEPRECATED! https://developers.google.com/web-search/docs/
	 */
	/*
	private function get_from_images_google($search_words = '') {
		$manual_referer = 'http://google.com/';

		$args = array(
			'v' => '1.0',
			'q' => $search_words,
			'imgsz' => 'large',
			'rsz' => 8
		);

		$url = $this->GOOGLE_IMAGES_URL;

		foreach ($args as $key => $val) {
			$url .= $key.'='.rawurlencode($val).'&';
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, $manual_referer);

		$body = curl_exec($ch);
		$response = curl_getinfo($ch);

		$attempt = $this->CURL_REQUEST_ATTEMPT;

		if ($response['http_code'] != 200) {
			while ($attempt > 0) {
				$body = curl_exec($ch);
				$response = curl_getinfo($ch);
				if ($response['http_code'] == 200) break;
				$attempt--;
			}
		}

		curl_close($ch);

		if ($attempt <= 0) {
			echo 'WARNING: CURL cant do request. Do something...';
			return false;
		}

		$json = json_decode($body, true);

		return $json['responseData']['results'];
	}
	*/

	/**
	 *
	 */
	private function get_from_images_google($search_words = '') {
		$curl = new Curl();
		$url = $this->GOOGLE_IMAGES_URL.urlencode($search_words).$this->GOOGLE_IMAGES_URL_END_PREFIX;
		$image_url = '';

		// get thumb from google search by images 
		$html = $curl->get($url);
		$dom = str_get_html($html);
		$result = $dom->find('#search .images_table a', 0);
		$google_image_href = $result->attr['href'];
		$google_image_thumb = $result->children[0]->attr['src'];

		//data:image/jpeg;base64,
		//echo($url); die();
		//$this->d($html, 1);

		// get big iamge from kinopoisk.ru
		if ($this->get_large_images == true && strpos($google_image_href, 'kinopoisk.ru') !== false) {
			$google_image_href = substr($google_image_href, 7, strlen($google_image_href)-1); // crop "/url?q=" from redirect url
			$html = $curl->get($google_image_href);
			$dom = str_get_html($html);
			$img = $dom->find('#photoBlock .popupBigImage img, #photoBlock img', 0);
			if ($img) $image_url = $img->src;
		}

		if ($image_url == '') {
			$image_url = $google_image_thumb;
			$this->get_large_images = false;
		}

		return $image_url;
	}

	/**
	 * @param 	array 	$imges_arr 			array of images from result after Google Search.
	 * @param 	array 	$BLOCKED_RESOURCES 	array with resourses witch blocks their images for parsing.
	 * 
	 * @return 	string 	result of decoded json array
	 *
	 * @see Commented properties & methods are DEPRECATED! https://developers.google.com/web-search/docs/
	 */
	/*
	private function filter_from_blocked_resources($imges_arr = array(), $BLOCKED_RESOURCES = array()) {
		$good_url = '';

		for ($i = 0; $i < count($imges_arr); $i++) {
			if (!in_array($imges_arr[$i]['visibleUrl'], $BLOCKED_RESOURCES)) {
				$good_url = $imges_arr[$i]['url'];
				break;
			}
		}

		return (isset($imges_arr[0]['url']) && $good_url == '' ? $imges_arr[0]['url'] : $good_url); 
	}
	*/

	/**.
	 * @param 	int 	$error_num 	nuber of error from error array.
	 * 
	 * @return 	boolean TRUE, if error was set. FALSE, if not.
	 */
	private function set_error($error_num = null) {
		if (is_numeric($error_num)) {
			$this->error = $this->errors_arr[$error_num];
			return true;
		}

		return false;
	}


	/**
	 * @param 	mixed 	$data 	data for debuging.
	 * @param 	boolean $die 	param for stop running script after debug. defalt - FALSE.
	 * 
	 * @return 	boolean TRUE, anyway.
	 */
	public function d($data, $die = false) {
		echo '<pre>';
		print_r($data);
		var_dump($data);
		echo '</pre>';

		if ($die != false) die();

		return true;
	}

	/**
	 * @return 	object|null object with parsed movie data from XML file or NULL.
	 */
	public function get_random_movie() {
		$random_movie = null;

		@$xml = file_get_contents($this->XML_PATH);

		if ($xml) {
			//@$xmlData = new SimpleXMLElement($xml);
			@$xmlData = simplexml_load_string($xml);

			if ($xmlData) $random_movie = $xmlData->film[rand(0, count($xmlData) - 1)];
			else $this->set_error(1);
		}
		else $this->set_error(0);

		return $random_movie;
	}

	/**
	 * @return 	string title of movie for Google serach, to get it poster image.
	 */
	public function get_search_movie_title() {
		if ($this->search_movie_title != '') return $this->search_movie_title;
		if ($this->random_movie == null) $this->random_movie = $this->get_random_movie();

		$search_movie_title = ($this->random_movie->en != '' ? $this->random_movie->en . ' ' : '')
			. ($this->random_movie->ru != '' ? $this->random_movie->ru . ' ' : '')
			//. ($this->random_movie->year != '' ? $this->random_movie->year . ' ' : '')
			. $this->EN_SEARCH_PREFIX; // RU_SEARCH_PREFIX - bad result with "RU"

		return $search_movie_title;
	}

	/**
	 * @return 	string filter Google Search results and return single image from it.
	 */
	public function get_image_url() {
		if ($this->image_url != '') return $this->image_url;
		if ($this->search_movie_title != '') $this->search_movie_title = $this->get_search_movie_title();

		//$images_arr = $this->get_from_images_google($this->search_movie_title);
		//$image_url = $this->filter_from_blocked_resources($images_arr, $this->BLOCKED_RESOURCES);
		$image_url = $this->get_from_images_google($this->search_movie_title);

		return $image_url;
	}

	/**
	 * @return 	string filtered $this->search_movie_title.
	 */
	public function get_h1_title() {
		if ($this->h1_title != '') return $this->h1_title;
		if ($this->random_movie == null) $this->random_movie = $this->get_random_movie();

		$h1_title = ($this->random_movie->ru ? $this->random_movie->ru . ' | ' : '')
			. ($this->random_movie->en ? $this->random_movie->en . ' | ' : '')
			. ($this->random_movie->year ? $this->random_movie->year : '');

		//if (mb_substr($h1_title, -2) == "| ") $h1_title = mb_substr($h1_title, 0, mb_strlen($h1_title, -2));

		return $h1_title;
	}
}