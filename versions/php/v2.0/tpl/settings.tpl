<?php require_once '_header.tpl'; ?>

<p>
	<form action="" method="post">
		<table>
			<tr>
				<td class="lit">ru</td>
				<td align="left"><input type="text" name="ru" value="" /></td>
			</tr>
			<tr>
				<td class="lit">en</td>
				<td align="left"><input type="text" name="en" value="" /></td>
			</tr>
			<tr>
				<td class="lit">год</td>
				<td align="left"><input type="text" name="year" value="" /></td>
			</tr>
			<tr>
				<td class="lit">kinopoisk.ru</td>
				<td align="left"><input type="text" name="kinopoisk" value="" /></td>
			</tr>
			<tr>
				<td class="lit">imdb.com</td>
				<td align="left"><input type="text" name="imdb" value="" /></td>
			</tr>
			<tr>
				<td>
					<button type="submit">ADD</button>
				</td>
			</tr>
		</table>
	</form>
</p>

<?php
	if ($xmlData) {
		echo '<table class="films_list">';
		foreach ($xmlData->film as $film) {
			echo '<tr>';
			echo '<td width="82%" align="left">'.$film->ru.' / '.$film->en.' '.$film->year.'</td>';
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