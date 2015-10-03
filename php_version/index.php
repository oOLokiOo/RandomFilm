<?php

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);
@session_start();

require_once 'functions.php';
require_once '../users/1/films.php';

$random_film_number = rand(0, count($films) - 1);
$film_to_search = $films[$random_film_number];

//$poster = file_get_contents('http://images.google.at/images?hl=de&q=' . urlencode($film_to_search[0] . ' постер') . '"', 'r');
//@ereg ("imgurl=http://www.[A-Za-z0-9-]*.[A-Za-z]*[^.]*.[A-Za-z]*", $poster, $img);
//@ereg ("http://(.*)", $img[0], $img_url);
//echo '<img src="' . $img_url[0] . '" height="400" />';

$img = get_from_images_google($film_to_search[0] . ' фильм постер');
?>


<!DOCTYPE html>
<html>
<head>
	<title>Random movie that you would like to revise (c) Script by oOLokiOo</title>

	<meta charset="utf-8">

	<link rel="stylesheet" href="../css/style.css" />
</head>

<body>
<body>
	<header>
	</header>

	<main>
		<h1><a target="_blank" href="http://google.com/search?q=<?=$film_to_search[0]?> смотреть фильм онлайн"><?=$film_to_search?></a></h1>
		<button type="button" onclick="location.reload(); return false;">Get Film!</button>
		<br /><br />
		<img src="<?=$img[2]['url']?>" alt="<?=$film_to_search[0]?>" title="<?=$film_to_search[0]?>" />
	</main>

	<footer>
	</footer>
</body>
</html>