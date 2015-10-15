<?php

require_once '../config.php';
//require_once '../functions.php';


$detail_page_url = '';
$result = array(
	'error' => '',
	'ru' => '',
	'en' => '',
	'year' => '',
	'img' => ''
);

if (isset($_REQUEST['movie']) && $_REQUEST['movie'] != '') {
	// https://github.com/RubtsovAV/php-curl-lib
	require_once 'Curl.php';
	// http://simplehtmldom.sourceforge.net/manual.htm
	require_once 'simple_html_dom.php';

	$domen = 'http://www.kinopoisk.ru';
	$url = $domen . '/index.php?first=no&what=&kp_query=';
	$url .= $_REQUEST['movie'];

	$curl = new Curl();
	$response = $curl->get($url);
	$html = str_get_html($response);
	//echo $response;

	$e = $html->find('.search_results .name a', 0);

	if ($e) {
		$detail_page_url = $domen . $e->href;

		$response = $curl->get($detail_page_url);
		$html = str_get_html($response);
		//echo $response;

		$ru = $html->find('#headerFilm h1', 0);
		$result['ru'] = mb_convert_encoding($ru->innertext, 'UTF-8', 'Windows-1251');

		$en = $html->find('#headerFilm span', 0);
		$result['en'] = $en->innertext;

		$year = $html->find('#infoTable tr', 0)->find('td', 1)->find('a', 0);
		$result['year'] = $year->innertext;

		$img = $html->find('#photoBlock .popupBigImage img', 0);
		$result['img'] = $img->src;
	}
	else $result['error'] = 'Nothing was found...';
}
?>

<!DOCTYPE html>
<html>
<head>
	<title>Kinopoisk Parser</title>
	<meta charset="utf-8">
	<link rel="stylesheet" href="../../../css/style.css" />
	<style>
		p {
			padding: 4px;
			margin: 0;
		}
	</style>
</head>
<body>
	<main>
		<p><?=$result['error']?></p>

		<form method="post">
			<input type="text" name="movie" value="" />
			<button type="submit">Go!</button>
		</form>

		<section>
			<p>RESULT:</p>
			<p><a href="<?=$detail_page_url?>" target="_blank"><?=$detail_page_url?></a></p>
			<p><input type="text" name="ru" value="<?=strip_tags($result['ru'])?>" /></p>
			<p><input type="text" name="en" value="<?=strip_tags($result['en'])?>" /></p>
			<p><input type="text" name="year" value="<?=strip_tags($result['year'])?>" /></p>
			<br /><br />
			<img src="<?=$result['img']?>" />
		</section>
	</main>
</body>
</html>