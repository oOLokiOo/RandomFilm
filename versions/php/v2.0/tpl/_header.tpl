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
				<li><a href="/?page=index">Main<a></li>
				<li> / </li>
				<li><a href="/?page=settings">Settings<a></li>
				<li> / </li>
				<li><a href="/?page=login">Logout<a></li>
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
