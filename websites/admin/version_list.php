<?php
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Llista de versions d'anime - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Llista de versions de manga - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Llista de versions d'acció real - Acció real";
		$page="liveaction";
	break;
}

include("header.inc.php");

switch ($type) {
	case 'anime':
		$content_uc="Anime";
		$content_prep="d'anime";
	break;
	case 'manga':
		$content_uc="Manga";
		$content_prep="de manga";
	break;
	case 'liveaction':
		$content_uc="Contingut d'acció real";
		$content_prep="de contingut d'acció real";
	break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-version", "S'ha suprimit una versió de '".query_single("SELECT s.name FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.id=".escape($_GET['delete_id']))."' (id. de versió: ".$_GET['delete_id'].")");
		query("DELETE FROM file WHERE version_id=".escape($_GET['delete_id']));
		query("DELETE FROM remote_folder WHERE version_id=".escape($_GET['delete_id']));
		query("DELETE FROM episode_title WHERE version_id=".escape($_GET['delete_id']));
		query("DELETE FROM rel_version_fansub WHERE version_id=".escape($_GET['delete_id']));
		query("DELETE FROM version WHERE id=".escape($_GET['delete_id']));
		//Views will NOT be removed in order to keep consistent stats history
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de versions <?php echo $content_prep; ?></h4>
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
						<thead class="table-dark">
							<tr>
								<th scope="col">Fansub</th>
								<th scope="col"><?php echo $content_uc; ?></th>
								<th class="text-center" scope="col">Estat</th>
								<th class="text-center" scope="col"><span title="Recomanable pel sistema de recomanacions">R</span></th>
								<th class="text-center" scope="col">Fitxers</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$extra_where = ' AND EXISTS (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.version_id=v.id AND vf2.fansub_id='.$_SESSION['fansub_id'].')';
	} else {
		$extra_where = '';
	}

	$result = query("SELECT GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name, s.name series_name, v.*, COUNT(DISTINCT fi.id) files FROM version v LEFT JOIN file fi ON v.id=fi.version_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='$type'$extra_where GROUP BY v.id ORDER BY fansub_name, s.name");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="6" class="text-center">- No hi ha cap versió -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['fansub_name']); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['series_name']); ?></td>
								<td class="align-middle text-center"><?php echo get_status_description_short($row['status']); ?></td>
								<td class="align-middle text-center"><?php echo $row['is_featurable']==1 ? 'R'.($row['is_always_featured']==1 ? 'S' : '') : '-'; ?></td>
								<td class="align-middle text-center"><?php echo $row['files']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="version_links.php?type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" title="Enllaços" class="fa fa-link p-1 text-info"></a> <a href="version_stats.php?type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" title="Estadístiques" class="fa fa-chart-line p-1 text-success"></a> <a href="version_edit.php?type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="version_list.php?type=<?php echo $type; ?>&delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir la versió de '".$row['series_name']."' de ".$row['fansub_name']." i tots els seus fitxers? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="series_choose.php?type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix una versió</a>
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
