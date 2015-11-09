<?php

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

require_once getcwd().'/inc/app.class.php';

$app = new APP('../../../users/1/films.xml');

require_once getcwd().'/tpl/index.tpl';