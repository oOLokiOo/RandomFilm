<!DOCTYPE html>
<html>
<head>
	<title>Kinopoisk.ru PHP simple parser / КиноПоиск.ру PHP простой парсер</title>
	<meta charset="utf-8">

	<!-- Just for local & github project version, common CSS file, you can remove it from here -->
	<link rel="stylesheet" href="../css/style.css" />
	<style>
		p {
			padding: 10px 0;
			margin: 0;
		}

		img {
			min-width: 250px;
		}

		table {
			box-shadow: 0 0 10px rgba(0,0,0,0.5);
			margin: 0 auto;
			padding: 10px;
		}
			table tr td {
				text-transform: capitalize;
				text-align: left;
				vertical-align: top;
			}
			table input, table textarea {
				width: 90%;
				font-size: 16px;
			}
			table .lit {
				width: 130px;
				font-weight: bold;
			}
	</style>
	<!-- /end -->
</head>
<body>
	<header>
	</header>

	<main>
		<?php if(isset($result->errors)) { ?><p class="error"><?=$result->errors[0]?></p><?php } ?>

		<p><b>Введите название фильма:<b/></p>
		<form method="post">
			<input type="text" name="search_query" value="" />
			<button type="submit">Get Film!</button>
		</form>

		<section>
			<p><b>Результат:</b></p>

			<table>
				<tr>
					<td colspan="2"><a href="<?=(isset($result->data->detail_page_url) ? $result->data->detail_page_url : '')?>" target="_blank"><?=(isset($result->data->detail_page_url) ? $result->data->detail_page_url : '')?></a></td>
				</tr>
				<tr>
					<td class="lit">ru</td>
					<td align="left"><input type="text" name="ru" value="<?=(isset($result->data->ru) ? strip_tags($result->data->ru) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">en</td>
					<td align="left"><input type="text" name="en" value="<?=(isset($result->data->en) ? strip_tags($result->data->en) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">год</td>
					<td align="left"><input type="text" name="year" value="<?=(isset($result->data->year) ? strip_tags($result->data->year) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">страна</td>
					<td align="left"><input type="text" name="country" value="<?=(isset($result->data->country) ? strip_tags($result->data->country) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">режиссер</td>
					<td align="left"><input type="text" name="producer" value="<?=(isset($result->data->producer) ? strip_tags($result->data->producer) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">жанр</td>
					<td align="left"><textarea name="genre" rows="2"><?=(isset($result->data->genre) ? strip_tags($result->data->genre) : '')?></textarea></td>
				</tr>
				<tr>
					<td class="lit">бюджет</td>
					<td align="left"><input type="text" name="budget" value="<?=(isset($result->data->budget) ? strip_tags($result->data->budget) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">сборы в США</td>
					<td align="left"><input type="text" name="budget_usa" value="<?=(isset($result->data->budget_usa) ? strip_tags($result->data->budget_usa) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">сборы в мире</td>
					<td align="left"><input type="text" name="budget_world" value="<?=(isset($result->data->budget_world) ? strip_tags($result->data->budget_world) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">премьера (мир)</td>
					<td align="left"><input type="text" name="premiere_world" value="<?=(isset($result->data->premiere_world) ? strip_tags($result->data->premiere_world) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">премьера (РФ)</td>
					<td align="left"><input type="text" name="premiere_rf" value="<?=(isset($result->data->premiere_rf) ? strip_tags($result->data->premiere_rf) : '')?>" /></td>
				</tr>
				<tr>
					<td class="lit">время</td>
					<td align="left"><input type="text" name="time" value="<?=(isset($result->data->time) ? strip_tags($result->data->time) : '')?>" /></textarea></td>
				</tr>
				<tr>
					<td class="lit">в главных ролях</td>
					<td align="left"><textarea name="starring" rows="6"><?=(isset($result->data->starring) ? $result->data->starring : '')?></textarea></td>
				</tr>
			</table>

			<br /><br />
			<img src="<?=(isset($result->data->img) ? $result->data->img : '')?>" />
		</section>
	</main>

	<footer>
		&copy; Ivan Volkov aka oOLokiOo
	</footer>
</body>
</html>
