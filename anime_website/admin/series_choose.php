<?php
$header_title="Versions";
$page="version";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">De quina sèrie vols crear una versió?</h4>
					<hr>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col">Nom</th>
								<th class="text-center" scope="col">Tipus</th>
								<th class="text-center" scope="col">Capítols</th>
								<th class="text-center" scope="col">Versions</th>
								<th class="text-center" scope="col">Acció</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT s.*,COUNT(DISTINCT v.id) versions FROM series s LEFT JOIN version v ON s.id=v.series_id GROUP BY s.id ORDER BY s.name");
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle text-center"><?php echo $row['type']=='movie' ? 'Film' : 'Sèrie'; ?></td>
								<td class="align-middle text-center"><?php echo $row['episodes']!=NULL ? $row['episodes'] : '-'; ?></td>
								<td class="align-middle text-center"><?php echo $row['versions']; ?></td>
								<td class="align-middle text-center"><a href="version_edit.php?series_id=<?php echo $row['id']; ?>" title="Crea'n una versió" class="fa fa-plus-square p-1 text-success"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
					<div class="text-center">
						<a href="series_edit.php" class="btn btn-primary">No hi és? Afegeix una sèrie nova</a>
					</div>
<?php
	}
?>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
