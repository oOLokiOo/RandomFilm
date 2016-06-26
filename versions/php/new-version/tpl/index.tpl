<!DOCTYPE html>
<html>
<head>
	<title>Random movie that you would like to revise (c) Script was made by Ivan Volkov aka oOLokiOo</title>
	<meta charset="utf-8">

	<!-- Just for local & github project version, common CSS file, you can remove it from here -->
	<link rel="stylesheet" href="<?=$CSS_PATH?>" />
	<style type="text/css">
		img {
			<?php if ($app->get_large_images == true) { ?>
				min-width: 360px;
			<?php } else { ?>
				min-width: 180px;
			<?php } ?>
		}
	</style>
	<!-- /end -->
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
		&copy; Ivan Volkov aka oOLokiOo
	</footer>
</body>
</html>