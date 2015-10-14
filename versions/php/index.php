<?php

require_once 'config.php';
require_once 'functions.php';

$xml = file_get_contents(XML_PATH);
$xmlData = new SimpleXMLElement($xml);

$rand = rand(0, count($xmlData) - 1);
$random_movie = $xmlData->film[$rand];

$search_movie_title = '';
$title = '';
$img_url = '';
$error = '';

if (isset($random_movie)) {
	$search_movie_title .= ($random_movie->en != '' ? $random_movie->en . ' ' : '')
		. ($random_movie->ru != '' ? $random_movie->ru . ' ' : '')
		. ($random_movie->year != '' ? $random_movie->year . ' ' : '')
		. EN_SEARCH_PREFIX; // RU_SEARCH_PREFIX - bad result with "ru"

	$imges_arr = get_from_images_google($search_movie_title);
	$img_url = filter_from_blocked_resources($imges_arr, $BLOCKED_RESOURCES);

	$title = ($random_movie->ru ? $random_movie->ru . ' | ' : '')
		. ($random_movie->en ? $random_movie->en . ' | ' : '')
		. ($random_movie->year ? $random_movie->year : '');

	//if (mb_substr($title, -2) == "| ") $title = mb_substr($title, 0, mb_strlen($title, -2));
}
else $error = "Movie [" . $rand . "] - not found!" 
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
		<?php if ($error != '') { ?><p><b><?=$error?></b></p><?php } ?>
		<h1><a target="_blank" href="http://google.com/search?q=<?=str_replace(' | ', ' ', $title)?> смотреть фильм онлайн"><?=$title?></a></h1>
		<button type="button" onclick="location.reload(); return false;">Get Film!</button>
		<br /><br />
		<img src="<?=$img_url?>" alt="<?=$title?>" title="<?=$title?>" />
	</main>

	<footer>
	</footer>
</body>
</html>