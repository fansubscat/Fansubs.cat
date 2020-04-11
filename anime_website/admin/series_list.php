<?php
$header_title="Sèries";
$page="series";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete", "series", $_GET['delete_id']);
		query("DELETE FROM rel_series_genre WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM episode WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM version WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM series WHERE id=".escape($_GET['delete_id']));
		//Cascaded deletions: link, rel_version_fansub, views
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de sèries</h4>
					<hr>

<?php
	if (!empty($_SESSION['message'])) {
?>
					<p class="alert alert-success text-center"><?php echo $_SESSION['message']; ?></p>
<?php
		$_SESSION['message']=NULL;
	}
?>

					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col">Nom</th>
								<th class="text-center" scope="col">Tipus</th>
								<th class="text-center" scope="col">Capítols</th>
								<th class="text-center" scope="col">Versions</th>
								<th class="text-center" scope="col">Accions</th>
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
								<td class="align-middle text-center"><a href="version_edit.php?series_id=<?php echo $row['id']; ?>" title="Crea'n una versió" class="fa fa-plus p-1 text-success"></a> <a href="series_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="series_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir la sèrie '".$row['name']."' i tot el seu material? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="series_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una sèrie</a>
					</div>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
