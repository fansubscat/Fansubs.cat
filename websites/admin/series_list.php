<?php
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Llista d’anime - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Llista de manga - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Llista de contingut d’imatge real - Imatge real";
		$page="liveaction";
	break;
}

include(__DIR__.'/header.inc.php');

switch ($type) {
	case 'anime':
		$content="anime";
		$content_prep="d’anime";
	break;
	case 'manga':
		$content="manga";
		$content_prep="de manga";
	break;
	case 'liveaction':
		$content="contingut d’imatge real";
		$content_prep="de contingut d’imatge real";
	break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-series", "S’ha suprimit la sèrie «".query_single("SELECT name FROM series WHERE id=".escape($_GET['delete_id']))."» (id. de sèrie: ".$_GET['delete_id'].")");
		query("DELETE FROM rel_series_genre WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM episode WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM version WHERE series_id=".escape($_GET['delete_id']));
		query("DELETE FROM series WHERE id=".escape($_GET['delete_id']));
		@unlink(STATIC_DIRECTORY.'/images/series/'.$_GET['delete_id'].'.jpg');
		@unlink(STATIC_DIRECTORY.'/social/series_'.$_GET['delete_id'].'.jpg');
		//Cascaded deletions: file, link, rel_version_fansub
		//Views will NOT be removed in order to keep consistent stats history
		$_SESSION['message']="S’ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista <?php echo $content_prep; ?></h4>
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
								<th scope="col">Nom</th>
								<th class="text-center" scope="col">Tipus</th>
								<th class="text-center" scope="col">Divisions</th>
								<th class="text-center" scope="col">Capítols</th>
								<th class="text-center" scope="col">Versions</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT s.*,(SELECT GROUP_CONCAT(DISTINCT d.name ORDER BY d.number ASC, d.name ASC SEPARATOR '|||') FROM division d WHERE d.series_id=s.id AND d.external_id IS NOT NULL AND (d.external_id<>s.external_id".($type!='manga' ? ' OR (d.external_id IS NOT NULL AND d.name<>s.name COLLATE utf8mb4_bin)' : '').")) division_titles,(SELECT GROUP_CONCAT(DISTINCT v.title ORDER BY v.title ASC SEPARATOR ', ') FROM version v WHERE v.series_id=s.id AND v.title<>s.name COLLATE utf8mb4_bin) version_titles,(SELECT COUNT(DISTINCT v.id) FROM version v WHERE v.series_id=s.id) versions,(SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.is_real=1) divisions, (SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.is_real=0) fake_divisions FROM series s WHERE s.type='$type' GROUP BY s.id ORDER BY s.name");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="6" class="text-center">- No hi ha cap <?php echo $content; ?> -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
		$divisions = !empty($row['division_titles']) ? explode('|||', $row['division_titles']) : array();
		$divisions_escaped = array();
		foreach ($divisions as $division) {
			array_push($divisions_escaped, htmlspecialchars($division));
		}
?>
							<tr<?php echo $row['rating']=='XXX' ? ' class="hentai"' : ''; ?>>
								<th scope="row" class="align-middle<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo htmlspecialchars($row['name']); ?><?php echo !empty($divisions_escaped) ? '<i><br>&nbsp;&nbsp;&nbsp┗ '.implode('<br>&nbsp;&nbsp;&nbsp;┗ ', $divisions_escaped).'</i>' : ''; ?><?php echo !empty($row['version_titles']) ? '<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i style="font-weight: normal;" class="text-muted">('.htmlspecialchars($row['version_titles']).')</i>' : ''; ?></th>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo get_subtype_name($row['subtype']); ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['divisions']; ?><?php echo $row['fake_divisions']>0 ? '<small>+'.$row['fake_divisions'].'</small>' : ''; ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['number_of_episodes']; ?></td>
								<td class="align-middle text-center<?php echo $row['versions']==0 ? ' text-muted' : ''; ?>"><?php echo $row['versions']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="version_edit.php?type=<?php echo $type; ?>&series_id=<?php echo $row['id']; ?>" title="Crea’n una versió" class="fa fa-plus p-1 text-success"></a> <a href="series_edit.php?type=<?php echo $type; ?>&id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="series_list.php?type=<?php echo $type; ?>&delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir ".$content_prep." «".$row['name']."» i tot el seu material? L’acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="series_edit.php?type=<?php echo $type; ?>" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix un <?php echo $content; ?></a>
					</div>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
