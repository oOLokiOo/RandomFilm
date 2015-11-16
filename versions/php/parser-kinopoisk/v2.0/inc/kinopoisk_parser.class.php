<?php

require_once 'curl.php';
require_once 'simple_html_dom.php';


class KinopoiskParser {
	private $main_domen = 'http://www.kinopoisk.ru';
	private $search_page_url = '/index.php?first=no&what=&kp_query=';
	private $errors_arr = array(
		'0' => 'Nothing was found...',
		);

	public $result = array(
		'error'				=> '',
		'detail_page_url'	=> '',
		'ru'				=> '',
		'en'				=> '',
		'year'				=> '',
		'country'			=> '',
		'genre'				=> '',
		'genre_arr'			=> array(),
		'time'				=> '',
		'starring'			=> '',
		'starring_arr'		=> array(),
		'img'				=> ''
		);


	public function __construct($search_query = '') {
		$curl = new Curl();
		$response = $curl->get($this->main_domen.$this->search_page_url.urldecode($search_query));
		$dom = str_get_html($response);

		$top_search_result = $dom->find('.search_results .name a', 0);

		if ($top_search_result) {
			$this->result['detail_page_url'] = $this->main_domen.$top_search_result->href;

			$response = $curl->get($this->result['detail_page_url']);
			$dom = str_get_html($response);

			$ru = $dom->find('#headerFilm h1, #headerPeople h1', 0);
			if ($ru) $this->result['ru'] = mb_convert_encoding($ru->innertext, 'UTF-8', 'Windows-1251');

			$en = $dom->find('#headerFilm span, #headerPeople span', 0);
			if ($en) $this->result['en'] = $en->innertext;

			foreach ($dom->find('#infoTable table tr') as $tr_content) {
				$td_title = $tr_content->find('td', 0);
				$mb_td_title = mb_convert_encoding($td_title->innertext, 'UTF-8', 'Windows-1251');

				$td_value = $tr_content->find('td', 1);
				$mb_td_value = mb_convert_encoding($td_value->innertext, 'UTF-8', 'Windows-1251');

				switch ($mb_td_title) {
					case 'год':
						$a = $td_value->find('a', 0);
						$this->result['year'] = $a->innertext;
						break;

					case 'страна':
						$a = $td_value->find('a', 0);
						$a_mb = mb_convert_encoding($a->innertext, 'UTF-8', 'Windows-1251');
						$this->result['country'] = $a_mb;
						break;

					case 'жанр':
						$span = $td_value->find('span', 0);
						$span_mb = mb_convert_encoding($span->innertext, 'UTF-8', 'Windows-1251');
						$this->result['genre_arr'] = explode(', ', strip_tags($span_mb));
						$this->result['genre'] = $span_mb;
						break;

					case 'время':
						$this->result['time'] = $mb_td_value;
						break;
				}
			}

			$starring = $dom->find('#actorList ul', 0);
			if ($starring) {
				//$this->result['starring'] = mb_convert_encoding($starring->innertext, 'UTF-8', 'Windows-1251');

				foreach ($starring->find('li') as $li_content) {
					$this->result['starring_arr'][] = mb_convert_encoding(strip_tags($li_content->innertext), 'UTF-8', 'Windows-1251');
				}
				array_pop($this->result['starring_arr']);
				$this->result['starring'] = implode(', ', $this->result['starring_arr']);
			}

			$img = $dom->find('#photoBlock .popupBigImage img, #photoBlock img', 0);
			if ($img) $this->result['img'] = $img->src;
		}
		else $this->result['error'] = $this->errors_arr[0];
	}
}