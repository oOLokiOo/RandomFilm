<?php

/**
 * RandomFilm.php
 * 
 * @author Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com>
 * @version 2.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php/v2.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v3.0
 */

class RandomFilm {
	private $random_movie = null;

	const ERR_XML_NOT_FOUND = 1;
	const ERR_CANT_PARSE_STRING = 2;

	private $errors_arr = array(
		'1' => 'User Films Xml file - not Found',
		'2' => 'String could not be parsed as XML'
		);

	private $google_images_url 				= 'http://www.google.by/search?q=';
	private $google_images_url_end_prefix 	= '&source=lnms&tbm=isch&sa=X'; // &ved=???

	private $en_search_prefix 	= 'kinopoisk.ru'; // film poster
	private $ru_search_prefix 	= 'kinopoisk.ru'; // фильм постер

	private $USER_XML_PATH = '';
	
	private $search_movie_title = '';


	public $get_large_images 	= true; // It makes the process slower...
	public $image_url 			= '';
	public $h1_title 			= '';
	public $error 				= '';


	public function __construct($USER_XML_PATH = '') {
		$this->USER_XML_PATH = $USER_XML_PATH; // TODO: link User Class here
		$this->random_movie = $this->get_random_movie();

		if ($this->random_movie != null) {
			$this->search_movie_title = $this->get_search_movie_title();
			$this->image_url = $this->get_image_url();
			$this->h1_title = $this->get_h1_title();
		}
	}


	private function get_from_images_google($search_words = '') {
		$parser = new Inc\KinopoiskParser\Parser();

		$url = $this->google_images_url.urlencode($search_words).$this->google_images_url_end_prefix;
		$image_url = '';

		// get thumb from google search by images 
		$page = $parser->getSubsidiaryPage($url);
		$dom = $page->dom;
		$result = $dom->find('#search .images_table a', 0);
		$google_image_href = $result->attr['href'];
		$google_image_thumb = $result->children[0]->attr['src'];

		// get big iamge from kinopoisk.ru
		if ($this->get_large_images == true && strpos($google_image_href, 'kinopoisk.ru') !== false) {
			$google_image_href = substr($google_image_href, 7, strlen($google_image_href)-1); // crop "/url?q=" from redirect url

			$res = $parser->getFilmByDirectUrl($google_image_href);
			$image_url = $res->data->img;
		}

		if ($image_url == '') {
			$image_url = $google_image_thumb;
			$this->get_large_images = false;
		}

		return $image_url;
	}

	private function set_error($error_num = null) {
		if (is_numeric($error_num)) {
			$this->error = $this->errors_arr[$error_num];
			return true;
		}

		return false;
	}


	public function d($data, $die = false) {
		echo '<pre>';
		print_r($data);
		var_dump($data);
		echo '</pre>';

		if ($die != false) die();

		return true;
	}

	public function get_random_movie() {
		$random_movie = null;

		@$xml = file_get_contents($this->USER_XML_PATH);

		if ($xml) {
			//@$xmlData = new SimpleXMLElement($xml);
			@$xmlData = simplexml_load_string($xml);

			if ($xmlData) $random_movie = $xmlData->film[rand(0, count($xmlData) - 1)];
			else $this->set_error(1);
		}
		else $this->set_error(0);

		return $random_movie;
	}

	public function get_search_movie_title() {
		if ($this->search_movie_title != '') return $this->search_movie_title;
		if ($this->random_movie == null) $this->random_movie = $this->get_random_movie();

		$search_movie_title = ($this->random_movie->en != '' ? $this->random_movie->en . ' ' : '')
			. ($this->random_movie->ru != '' ? $this->random_movie->ru . ' ' : '')
			//. ($this->random_movie->year != '' ? $this->random_movie->year . ' ' : '')
			. $this->en_search_prefix; // RU_SEARCH_PREFIX - bad result with "RU"

		return $search_movie_title;
	}

	public function get_image_url() {
		if ($this->image_url != '') return $this->image_url;
		if ($this->search_movie_title != '') $this->search_movie_title = $this->get_search_movie_title();

		//$images_arr = $this->get_from_images_google($this->search_movie_title);
		//$image_url = $this->filter_from_blocked_resources($images_arr, $this->BLOCKED_RESOURCES);
		$image_url = $this->get_from_images_google($this->search_movie_title);

		return $image_url;
	}

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
