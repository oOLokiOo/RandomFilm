<?php


namespace Parser;

use stdClass;
use \Helpers\Page;
use \Helpers\Model;
use \Helpers\File;

/**
 * KinopoiskParser.php
 * 
 * @author Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com>
 * @version 3.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v3.0
 * @see https://github.com/RubtsovAV/php-curl-lib
 * @see http://simplehtmldom.sourceforge.net
 */

require_once 'iKinopoiskParser.php';
require_once 'Helpers/Page.php';
require_once 'Helpers/Model.php';
require_once 'Helpers/File.php';


class KinopoiskParser implements iKinopoiskParser {
	private $_page	= null;
	private $_model	= null;
	private $_file	= null;

	// result -> data
	// result -> errors
	private $result;

	private $url_matches		= 'kinopoisk.ru';
	private $main_domen			= 'http://www.kinopoisk.ru';
	private $search_page_url 	= '/index.php?first=no&what=&kp_query=';
	private $film_prefix_in_url	= '/film/';

	const ERR_PAGE_NOT_FOUND = 1;
	const ERR_NOTHING_FOUND_BY_QUERY = 2;
	const ERR_NOTHING_FOUND_BY_URL = 3;
	const ERR_CANT_FIND_FILM_ID = 4;
	const ERR_QUERY_IS_EMPTY = 5;
	const ERR_URL_IS_EMPTY = 6;
	const ERR_BAD_SEARCH_DOMAIN = 7;

	private $errors_arr = array(
		'1' => '404 - page with this $url was not found',
		'2' => 'nothing was found by this $query',
		'3' => 'nothing was found by this $url',
		'4' => 'can\'t find FILM ID in this $url',
		'5' => 'search $query is empty',
		'6' => 'search $url is empty',
		'7' => 'search $url does not contains domen kinopoisk.ru'
		);

	private $logging 	= true;
	private $log_result = 'logs/result.log';
	private $log_error 	= 'logs/error.log';


	public function __construct() {
		$this->_page	= new Page();
		$this->_model	= new Model();
		$this->_file	= new File();

		$this->reset();
	}


	private function reset() {
		$this->result = new stdClass();
	}

	// --- logging...
	private function setError($error_number, $error_addition = '') {
		if (isset($error_number) && 
			is_numeric($error_number) && 
			$error_number > 0 && 
			isset($this->errors_arr[$error_number])) {

			$log_str = $this->errors_arr[$error_number].($error_addition != '' ? ' --- '.$error_addition : '');
			$this->result->errors[] = $log_str;
			if ($this->logging === true) $this->pushLog($log_str);

			return true;
		}

		throw new \Exception('Can\'t setError(); something going wrong...');
	}

	private function pushLog($str = '') {
		return $this->_file->writeToFile($this->log_error, 'ERROR: ( '.date('H:i:s d.m.Y', time()).' ) ::: '.$str."\n");
	}
	// ---

	// --- parsing url...
	private function checkCommon($str = '') {
		return ((isset($str) && trim($str)) != '' ? true : false);
	}

	private function checkQuery($str = '') {
		if ($this->checkCommon($str) === false) {
			$this->setError(self::ERR_QUERY_IS_EMPTY);
			return false;
		}

		return true;
	}

	private function checkUrl($str = '') {
		if ($this->checkCommon($str) === false) {
			$this->setError(self::ERR_URL_IS_EMPTY);
			return false;
		}

		if ((strpos($str, $this->url_matches) === false)) {
			$this->setError(self::ERR_BAD_SEARCH_DOMAIN, $str);
			return false;
		}

		return true;
	}

	private function getFilmIdFromUrl($url = '') {
		$film_id = 0;
		$tmp_arr = array();

		$tmp_arr = explode('/film/', $url);

		if (isset($tmp_arr[1])) $film_id = explode('/', $tmp_arr[1])[0];
		$film_id = (is_numeric($film_id) ? $film_id : 0);

		if ($film_id == 0) $this->setError(self::ERR_CANT_FIND_FILM_ID, $url);

		return $film_id;
	}
	// ---

	// --- parsing content...
	private function decode($str = '') {
		$str = mb_convert_encoding($str, 'UTF-8', 'Windows-1251');
		$str = html_entity_decode($str);
		$str = trim($str);
		$str = preg_replace('/\s+/', ' ', $str);

		return $str;
	}

	private function process($url = '') {
		$this->_model->_id = $this->_model->id; // just MongoDB feature

		// parse content LEFT column info
		$ru = $this->_page->find('#headerFilm h1, #headerPeople h1', 0);
		if ($ru) $this->_model->ru = $this->decode($ru->innertext);

		$en = $this->_page->find('#headerFilm span, #headerPeople span', 0);
		if ($en) $this->_model->en = $this->decode($en->innertext);

		if ($ru || $en) {
			$this->_model->detail_page_url = $url;

			// parse content MIDDLE column info
			foreach ($this->_page->find('#infoTable table tr') as $tr_content) {
				$td_title = $tr_content->find('td', 0);
				$mb_td_title = $this->decode($td_title->innertext);

				$td_value = $tr_content->find('td', 1);
				$mb_td_value = $this->decode($td_value->innertext);

				switch ($mb_td_title) {
					case 'год':
						$a = $td_value->find('a', 0);
						$this->_model->year = $this->decode($a->innertext);
						break;

					case 'страна':
						$tmp_arr = array();
						foreach ($td_value->find('a') as $a) {
							$a_mb = $this->decode($a->innertext);
							$tmp_arr[] = $a_mb;
						}
						$this->_model->country_arr = $tmp_arr;
						$this->_model->country = implode(',', $this->_model->country_arr);
						break;

					case 'режиссер':
						$tmp_arr = array();
						foreach ($td_value->find('a') as $a) {
							$a_mb = $this->decode($a->innertext);
							$tmp_arr[] = $a_mb;
						}
						$this->_model->producer_arr = $tmp_arr;
						$this->_model->producer = implode(',', $this->_model->producer_arr);
						break;

					case 'жанр':
						$tmp_arr = array();
						foreach ($td_value->find('span a') as $a) {
							$a_mb = $this->decode($a->innertext);
							$tmp_arr[] = $a_mb;
						}
						$this->_model->genre_arr = $tmp_arr;
						$this->_model->genre = implode(',', $this->_model->genre_arr);
						break;

					case 'бюджет':
						$a = $td_value->find('a', 0);
						if ($a) $this->_model->budget = $this->decode($a->innertext);
						break;

					case 'сборы в США':
						$a = $td_value->find('a', 0);
						$this->_model->budget_usa = $this->decode($a->innertext);
						break;

					case 'сборы в мире':
						$a = $td_value->find('a', 0);
						$this->_model->budget_world = $this->decode($a->innertext);
						break;

					case 'премьера (мир)':
						$a = $td_value->find('a', 0);
						$a_mb = $this->decode($a->innertext);
						$this->_model->premiere_world = $a_mb;
						break;

					case 'премьера (РФ)':
						$a = $td_value->find('a', 0);
						$a_mb = $this->decode($a->innertext);
						$this->_model->premiere_rf = $a_mb;
						break;

					case 'время':
						$this->_model->time = strip_tags($mb_td_value);
						break;
				}
			}

			// parse content RIGHT column info
			$starring = $this->_page->find('#actorList ul', 0);
			if ($starring) {
				$tmp_arr = array();
				foreach ($starring->find('li') as $li_content) {
					$tmp_arr[] = $this->decode(strip_tags($li_content->innertext));
				}

				array_pop($tmp_arr);
				$this->_model->starring_arr = $tmp_arr;
				$this->_model->starring = implode(',', $this->_model->starring_arr);
			}

			// parse image
			$img = $this->_page->find('#photoBlock .popupBigImage img, #photoBlock img', 0);
			if ($img) $this->_model->img = $img->src;

			$this->result->data = $this->_model;
		}
		else $this->setError(self::ERR_PAGE_NOT_FOUND, $url);

		return $this->result;
	}
	// ---


	// --- public methods...
	public function d($data, $die = false) {
		echo '<pre>';
		print_r($data);
		//var_dump($data);
		echo '</pre>';

		if ($die !== false) die();

		return true;
	}

	public function setLogging($val = false) {
		$this->logging = ($val === true ? true : false);
	}

	public function setLogErrorPath($path = '') {
		$this->log_error = $path;
	}

	public function getFilmByDirectUrl($url = '') {
		$this->reset();

		if ($this->checkUrl($url) && 
			($this->_model->id = $this->getFilmIdFromUrl($url)) > 0 && 
			$this->_page->get($url)->dom != null) {

			return $this->process($url);
		}
		else $this->setError(self::ERR_NOTHING_FOUND_BY_URL, $url);

		return $this->result;
	}

	public function getFilmBySearchQuery($query = '') {
		$this->reset();

		if ($this->checkQuery($query) && 
			$this->_page->get($this->main_domen.$this->search_page_url.urldecode($query)) != null) {

			// get first film url from top search results by $search_query
			$top_search_result = $this->_page->find('.search_results .name a', 0);
			if ($top_search_result) {
				// get detail film page url from first link
				$url = $this->main_domen.$top_search_result->href;
				if (($this->_model->id = $this->getFilmIdFromUrl($url)) > 0 && 
					$this->_page->get($url)->dom != null) {
					return $this->process($url);
				}
				else $this->setError(self::ERR_NOTHING_FOUND_BY_URL, $url);
			}
			else $this->setError(self::ERR_NOTHING_FOUND_BY_QUERY, $query);
		}

		return $this->result;
	}

	public function getSubsidiaryPage($url = '') {
		return $this->_page->get($url);
	}
}
