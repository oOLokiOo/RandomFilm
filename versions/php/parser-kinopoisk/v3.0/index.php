<?php

namespace Inc\KinopoiskParser;

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);


const ROOT = __DIR__;

$result 		= null;
$save_result 	= true;
$action 		= 'web_version';
$action 		= 'parse_all_site';

require_once ROOT.'/inc/KinopoiskParser/Parser.php';
//use Inc\KinopoiskParser\Parser as KinopoiskParser;

$parser = new Parser();

switch ($action) {
	case 'web_version':
		$search_query = ((isset($_REQUEST['search_query']) && trim($_REQUEST['search_query']) != '') ? $_REQUEST['search_query'] : '');
		if ($search_query != '') $result = $parser->getFilmBySearchQuery($search_query);
		//$parser->d($result);

		require_once ROOT.'/tpl/index.tpl';
		break;

	case 'parse_all_site':
		$result_log 			= ROOT.'/logs/result.log';
		$result_images_path 	= ROOT.'/result/';
		$no_image_prefix 		= 'poster_none.png';
		$wait_to_redirect_time 	= 5; // in sec. to avoid blocking of the frequent requests.

		$film_id = file_get_contents($result_log);
		$film_id = ($film_id == '' ? 1 : $film_id+1);
		$result = $parser->getFilmByDirectUrl('http://www.kinopoisk.ru/film/'.$film_id);

		// add all parsed data to DB
		if ($save_result === true && isset($result->data)) {
			if ($result->data->img) {
				$image_path = $result_images_path.$result->data->id.'.jpg';
				$tmp = explode('/', $result->data->img);

				if ($tmp[count($tmp)-1] != $no_image_prefix) file_put_contents($image_path, file_get_contents($result->data->img));
			}

			$mongo = new \MongoClient();
			$collection = $mongo->kinopoisk->movies->films;
			try {
				$collection->save($result->data);
			} catch (MongoException $e) {
				$parser->d($result);
				die('<h1>MongoException Error: code - '.$e->getCode().'. ObjectID: '.$film_id.'</h1>');
			}
			$mongo->close();
		}

		// write ID of new added to the DB film to log result file
		file_put_contents($result_log, $film_id);

		// do redirect to index page with random parameter to avoid the ban by browser because of recursion
		require_once ROOT.'/tpl/redirect.tpl';
		break;

	default:
		// TESTS ->getFilmByDirectUrl();
		//var_dump($parser->getFilmByDirectUrl());
		//var_dump($parser->getFilmByDirectUrl('test'));
		//var_dump($parser->getFilmByDirectUrl('http://www.kinopoisk.ru/61237777777777777/'));
		//var_dump($parser->getFilmByDirectUrl('http://www.kinopoisk.ru/film/61237777777777777/'));
		//var_dump($parser->getFilmByDirectUrl('http://www.kinopoisk.ru/film/61237/'));

		// TESTS ->getFilmBySearchQuery();
		//var_dump($parser->getFilmBySearchQuery());
		//var_dump($parser->getFilmBySearchQuery('testqqqwwweee11111222333qzzzzzzzzzzzzzz'));
		//var_dump($parser->getFilmBySearchQuery('test'));

		die('action not found...');
		break;
}

exit();
