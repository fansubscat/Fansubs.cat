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
					<p class="text-center">Aquestes són les cerques més populars al web d'anime.</p>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col">Cerca</th>
								<th scope="col" style="width: 12%;" class="text-center">Repeticions</th>
								<th scope="col" style="width: 12%;" class="text-center">Resultats (ara)</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT LOWER(query) query,COUNT(*) cnt,(SELECT COUNT(*) FROM series s WHERE s.name LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR s.alternate_names LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR s.studio LIKE CONCAT('%',REPLACE(query,' ','%'),'%') OR s.keywords LIKE CONCAT('%',REPLACE(query,' ','%'),'%')) results FROM search_history GROUP BY LOWER(query) ORDER BY cnt DESC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="5" class="text-center">- No hi ha cap cerca -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<td class="align-middle"><?php echo htmlspecialchars($row['query']); ?></td>
								<td class="align-middle text-center"><?php echo htmlspecialchars($row['cnt']); ?></td>
								<td class="align-middle text-center"><?php echo htmlspecialchars($row['results']); ?></td>
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
