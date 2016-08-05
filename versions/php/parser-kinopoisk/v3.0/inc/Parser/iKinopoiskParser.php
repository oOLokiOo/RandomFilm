<?php

namespace Parser;

interface IKinopoiskParser {

	/**
	 * [d description] debug function
	 * 
	 * @param 	mixed 		$data 	
	 * @param 	boolean 	$die 	param for stopping php further processing 
	 * @return 	boolean 	true 	or die if $die seted to true
	 */
	public function d($data, $die = false);

	/**
	 * [setLogging description] sets we need file logging or not
	 * 
	 * @param boolean $val
	 */
	public function setLogging($val = false);

	/**
	 * [setLogErrorPath description] sets path to file loging file
	 * 
	 * @param string $path 	filesystem path
	 */
	public function setLogErrorPath($path = '');

	/**
	 * [getFilmByDirectUrl description] get parsed film details
	 * 
	 * @param  string $url 	direct url to single film from kinopoisk.ru
	 * @return object      	object with model of parsed film and parsing process errors
	 */
	public function getFilmByDirectUrl($url = '');

	/**
	 * [getFilmBySearchQuery description] get direct url to single film from first result of kinopoisk.ru search form, 
	 * then get parsed film details by this url
	 * 
	 * @param  string $query 	query for kinopoisk.ru search form
	 * @return object 			object with model of parsed film and parsing process errors
	 */
	public function getFilmBySearchQuery($query = '');

	/**
	 * [getSubsidiaryPage description] get site arbitrary page for personal parsing 
	 * 
	 * @param  string 			$url 	url to arbitrary site page
	 * @return SimpleHtmlDom 			SimpleHtmlDom DOM object
	 */
	public function getSubsidiaryPage($url = '');
}
