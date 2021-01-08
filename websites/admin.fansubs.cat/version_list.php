<?php
function get_status_description_short($id){
	switch ($id){
		case 1:
			return "Completada";
		case 2:
			return "En procés";
		case 3:
			return "Parcialment completada";
		case 4:
			return "Abandonada";
		case 5:
			return "Cancel·lada";
		default:
			return "Desconegut";
	}
}

$header_title="Versions";
$page="version";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-version", "S'ha suprimit la versió (id. de versió: ".$_GET['delete_id'].")");
		query("DELETE FROM link WHERE version_id=".escape($_GET['delete_id']));
		query("DELETE FROM folder WHERE version_id=".escape($_GET['delete_id']));
		query("DELETE FROM episode_title WHERE version_id=".escape($_GET['delete_id']));
		query("DELETE FROM rel_version_fansub WHERE version_id=".escape($_GET['delete_id']));
		query("DELETE FROM version WHERE id=".escape($_GET['delete_id']));
		@unlink('../anime.fansubs.cat/images/versions/'.$_GET['delete_id'].'.jpg');
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de versions</h4>
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
								<th scope="col">Fansub</th>
								<th scope="col">Sèrie</th>
								<th class="text-center" scope="col">Estat</th>
								<th class="text-center" scope="col"><span title="Obtenció automàtica d'enllaços activada">OA</span></th>
								<th class="text-center" scope="col"><span title="Recomanable pel sistema de recomanacions">R</span></th>
								<th class="text-center" scope="col">Enllaços</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE EXISTS (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.version_id=v.id AND vf2.fansub_id='.$_SESSION['fansub_id'].')';
	} else {
		$where = '';
	}

	$result = query("SELECT GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name, s.name series_name, v.*, COUNT(DISTINCT l.id) links, (SELECT COUNT(*) FROM folder fo WHERE fo.active=1 AND fo.version_id=v.id) autofetch FROM version v LEFT JOIN link l ON v.id=l.version_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id$where GROUP BY v.id ORDER BY f.name, s.name");
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['fansub_name']); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></td>
								<td class="align-middle text-center"><?php echo get_status_description_short($row['status']); ?></td>
								<td class="align-middle text-center"><?php echo $row['autofetch']==1 ? 'OA' : '-'; ?></td>
								<td class="align-middle text-center"><?php echo $row['is_featurable']==1 ? 'R'.($row['is_always_featured']==1 ? 'S' : '') : '-'; ?></td>
								<td class="align-middle text-center"><?php echo $row['links']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="version_stats.php?id=<?php echo $row['id']; ?>" title="Estadístiques" class="fa fa-chart-line p-1 text-success"></a> <a href="version_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="version_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir la versió de la sèrie '".$row['series_name']."' de ".$row['fansub_name']." i tots els seus enllaços? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="series_choose.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una versió</a>
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
