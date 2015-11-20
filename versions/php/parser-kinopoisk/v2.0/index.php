<?php

/**
 * @author Ivan Volkov aka oOLokiOo <ivan.volkov.older@gmail.com>
 * @version 2.0
 * @see https://github.com/RubtsovAV/php-curl-lib
 * @see http://simplehtmldom.sourceforge.net
 */

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
ini_set('memory_limit', '2048M');
ini_set('max_execution_time', 86400);
set_time_limit(86400);

if (!defined('ROOT')) define('ROOT', __DIR__);

function d($data, $die = false) {
	echo '<pre>';
	print_r($data);
	//var_dump($data);
	echo '</pre>';

	if ($die !== false) die();
}


/*
$mongo = new MongoClient();
$collection= $mongo->kinopoisk->movies->films;
$filter = array('id' => 301);
d($collection->findOne($filter));
$mongo->close();
*/

require_once ROOT.'/inc/kinopoisk_parser.class.php';

$css_path = '../../../../css/style.css'; // Just for local & github project version, common CSS file, you can remove it from here
$search_query = ((isset($_REQUEST['search_query']) && $_REQUEST['search_query'] != '') ? $_REQUEST['search_query'] : '');

$parser = new KinopoiskParser($search_query);
//d($parser, 1);

require_once ROOT.'/tpl/index.tpl';