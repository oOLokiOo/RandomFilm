<?php

mb_internal_encoding('UTF-8');
error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);


require_once '../../users/1/films.php';
require_once 'functions.php';


$random_film_number = rand(0, count($films) - 1);
$film_to_search = $films[$random_film_number];
$img = get_from_images_google($film_to_search . ' film poster');
$img_url = filter_from_blocked_resources($img);
?>


<!DOCTYPE html>
<html>
<head>
	<title>Random movie that you would like to revise (c) Script by oOLokiOo</title>
	<meta charset="utf-8">

	<link rel="stylesheet" href="../../css/style.css" />
</head>
<body>
	<header>
	</header>

	<main>
		<h1><a target="_blank" href="http://google.com/search?q=<?=str_replace(' | ', ' ', $film_to_search)?> смотреть фильм онлайн"><?=$film_to_search?></a></h1>
		<button type="button" onclick="location.reload(); return false;">Get Film!</button>
		<br /><br />
		<img src="<?=$img_url?>" alt="<?=$film_to_search?>" title="<?=$film_to_search?>" />
	</main>

	<footer>
	</footer>
</body>
</html>