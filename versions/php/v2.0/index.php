<?php

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

function d($data, $die = false) {
	echo '<pre>';
	print_r($data);
	//var_dump($data);
	echo '</pre>';

	if ($die != false) die();

	return true;
}

/* *** PROJECT USER XML STRUCTURE ***

<?xml version="1.0" encoding="UTF-8"?>
<movies>
	<film>
		<ru>test movie</ru>
		<en>тест</en>
		<year>1234</year>
		<kinopoisk>http://www.kinopoisk.ru/xxx</kinopoisk>
		<imdb>http://www.imdb.com/xxx</imdb>
	</film>
	<film>
		<ru>test movie 2</ru>
		<en>тест</en>
		<year>1234</year>
		<kinopoisk>http://www.kinopoisk.ru/xxx2</kinopoisk>
		<imdb>http://www.imdb.com/xxx2</imdb>
	</film>
</movies>
*/


const ROOT = __DIR__;

require_once ROOT.'/inc/RandomFilm/RandomFilm.php';


$tpl = 'index';
$page = (isset($_REQUEST['page']) ? $_REQUEST['page'] : '');

switch ($page) {
	//case '':
	case 'login':
		$tpl = 'login';
		break;

	case 'settings':
		//require_once ROOT.'/inc/KinopoiskParser/kinopoisk_parser.class.php';

		// PATH = %ROOT%/users/%USER_ID%/films.xml
		$USER_XML_PATH 	= ROOT.'/users/1/films.xml'; // TODO: link UserClass here

		@$xml = file_get_contents($USER_XML_PATH);

		if ($xml) {
			@$xmlData = simplexml_load_string($xml);
		}

		$tpl = 'settings';
		break;

	case 'edit':
		$tpl = 'edit';
		break;
	
	case '':
	case 'index':
		// PATH = %ROOT%/users/%USER_ID%/films.xml
		$USER_XML_PATH 	= ROOT.'/users/1/films.xml'; // TODO: link UserClass here

		$app = new RandomFilm();
		$app->show_large_image = true; // true to configure large image (increases page load)
		$film = $app->getFilm($USER_XML_PATH);

		$tpl = 'index';
		break;
}

require_once ROOT.'/tpl/'.$tpl.'.tpl';
exit();
