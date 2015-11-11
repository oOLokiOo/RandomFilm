<!DOCTYPE html>
<html>
<head>
	<title>Kinopoisk.ru PHP simple parser / КиноПоиск.ру PHP простой парсер</title>
	<meta charset="utf-8">

	<!-- Just for local & github project version, common CSS file, you can remove it from here -->
	<link rel="stylesheet" href="<?=$css_path?>" />
	<style>
		p {
			padding: 0 4px;
			margin: 0;
		}

		table {
			margin: 0 auto;
			border: 1px solid #aaa;
			padding: 10px;
		}

		table input, table textarea {
			width: 90%;
			font-size: 16px;
		}

		table .lit {
			width: 130px;
		}
	</style>
	<!-- /end -->
</head>
<body>
	<main>
		<p><h3><?=$parser->result['error']?></h3></p>

		<p>Введите название фильма:</p>
		<form method="post">
			<input type="text" name="search_query" value="" />
			<button type="submit">Go!</button>
		</form>

		<section>
			<p>Результат:</p>

			<table>
				<tr>
					<td colspan="2"><a href="<?=$parser->result['detail_page_url']?>" target="_blank"><?=$parser->result['detail_page_url']?></a></td>
				</tr>
				<tr>
					<td class="lit">ru</td>
					<td align="left"><input type="text" name="ru" value="<?=strip_tags($parser->result['ru'])?>" /></td>
				</tr>
				<tr>
					<td class="lit">en</td>
					<td align="left"><input type="text" name="en" value="<?=strip_tags($parser->result['en'])?>" /></td>
				</tr>
				<tr>
					<td class="lit">год</td>
					<td align="left"><input type="text" name="year" value="<?=strip_tags($parser->result['year'])?>" /></td>
				</tr>
				<tr>
					<td class="lit">страна</td>
					<td align="left"><input type="text" name="country" value="<?=strip_tags($parser->result['country'])?>" /></td>
				</tr>
				<tr>
					<td class="lit">жанр</td>
					<td align="left"><textarea name="genre" rows="2"><?=strip_tags($parser->result['genre'])?></textarea></td>
				</tr>
				<tr>
					<td class="lit">время</td>
					<td align="left"><textarea name="genre" rows="2"><?=strip_tags($parser->result['time'])?></textarea></td>
				</tr>
				<tr>
					<td class="lit">в главных ролях</td>
					<td align="left"><textarea name="starring" rows="6"><?=$parser->result['starring']?></textarea></td>
				</tr>
			</table>

			<br /><br />
			<img src="<?=$parser->result['img']?>" />
		</section>
	</main>
</body>
</html>