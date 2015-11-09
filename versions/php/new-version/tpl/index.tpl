<!DOCTYPE html>
<html>
<head>
	<title>Random movie that you would like to revise (c) Script by oOLokiOo</title>
	<meta charset="utf-8">

	<link rel="stylesheet" href="../../../../css/style.css" />
</head>
<body>
	<header>
	</header>

	<main>
		<?php if ($app->error != '') { ?><p><b><?=$app->error?></b></p><?php } ?>
		<h1><a target="_blank" href="http://google.com/search?q=<?=str_replace(' | ', ' ', $app->h1_title)?> смотреть фильм онлайн"><?=$app->h1_title?></a></h1>
		<button type="button" onclick="location.reload(); return false;">Get Film!</button>
		<br /><br />
		<img src="<?=$app->image_url?>" alt="<?=$app->h1_title?>" title="<?=$app->h1_title?>" />
	</main>

	<footer>
	</footer>
</body>
</html>