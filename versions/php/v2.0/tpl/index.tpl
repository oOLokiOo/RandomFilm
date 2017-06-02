<!DOCTYPE html>
<html>
<head>
	<title>Random movie that you would like to revise (c) Script was made by Ivan Volkov aka oOLokiOo</title>
	<meta charset="utf-8">

	<link rel="stylesheet" href="/assets/css/style.css" />
	<style type="text/css">
		img {
			<?php if ($app->show_large_image === true) { ?>
				min-width: 360px;
			<?php } else { ?>
				min-width: 180px;
			<?php } ?>
		}
	</style>

	<!--script type='text/javascript' src='/vendor/components/jquery/jquery.min.js'></script-->
</head>
<body>
	<header>
		<menu>
			<ul>
				<li><a href="#">Settings<a></li>
				<li> / </li>
				<li><a href="#">Logout<a></li>
			</ul>
		</menu>

		<?php if (isset($film->errors)) { ?>
			<p class="error">
				<?php foreach($film->errors as $error) { ?>
					<?=$error?>
				<?php } ?>
			</p>
		<?php } ?>
	</header>

	<main>
		<h1><a target="_blank" href="http://google.com/search?q=<?=str_replace(' | ', ' ', $film->data->h1_title)?> смотреть фильм онлайн"><?=$film->data->h1_title?></a></h1>

		<?php if (isset($film->data->kinopoisk)) { ?><a href="<?=$film->data->kinopoisk?>" target="_blank">KINOPOISK</a><br /><?php } ?>

		<?php if (isset($film->data->imdb)) { ?><a href="<?=$film->data->imdb?>" target="_blank">IMDB</a><br /><?php } ?>

		<br />
		<button type="button" onclick="location.reload(); return false;">Get Film!</button>
		<br /><br />
		<img src="<?=$film->data->image_url?>" alt="<?=$film->data->h1_title?>" title="<?=$film->data->h1_title?>" />
	</main>

	<footer>
		&copy; Ivan Volkov aka oOLokiOo
	</footer>
</body>
</html>
