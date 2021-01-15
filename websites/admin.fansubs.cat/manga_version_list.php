<?php
$header_title="Llista de versions de manga - Manga";
$page="manga";
include("header.inc.php");
require_once("common.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-manga-version", "S'ha suprimit la versió de manga (id. de versió: ".$_GET['delete_id'].")");
		query("DELETE FROM file WHERE manga_version_id=".escape($_GET['delete_id']));
		query("DELETE FROM chapter_title WHERE manga_version_id=".escape($_GET['delete_id']));
		query("DELETE FROM rel_manga_version_fansub WHERE manga_version_id=".escape($_GET['delete_id']));
		query("DELETE FROM manga_version WHERE id=".escape($_GET['delete_id']));
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de versions de manga</h4>
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
								<th scope="col">Manga</th>
								<th class="text-center" scope="col">Estat</th>
								<th class="text-center" scope="col"><span title="Recomanable pel sistema de recomanacions">R</span></th>
								<th class="text-center" scope="col">Fitxers</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE EXISTS (SELECT vf2.manga_version_id FROM rel_manga_version_fansub vf2 WHERE vf2.manga_version_id=v.id AND vf2.fansub_id='.$_SESSION['fansub_id'].')';
	} else {
		$where = '';
	}

	$result = query("SELECT GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name, m.name manga_name, v.*, COUNT(DISTINCT fi.id) files FROM manga_version v LEFT JOIN file fi ON v.id=fi.manga_version_id LEFT JOIN rel_manga_version_fansub vf ON v.id=vf.manga_version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN manga m ON v.manga_id=m.id$where GROUP BY v.id ORDER BY f.name, m.name");
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['fansub_name']); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['manga_name']); ?></td>
								<td class="align-middle text-center"><?php echo get_status_description_short($row['status']); ?></td>
								<td class="align-middle text-center"><?php echo $row['is_featurable']==1 ? 'R'.($row['is_always_featured']==1 ? 'S' : '') : '-'; ?></td>
								<td class="align-middle text-center"><?php echo $row['files']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="manga_version_stats.php?id=<?php echo $row['id']; ?>" title="Estadístiques" class="fa fa-chart-line p-1 text-success"></a> <a href="manga_version_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="manga_version_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir la versió del manga '".$row['manga_name']."' de ".$row['fansub_name']." i tots els seus fitxers? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="manga_choose.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix una versió</a>
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
