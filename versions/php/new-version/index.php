<?php

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

require_once getcwd().'/inc/app.class.php';

$XML_PATH = '../../../users/1/films.xml';
$CSS_PATH = '../../../../css/style.css';

$app = new APP($XML_PATH);

require_once getcwd().'/tpl/index.tpl';