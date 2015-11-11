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


require_once getcwd().'/inc/kinopoisk_parser.class.php';

$css_path = '../../../../css/style.css'; // Just for local & github project version, common CSS file, you can remove it from here
$search_query = ((isset($_REQUEST['search_query']) && $_REQUEST['search_query'] != '') ? $_REQUEST['search_query'] : 'мстители');

$parser = new KinopoiskParser($search_query);
//echo '<pre>'; print_r($parser); echo '</pre>';

require_once getcwd().'/tpl/index.tpl';