<?php

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
@session_start();

require_once 'functions.php';
require_once '../users/1/films.php';

echo '
<!DOCTYPE html>
<html>
<head>
	<title>Random film from my collection.</title>

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />

	<style>
		body {text-align: center; padding: 0; margin: 0;}
		a {color: #000; text-decoration: underline;}
		h1,h2,h3,h4,h5,h6 {padding: 4; margin: 4;}
	</style>
</head>

<body>
	<h3>Фильм, который можно пересмотреть:</h3>

	<form action="" method="post">
		<input type="hidden" name="action" value="get_film" />
		<input type="submit" value="Get Film!" />
	</form>
';

$random_film_number = rand(0, count($films) - 1);

if (!empty($_POST['action']) && $_POST['action'] == 'get_film') {
	$film = str_replace(' | ', '<br />', $films[$random_film_number]);
	$film_to_search = explode('<br />', $film);
	echo '<h1><a href="#" onclick="window.open(\'http://google.com/search?q=' . $film_to_search[0] . ' смотреть фильм онлайн\'); return false;">' . $film . '</a></h1>';

	//$poster = file_get_contents('http://images.google.at/images?hl=de&q=' . urlencode($film_to_search[0] . ' постер') . '"', 'r');
	//@ereg ("imgurl=http://www.[A-Za-z0-9-]*.[A-Za-z]*[^.]*.[A-Za-z]*", $poster, $img);
	//@ereg ("http://(.*)", $img[0], $img_url);
	//echo '<img src="' . $img_url[0] . '" height="400" />';

	$img = get_from_images_google($film_to_search[0] . ' фильм постер');
	echo '<img src="' . $img[2]['url'] . '" />';
}

echo '
</body>
</html>
';

