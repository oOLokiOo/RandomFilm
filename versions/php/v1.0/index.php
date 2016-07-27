<?php

require_once 'config.php';
require_once 'functions.php';

$errors = array();
$xml = file_get_contents(XML_PATH);

if ($xml) {
	//@$xmlData = new SimpleXMLElement($xml);
	@$xmlData = simplexml_load_string($xml);

	if ($xmlData) {
		$rand = rand(0, count($xmlData) - 1);
		$random_movie = $xmlData->film[$rand];
	}
	else $errors[] = 'Xml file not Found';
}
else $errors[] = 'String could not be parsed as XML';

$rand = -1;
$search_movie_title = '';
$h1_title = '';
$image_url = '';


if (isset($random_movie)) {
	$search_movie_title .= ($random_movie->en != '' ? $random_movie->en . ' ' : '')
		. ($random_movie->ru != '' ? $random_movie->ru . ' ' : '')
		//. ($random_movie->year != '' ? $random_movie->year . ' ' : '')
		. EN_SEARCH_PREFIX; // RU_SEARCH_PREFIX - bad result with "ru"

	$images_arr = get_from_images_google($search_movie_title);
	$image_url = filter_from_blocked_resources($images_arr, $BLOCKED_RESOURCES);

	$h1_title = ($random_movie->ru ? $random_movie->ru . ' | ' : '')
		. ($random_movie->en ? $random_movie->en . ' | ' : '')
		. ($random_movie->year ? $random_movie->year : '');

	//if (mb_substr($h1_title, -2) == "| ") $h1_title = mb_substr($h1_title, 0, mb_strlen($h1_title, -2));
}
else $errors[] = "Movie [" . $rand . "] - not found!" 
?>


<!DOCTYPE html>
<html>
<head>
	<title>Random movie that you would like to revise (c) Script by oOLokiOo</title>
	<meta charset="utf-8">

	<link rel="stylesheet" href="../../../css/style.css" />
</head>
<body>
	<header>
	</header>

	<main>
		<?php if (count($errors)) { ?><p><b><?=implode('<br />', $errors)?></b></p><?php } ?>
		<h1><a target="_blank" href="http://google.com/search?q=<?=str_replace(' | ', ' ', $h1_title)?> смотреть фильм онлайн"><?=$h1_title?></a></h1>
		<button type="button" onclick="location.reload(); return false;">Get Film!</button>
		<br /><br />
		<img src="<?=$image_url?>" alt="<?=$h1_title?>" title="<?=$h1_title?>" />
	</main>

	<footer>
	</footer>
</body>
</html>