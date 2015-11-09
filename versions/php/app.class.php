<?php

/**
 * Common Class for my project - "random-film". All in one Class, without different files with scripts.
 * 
 * @author Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com>
 * @version 1.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php
 */

Class APP {
	private $rand 	= 0;
	private $error 	= '';
	private $errors_arr = array(
		'0' => 'Xml file not Found',
		'1' => 'String could not be parsed as XML'
		);

	private $GOOGLE_IMAGES_URL 		= 'http://ajax.googleapis.com/ajax/services/search/images?';
	private $CURL_REQUEST_ATTEMPT 	= 5;
	private $BLOCKED_RESOURCES = array(
		'www.impawards.com',
		'en.wikipedia.org'
	);

	public $EN_SEARCH_PREFIX 	= 'film poster';
	public $RU_SEARCH_PREFIX 	= 'фильм постер';

	public $XML_PATH = '../../users/1/films.xml';

	public $random_movie 		= null;
	public $search_movie_title 	= '';
	public $image_url 			= '';
	public $h1_title 			= '';


	function __construct() {
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
	 * @return 	array 	result of decoded json array.
	 */
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
			$url .= $key . '=' . rawurlencode($val) . '&';
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

	/**
	 * @param 	array 	$imges_arr 			array of images from result after Google Search.
	 * @param 	array 	$BLOCKED_RESOURCES 	array with resourses witch blocks their images for parsing.
	 * 
	 * @return 	string 	result of decoded json array
	 */
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

	/**.
	 * @param 	int 	$error_num 	nuber of error from error array.
	 * 
	 * @return 	boolean 	TRUE, if error was set. FALSE, if not.
	 */
	private function set_error($error_num = null) {
		if ($error_num != null && is_numeric($error_num)) {
			$this->error = $this->errors_arr[$error_num];
			return true;
		}

		return false;
	}


	/**
	 * @param 	mixed 	$data 	data for debuging.
	 * @param 	boolean 	$die 	param for stop running script after debug. defalt - FALSE.
	 * 
	 * @return 	boolean 	TRUE, anyway.
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
	 * @return 	object|null 	object with parsed movie data from XML file or NULL.
	 */
	public function get_random_movie() {
		$random_movie = null;
		
		$xml = file_get_contents($this->XML_PATH);

		if ($xml) {
			//@$xmlData = new SimpleXMLElement($xml);
			@$xmlData = simplexml_load_string($xml);

			if ($xmlData) {
				$this->rand = rand(0, count($xmlData) - 1);
				$random_movie = $xmlData->film[$this->rand];
			}
			else $this->set_error(1);
		}
		else $this->set_error(0);

		return $random_movie;
	}

	/**
	 * @return 	string 	title of movie for Google serach, to get it poster image.
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
	 * @return 	string 	filter Google Search results and return single image from it.
	 */
	public function get_image_url() {
		if ($this->image_url != '') return $this->image_url;
		if ($this->search_movie_title != '') $this->search_movie_title = $this->get_search_movie_title();

		$images_arr = $this->get_from_images_google($this->search_movie_title);
		$image_url = $this->filter_from_blocked_resources($images_arr, $this->BLOCKED_RESOURCES);

		return $image_url;
	}

	/**
	 * @return 	string 	filtered $this->search_movie_title.
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