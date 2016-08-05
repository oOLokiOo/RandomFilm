<?php

require_once ROOT.'/../parser-kinopoisk/v3.0/inc/Parser/KinopoiskParser.php';

/**
 * RandomFilm.php
 * 
 * @author Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com>
 * @version 2.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php/v2.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v3.0
 */

class RandomFilm {
	private $_parser = null;

	private $USER_XML_PATH = '';

	private $google_images_url 				= 'http://www.google.by/search?q=';
	private $google_images_url_end_prefix 	= '&source=lnms&tbm=isch'; // for switching to google images tab
	private $en_search_prefix 				= 'kinopoisk.ru'; // film poster
	private $ru_search_prefix 				= 'kinopoisk.ru'; // фильм постер

	const ERR_XML_NOT_FOUND = 1;
	const ERR_CANT_PARSE_STRING = 2;

	private $errors_arr = array(
		'1' => 'User Films Xml file - not Found',
		'2' => 'String could not be parsed as XML'
		);


	// result -> data
	// result -> errors
	public $result;

	public $show_large_image = true; // it makes the process slower...


	public function __construct() {
		$this->_parser = new \Parser\KinopoiskParser();
		$this->_parser->setLogErrorPath(__DIR__.'/../../logs/error.log'); // TODO: fix it!

		$this->reset();
	}


	private function reset() {
		$this->result = new stdClass();
	}

	// --- to XmlDB Class
	private function getFilmFromXml() {
		$random_film = null;

		@$xml = file_get_contents($this->USER_XML_PATH);

		if ($xml) {
			//@$xmlData = new SimpleXMLElement($xml);
			@$xmlData = simplexml_load_string($xml);

			if ($xmlData) $random_film = $xmlData->film[rand(0, count($xmlData) - 1)];
			else $this->setError(ERR_CANT_PARSE_STRING);
		}
		else $this->setError(ERR_XML_NOT_FOUND);

		return $random_film;
	}


	// --- to Film Class - preparing film object
	private function prepareSearchTitle() {
		$search_title = ($this->result->data->en != '' ? $this->result->data->en . ' ' : '')
			. ($this->result->data->ru != '' ? $this->result->data->ru . ' ' : '')
			//. ($this->result->data->year != '' ? $this->result->data->year . ' ' : '')
			. $this->en_search_prefix; // RU_SEARCH_PREFIX - bad result with "RU"

		return $search_title;
	}

	private function prepareH1Title() {
		$h1_title = ($this->result->data->ru ? $this->result->data->ru . ' | ' : '')
			. ($this->result->data->en ? $this->result->data->en . ' | ' : '')
			. ($this->result->data->year ? $this->result->data->year : '');

		//if (mb_substr($h1_title, -2) == "| ") $h1_title = mb_substr($h1_title, 0, mb_strlen($h1_title, -2));

		return $h1_title;
	}

	// --- to KinopoiskParser class ???
	private function getUrlFromGoogleImages($search_words = '') {
		$image_url = '';
		$search_url = $this->google_images_url.urlencode($search_words).$this->google_images_url_end_prefix;

		// get thumb from google search by images 
		$page = $this->_parser->getSubsidiaryPage($search_url);
		$dom = $page->dom;
		$result = $dom->find('#search .images_table a', 0);
		$google_image_href = $result->attr['href'];
		$image_url = $result->children[0]->attr['src'];

		// get big iamge from kinopoisk.ru
		if ($this->show_large_image == true && strpos($google_image_href, 'kinopoisk.ru') !== false) {
			$google_image_href = substr($google_image_href, 7, strlen($google_image_href)-1); // crop "/url?q=" from redirect url

			$film = $this->_parser->getFilmByDirectUrl($google_image_href);
			
			if (!count($film->errors)) $image_url = $film->data->img;
			else $this->result->errors = $film->errors;
		}

		return $image_url;
	}

	// --- to COMMON class
	private function setError($error_number, $error_addition = '') {
		if (isset($error_number) && 
			is_numeric($error_number) && 
			$error_number > 0 && 
			isset($this->errors_arr[$error_number])) {

			$log_str = $this->errors_arr[$error_number].($error_addition != '' ? ' --- '.$error_addition : '');
			$this->result->errors[] = $log_str;
			//if ($this->logging === true) $this->pushLog($log_str);

			return true;
		}

		throw new \Exception('Can\'t setError(); something going wrong...');
	}

	private function d($data, $die = false) {
		echo '<pre>';
		print_r($data);
		//var_dump($data);
		echo '</pre>';

		if ($die != false) die();

		return true;
	}


	// --- public methods...
	public function getFilm($USER_XML_PATH = '') {
		$this->reset();
		$this->USER_XML_PATH = $USER_XML_PATH;

		$this->result->data = $this->getFilmFromXml();

		if ($this->result->data != null) {
			$this->result->data->search_title = $this->prepareSearchTitle();
			$this->result->data->image_url = $this->getUrlFromGoogleImages($this->result->data->search_title);
			$this->result->data->h1_title = $this->prepareH1Title();
		}

		return $this->result;
	}
}
