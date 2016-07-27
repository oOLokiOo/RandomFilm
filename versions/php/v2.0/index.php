<?php

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);


//require_once __DIR__.'/../parser-kinopoisk/v3.0/inc/KinopoiskParser/Parser.php';
require_once __DIR__.'/../parser-kinopoisk/v2.0/inc/kinopoisk_parser.class.php';
require_once __DIR__.'/inc/RandomFilm.php';

/* *** PROJECT XML STRUCTURE ***

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

$XML_PATH = '../../../users/1/films.xml';

$app = new RandomFilm($XML_PATH);

require_once __DIR__.'/tpl/index.tpl';
