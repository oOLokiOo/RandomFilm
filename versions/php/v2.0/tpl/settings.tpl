<?php require_once '_header.tpl'; ?>

<p>
	<form action="" method="post">
		<table>
			<tr>
				<td align="left">ru</td>
				<td align="left"><input type="text" name="ru" value="" /></td>
			</tr>
			<tr>
				<td align="left">en</td>
				<td align="left"><input type="text" name="en" value="" /></td>
			</tr>
			<tr>
				<td align="left">год</td>
				<td align="left"><input type="text" name="year" value="" /></td>
			</tr>
			<tr>
				<td align="left">kinopoisk.ru</td>
				<td align="left"><input type="text" name="kinopoisk" value="" /></td>
			</tr>
			<tr>
				<td align="left">imdb.com</td>
				<td align="left"><input type="text" name="imdb" value="" /></td>
			</tr>
			<tr>
				<td align="left"><button type="submit" name="add">ADD</button></td>
				<td align="left"><button type="submit" name="find">FIND</button></td>
			</tr>
		</table>
	</form>
</p>

<?php
	if ($xmlData) {
		echo '<table class="films_list">';
		foreach ($xmlData->film as $film) {
			$title = $film->ru.' / '.$film->en.' '.$film->year;

			echo '<tr>';
			echo '<td width="82%" align="left"><a target="_blank" href="http://google.com/search?q='.str_replace(' / ', ' ', $title).' смотреть фильм онлайн">'.$title.'</a></td>';
			echo '<td width="4%"><a href="#">edit</a></td>';
			echo '<td width="4%"><a href="#">del</a></td>';
			echo '</tr>';
		}
		echo '</table>';
	} else {
		echo 'No data...';
	}
?>

<?php require_once '_footer.tpl'; ?>