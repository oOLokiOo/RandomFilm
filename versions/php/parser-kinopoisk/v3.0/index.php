<?php

/**
 * @author Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com>
 * @version 3.0
 * @see https://github.com/oOLokiOo/random-film/tree/master/versions/php/parser-kinopoisk/v3.0
 * @see https://github.com/RubtsovAV/php-curl-lib
 * @see http://simplehtmldom.sourceforge.net
 */

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 86400);
set_time_limit(86400);

if (!defined('KinopoiskParserProjectRoot')) define('KinopoiskParserProjectRoot', __DIR__);


require_once KinopoiskParserProjectRoot.'/inc/kinopoisk_parser.class.php';

$css_path = '../../../../css/style.css'; // Just for local & github project version, common CSS file, you can remove it from here.
$search_query = ((isset($_REQUEST['search_query']) && $_REQUEST['search_query'] != '') ? $_REQUEST['search_query'] : '');

$result = array();
$parser = new KinopoiskParser(KinopoiskParserProjectRoot);

// mode - 1
/*
$result = $parser->getFilmBySearchQuery($search_query);
//$parser->save_result = true;
$parser->d($result);
require_once KinopoiskParserProjectRoot.'/tpl/index.tpl';
*/

/*
// mode - 2
// 61237 - железный человек
// 709570 - test
// 7095700 - 404 not found
$result = $parser->getFilmByDirectUrl('http://www.kinopoisk.ru/film/61237/');
//$parser->save_result = true;
$parser->d($result);
require_once KinopoiskParserProjectRoot.'/tpl/index.tpl';
*/

// mode - 3
///*
$parser->save_result = true;
$parser->parseAllSite();
//*/
