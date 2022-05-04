<?php
$header_title="Cerques d'anime - Anàlisi";
$page="analytics";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Cerques d'anime</h4>
					<hr>
					<p class="text-center">Aquestes són les cerques més populars al web d'anime durant els darrers 30 dies.</p>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col">Cerca</th>
								<th scope="col" style="width: 12%;" class="text-center">Repeticions</th>
<?php
	if (!empty($_GET['show_results'])) {
?>
								<th scope="col" style="width: 12%;" class="text-center">Resultats d'anime (ara)</th>
								<th scope="col" style="width: 12%;" class="text-center">Resultats de manga (ara)</th>
<?php
	}
?>
							</tr>
						</thead>
						<tbody>
<?php
	$extra_query="";
	if (!empty($_GET['show_results'])) {
		$extra_query=",(SELECT COUNT(*) FROM series s WHERE (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.hidden=0)>0 AND s.name LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR s.alternate_names LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR s.studio LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR s.keywords LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR s.id IN (SELECT sg.series_id FROM rel_series_genre sg LEFT JOIN genre g ON sg.genre_id=g.id WHERE g.name=query)) results_anime,(SELECT COUNT(*) FROM manga m WHERE (SELECT COUNT(*) FROM manga_version mv WHERE mv.manga_id=m.id AND mv.hidden=0)>0 AND m.name LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR m.alternate_names LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR m.author LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR m.keywords LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR m.id IN (SELECT mg.manga_id FROM rel_manga_genre mg LEFT JOIN genre g ON mg.genre_id=g.id WHERE g.name=query)) results_manga";
	}
	$result = query("SELECT LOWER(query) query,COUNT(*) cnt$extra_query FROM search_history WHERE day>='".date('Y-m-d', strtotime('-30 days'))."' GROUP BY LOWER(query) ORDER BY cnt DESC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center">- No hi ha cap cerca -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<td class="align-middle"><?php echo htmlspecialchars($row['query']); ?></td>
								<td class="align-middle text-center"><?php echo htmlspecialchars($row['cnt']); ?></td>
<?php
		if (!empty($_GET['show_results'])) {
?>
								<td class="align-middle text-center"><?php echo htmlspecialchars($row['results_anime']); ?></td>
								<td class="align-middle text-center"><?php echo htmlspecialchars($row['results_manga']); ?></td>
<?php
		}
?>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
