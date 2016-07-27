<?php

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);


const GOOGLE_IMAGES_URL = 'http://ajax.googleapis.com/ajax/services/search/images?';
const CURL_REQUEST_ATTEMPT = 5;
const EN_SEARCH_PREFIX 	= 'film poster';
const RU_SEARCH_PREFIX 	= 'фильм постер';
$BLOCKED_RESOURCES = array(
    'www.impawards.com',
    'en.wikipedia.org'
);
const XML_PATH = '../../../users/1/films.xml';
