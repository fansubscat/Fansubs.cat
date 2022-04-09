<?php
$header_title="Manteniment - Eines";
$page="tools";
include("header.inc.php");

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
	$result = query("SELECT g.myanimelist_id_anime, g.name FROM genre g WHERE g.myanimelist_id_anime IS NOT NULL ORDER BY g.name");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'{"mal_id": '.$row['myanimelist_id_anime'].', "name": "'.htmlspecialchars($row['name']).'"}';
	}
	mysqli_free_result($result);
?>

			];
			var mangaGenres = [
<?php
	//Get all manga genres, format for JavaScript:
	$result = query("SELECT g.myanimelist_id_manga, g.name FROM genre g WHERE g.myanimelist_id_manga IS NOT NULL ORDER BY g.name");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'{"mal_id": '.$row['myanimelist_id_manga'].', "name": "'.htmlspecialchars($row['name']).'"}';
	}
	mysqli_free_result($result);
?>

			];
			var animes = [
<?php
	//Get all animes and their tags, format for JavaScript:
	$result = query("SELECT * FROM (SELECT IF(a.myanimelist_id IS NOT NULL,CONCAT(a.myanimelist_id, ',', GROUP_CONCAT(DISTINCT s.myanimelist_id)),GROUP_CONCAT(DISTINCT s.myanimelist_id)) myanimelist_ids, a.name, GROUP_CONCAT(DISTINCT g.myanimelist_id_anime) genre_ids FROM rel_series_genre ag LEFT JOIN series a ON ag.series_id=a.id LEFT JOIN genre g ON ag.genre_id=g.id LEFT JOIN season s ON s.series_id=ag.series_id WHERE a.myanimelist_id IS NOT NULL OR s.myanimelist_id IS NOT NULL GROUP BY ag.series_id ORDER BY a.name) subquery WHERE myanimelist_ids IS NOT NULL");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'{"mal_ids": ['.implode(',',array_unique(explode(',',$row['myanimelist_ids']))).'], "name": "'.htmlspecialchars($row['name']).'", "genres": ['.$row['genre_ids'].']}';
	}
	mysqli_free_result($result);
?>

			];
			var mangas = [
<?php
	//Get all mangas and their tags, format for JavaScript:
	$result = query("SELECT * FROM (SELECT IF(m.myanimelist_id IS NOT NULL,CONCAT(m.myanimelist_id, ',', GROUP_CONCAT(DISTINCT v.myanimelist_id)),GROUP_CONCAT(DISTINCT v.myanimelist_id)) myanimelist_ids, m.name, GROUP_CONCAT(DISTINCT g.myanimelist_id_manga) genre_ids FROM rel_manga_genre mg LEFT JOIN manga m ON mg.manga_id=m.id LEFT JOIN genre g ON mg.genre_id=g.id LEFT JOIN volume v ON v.manga_id=mg.manga_id WHERE m.myanimelist_id IS NOT NULL OR v.myanimelist_id IS NOT NULL GROUP BY mg.manga_id ORDER BY m.name) subquery WHERE myanimelist_ids IS NOT NULL");
	$first = TRUE;
	while ($row = mysqli_fetch_assoc($result)) {
		if (!$first) {
			echo ",\n";
		} else {
			$first = FALSE;
		}
		echo "\t\t\t\t".'{"mal_ids": ['.implode(',',array_unique(explode(',',$row['myanimelist_ids']))).'], "name": "'.htmlspecialchars($row['name']).'", "genres": ['.$row['genre_ids'].']}';
	}
	mysqli_free_result($result);
?>

			];

			var noMalAnime = [
<?php
	$result = query("SELECT a.name FROM series a WHERE myanimelist_id IS NULL AND NOT EXISTS (SELECT myanimelist_id FROM season s WHERE s.series_id=a.id AND s.myanimelist_id IS NOT NULL) ORDER BY a.name");
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
	$result = query("SELECT m.name FROM manga m WHERE myanimelist_id IS NULL AND NOT EXISTS (SELECT myanimelist_id FROM volume v WHERE v.manga_id=m.id AND v.myanimelist_id IS NOT NULL) ORDER BY m.name");
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

include("footer.inc.php");
?>
