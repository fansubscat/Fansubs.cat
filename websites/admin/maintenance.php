<?php
$header_title="Manteniment - Eines";
$page="tools";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Manteniment</h4>
					<hr>
					<div class="text-center p-2">
						<button type="button" class="btn btn-primary" onclick="return checkAnimeGenres(0,0,[]);" onauxclick="return false;">Analitza els gèneres dels animes</a>
					</div>
					<div class="text-center p-2">
						<button type="button" class="btn btn-primary" onclick="return checkMangaGenres(0,0,[]);" onauxclick="return false;">Analitza els gèneres dels mangues</a>
					</div>
					<div class="text-center p-2">
						<button type="button" class="btn btn-primary" onclick="return showAnimeWithNoMal();" onauxclick="return false;">Mostra els animes no enllaçats a MAL</a>
					</div>
					<div class="text-center p-2">
						<button type="button" class="btn btn-primary" onclick="return showMangaWithNoMal();" onauxclick="return false;">Mostra els mangues no enllaçats a MAL</a>
					</div>
					<div class="text-center p-2">
						<button type="button" class="btn btn-primary" onclick="return showLiveActionWithNoMdl();" onauxclick="return false;">Mostra els continguts d’imatge real no enllaçats a MDL</a>
					</div>
				</article>
			</div>
		</div>
		<div class="container d-flex justify-content-center p-4" id="output_card">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Sortida</h4>
					<hr>
					<div class="text-center p-2" id="output">
						Aquí es mostrarà la sortida de les ordres executades.
					</div>
				</article>
			</div>
		</div>
		<script>
			var animeGenres = [
<?php
	//Get all anime genres, format for JavaScript:
	$result = query("SELECT g.external_id_anime, g.name FROM genre g WHERE g.external_id_anime IS NOT NULL ORDER BY g.name");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'{"mal_id": '.$row['external_id_anime'].', "name": "'.htmlspecialchars($row['name']).'"}';
	}
	mysqli_free_result($result);
?>

			];
			var mangaGenres = [
<?php
	//Get all manga genres, format for JavaScript:
	$result = query("SELECT g.external_id_manga, g.name FROM genre g WHERE g.external_id_manga IS NOT NULL ORDER BY g.name");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'{"mal_id": '.$row['external_id_manga'].', "name": "'.htmlspecialchars($row['name']).'"}';
	}
	mysqli_free_result($result);
?>

			];
			var animes = [
<?php
	//Get all animes and their tags, format for JavaScript:
	$result = query("SELECT * FROM (SELECT IF(a.external_id IS NOT NULL,CONCAT(a.external_id, ',', GROUP_CONCAT(DISTINCT IFNULL(d.external_id,-1))),GROUP_CONCAT(DISTINCT IFNULL(d.external_id,-1))) external_ids, a.name, a.type, GROUP_CONCAT(DISTINCT g.external_id_anime) genre_ids FROM series a LEFT JOIN rel_series_genre ag ON ag.series_id=a.id LEFT JOIN genre g ON ag.genre_id=g.id LEFT JOIN division d ON d.series_id=ag.series_id WHERE a.external_id IS NOT NULL OR d.external_id IS NOT NULL GROUP BY a.id ORDER BY a.name) subquery WHERE type='anime' AND external_ids IS NOT NULL");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'{"mal_ids": ['.implode(',',array_diff(array_unique(explode(',',$row['external_ids'])), [-1])).'], "name": "'.htmlspecialchars($row['name']).'", "genres": ['.$row['genre_ids'].']}';
	}
	mysqli_free_result($result);
?>

			];
			var mangas = [
<?php
	//Get all mangas and their tags, format for JavaScript:
	$result = query("SELECT * FROM (SELECT IF(a.external_id IS NOT NULL,CONCAT(a.external_id, ',', GROUP_CONCAT(DISTINCT IFNULL(d.external_id,-1))),GROUP_CONCAT(DISTINCT IFNULL(d.external_id,-1))) external_ids, a.name, a.type, GROUP_CONCAT(DISTINCT g.external_id_manga) genre_ids FROM series a LEFT JOIN rel_series_genre ag ON ag.series_id=a.id LEFT JOIN genre g ON ag.genre_id=g.id LEFT JOIN division d ON d.series_id=ag.series_id WHERE a.external_id IS NOT NULL OR d.external_id IS NOT NULL GROUP BY a.id ORDER BY a.name) subquery WHERE type='manga' AND external_ids IS NOT NULL");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'{"mal_ids": ['.implode(',',array_diff(array_unique(explode(',',$row['external_ids'])), [-1])).'], "name": "'.htmlspecialchars($row['name']).'", "genres": ['.$row['genre_ids'].']}';
	}
	mysqli_free_result($result);
?>

			];

			var noMalAnime = [
<?php
	$result = query("SELECT s.name FROM series s WHERE external_id IS NULL AND NOT EXISTS (SELECT external_id FROM division d WHERE d.series_id=s.id AND d.external_id IS NOT NULL) AND s.type='anime' ORDER BY s.name");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'"'.htmlspecialchars($row['name']).'"';
	}
	mysqli_free_result($result);
?>

			];

			var noMalManga = [
<?php
	$result = query("SELECT s.name FROM series s WHERE external_id IS NULL AND NOT EXISTS (SELECT external_id FROM division d WHERE d.series_id=s.id AND d.external_id IS NOT NULL) AND s.type='manga' ORDER BY s.name");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'"'.htmlspecialchars($row['name']).'"';
	}
	mysqli_free_result($result);
?>

			];

			var noMdlLiveAction = [
<?php
	$result = query("SELECT s.name FROM series s WHERE external_id IS NULL AND NOT EXISTS (SELECT external_id FROM division d WHERE d.series_id=s.id AND d.external_id IS NOT NULL) AND s.type='liveaction' ORDER BY s.name");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'"'.htmlspecialchars($row['name']).'"';
	}
	mysqli_free_result($result);
?>

			];
		</script>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
