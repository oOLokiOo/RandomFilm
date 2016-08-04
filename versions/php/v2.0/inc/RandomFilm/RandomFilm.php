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
	private $result;

	private $USER_XML_PATH = '';

	private $google_images_url 				= 'http://www.google.by/search?q=';
	private $google_images_url_end_prefix 	= '&source=lnms&tbm=isch&sa=X'; // &ved=???

	private $en_search_prefix 	= 'kinopoisk.ru'; // film poster
	private $ru_search_prefix 	= 'kinopoisk.ru'; // фильм постер

	const ERR_XML_NOT_FOUND = 1;
	const ERR_CANT_PARSE_STRING = 2;

	private $errors_arr = array(
		'1' => 'User Films Xml file - not Found',
		'2' => 'String could not be parsed as XML'
		);

	
	private $search_movie_title = '';


	public $random_film 		= null;

	public $get_large_images 	= true; // It makes the process slower...
	public $image_url 			= '';
	public $h1_title 			= '';
	public $error 				= '';


	public function __construct($USER_XML_PATH = '') {
		$this->USER_XML_PATH = $USER_XML_PATH; // TODO: link User Class here
		$this->random_film = $this->getRandomFilm();

		if ($this->random_film != null) {
			$this->search_movie_title = $this->getSearchMovieTitle();
			$this->image_url = $this->getImageUrl();
			$this->h1_title = $this->getH1Title();
		}
	}


	private function getFromImagesGoogle($search_words = '') {
		$parser = new \Parser\KinopoiskParser();
		$parser->setLogErrorPath(__DIR__.'/../../logs/error.log');

		$url = $this->google_images_url.urlencode($search_words).$this->google_images_url_end_prefix;
		$image_url = '';

		// get thumb from google search by images 
		$page = $parser->getSubsidiaryPage($url);
		$dom = $page->dom;
		$result = $dom->find('#search .images_table a', 0);
		$google_image_href = $result->attr['href'];
		$image_url = $result->children[0]->attr['src'];

		// get big iamge from kinopoisk.ru
		if ($this->get_large_images == true && strpos($google_image_href, 'kinopoisk.ru') !== false) {
			$google_image_href = substr($google_image_href, 7, strlen($google_image_href)-1); // crop "/url?q=" from redirect url

			$res = $parser->getFilmByDirectUrl($google_image_href);
			if (!count($res->errors)) $image_url = $res->data->img;
			else $this->error = $res->errors[0];
		}

		return $image_url;
	}

	private function setError($error_num = null) {
		if (is_numeric($error_num)) {
			$this->error = $this->errors_arr[$error_num];
			return true;
		}

		return false;
	}


	public function d($data, $die = false) {
		echo '<pre>';
		print_r($data);
		//var_dump($data);
		echo '</pre>';

		if ($die != false) die();

		return true;
	}

	public function getRandomFilm() {
		$random_film = null;

		@$xml = file_get_contents($this->USER_XML_PATH);

		if ($xml) {
			//@$xmlData = new SimpleXMLElement($xml);
			@$xmlData = simplexml_load_string($xml);

			if ($xmlData) $random_film = $xmlData->film[rand(0, count($xmlData) - 1)];
			else $this->setError(1);
		}
		else $this->setError(0);

		return $random_film;
	}

	public function getSearchMovieTitle() {
		if ($this->search_movie_title != '') return $this->search_movie_title;
		if ($this->random_film == null) $this->random_film = $this->getRandomFilm();

		$search_movie_title = ($this->random_film->en != '' ? $this->random_film->en . ' ' : '')
			. ($this->random_film->ru != '' ? $this->random_film->ru . ' ' : '')
			//. ($this->random_film->year != '' ? $this->random_film->year . ' ' : '')
			. $this->en_search_prefix; // RU_SEARCH_PREFIX - bad result with "RU"

		return $search_movie_title;
	}

	public function getImageUrl() {
		if ($this->image_url != '') return $this->image_url;
		if ($this->search_movie_title != '') $this->search_movie_title = $this->getSearchMovieTitle();

		$image_url = $this->getFromImagesGoogle($this->search_movie_title);

		return $image_url;
	}

	public function getH1Title() {
		if ($this->h1_title != '') return $this->h1_title;
		if ($this->random_film == null) $this->random_film = $this->getRandomFilm();

		$h1_title = ($this->random_film->ru ? $this->random_film->ru . ' | ' : '')
			. ($this->random_film->en ? $this->random_film->en . ' | ' : '')
			. ($this->random_film->year ? $this->random_film->year : '');

		//if (mb_substr($h1_title, -2) == "| ") $h1_title = mb_substr($h1_title, 0, mb_strlen($h1_title, -2));

		return $h1_title;
	}
}
