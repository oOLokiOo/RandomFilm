<?php

require_once 'curl.php';
require_once 'simple_html_dom.php';


class KinopoiskParser {
	private $main_domen 		= 'http://www.kinopoisk.ru';
	private $film_prefix 		= '/film/';
	private $search_page_url 	= '/index.php?first=no&what=&kp_query=';
	private $errors_arr = array(
		'0' => 'Nothing was found...',
		);

	//public $start_from_id 	= 0; // max - http://www.kinopoisk.ru/film/555400 ?
	public $web_version 	= false;

	public $save_result 	= true;
	public $result_folder 	= '/result/';

	public $logging 		= true;
	public $log_result 		= '/logs/result.log';
	public $log_error 		= '/logs/error.log';

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


	public function __construct($search_query = '') {
		$film_id = 0;
		$curl = new Curl();

		// TODO: fix this IF normal with - web_version in all code
		if ($this->web_version === false) {
			$film_id = file_get_contents(ROOT.$this->log_result);
			$film_id = ($film_id == '' ? 1 : $film_id+1);
		} else {
			$response = $curl->get($this->main_domen.$this->search_page_url.urldecode($search_query));
			$dom = str_get_html($response);

			$top_search_result = $dom->find('.search_results .name a', 0);
		}

		if (isset($top_search_result) || $this->web_version === false) {
			if ($this->web_version === false) $this->result['detail_page_url'] = $this->main_domen.$this->film_prefix.$film_id;
			else {
				$this->result['detail_page_url'] = $this->main_domen.$top_search_result->href;

				// TODO: fix it with normal 100% id
				$film_id = explode('/', $this->result['detail_page_url'])[6];
				$film_id = (is_numeric($film_id) ? $film_id : 0);
				if ($film_id == 0) $this->log('cant find film id... url - '.$this->result['detail_page_url']);
			}

			$response = $curl->get($this->result['detail_page_url']);
			//echo $response; die();
			$dom = str_get_html($response);

			$this->result['id'] = $film_id;

			// parse left column info
			$ru = $dom->find('#headerFilm h1, #headerPeople h1', 0);
//var_dump($ru->innertext);
			if ($ru) $this->result['ru'] = $this->decode($ru->innertext);
//var_dump($this->result['ru']);
			$en = $dom->find('#headerFilm span, #headerPeople span', 0);
			if ($en) $this->result['en'] = $this->decode($en->innertext);

			// parse middle column info
			foreach ($dom->find('#infoTable table tr') as $tr_content) {
				$td_title = $tr_content->find('td', 0);
				$mb_td_title = $this->decode($td_title->innertext);

				$td_value = $tr_content->find('td', 1);
				$mb_td_value = $this->decode($td_value->innertext);

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
			$starring = $dom->find('#actorList ul', 0);
			if ($starring) {
				$this->result['starring'] = $this->decode($starring->innertext);

				foreach ($starring->find('li') as $li_content) {
					$this->result['starring_arr'][] = $this->decode(strip_tags($li_content->innertext));
				}
				array_pop($this->result['starring_arr']);
				$this->result['starring'] = implode(',', $this->result['starring_arr']);
			}

			// parse image
			$img = $dom->find('#photoBlock .popupBigImage img, #photoBlock img', 0);
			if ($img) $this->result['img'] = $img->src;

			// save all data to DB & HDD
			if ($this->save_result === true) {
				//if ($this->result['en'] == '' && $this->result['ru'] == '') { // TODO: check for 404 page here! (foreach all base and reove empty objects like 12400 id)
				//	d($this->result);
				//	die('<h1>Wrong Film Title! ObjectID: '.$film_id.'</h1>');
				//} else {
					$image_path = ROOT.$this->result_folder.$film_id.'.jpg';

					//if ($img /*&& !file_exists($image_path)*/) {
						if ($img) file_put_contents($image_path, file_get_contents($img->src));

						$this->result['_id'] = $this->result['id'];

						$mongo = new MongoClient();
						$collection = $mongo->kinopoisk->movies->films;
						try {
							$collection->save($this->result);
						} catch (MongoException $e) {
							d($this->result);
							die('<h1>MongoException Error: code - '.$e->getCode().'. ObjectID: '.$film_id.'</h1>');
						}
						$mongo->close();
					//}
//die("!");
					$this->log($film_id, 'result');	
				//}
			}
		}
		else $this->result['error'] = $this->errors_arr[0];

		if ($this->web_version === false) $this->do_redirect();
	}


	private function decode($str) {
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