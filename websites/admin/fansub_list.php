<?php
$header_title="Llista de fansubs - Fansubs";
$page="fansub";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-fansub", "S'ha suprimit el fansub '".query_single("SELECT name FROM fansub WHERE id=".escape($_GET['delete_id']))."' (id. de fansub: ".$_GET['delete_id'].")");
		query("DELETE FROM fansub WHERE id=".escape($_GET['delete_id']));
		@unlink($static_directory.'/images/icons/'.$_GET['delete_id'].'.jpg');
		@unlink($static_directory.'/images/logos/'.$_GET['delete_id'].'.jpg');
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de fansubs</h4>
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
								<th scope="col">Enllaços</th>
								<th class="text-center" scope="col">Estat</th>
								<th class="text-center" scope="col">Notícies</th>
								<th class="text-center" scope="col">Versions<br />d'anime</th>
								<th class="text-center" scope="col">Versions<br />de&nbsp;manga</th>
								<th class="text-center" scope="col">Versions<br />d'acció real</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT f.id, f.name, f.url, f.status, f.is_historical, f.twitter_url, f.archive_url, (SELECT COUNT(*) FROM news WHERE fansub_id=f.id) news, (SELECT COUNT(*) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='anime' AND vf.fansub_id=f.id) anime_versions, (SELECT COUNT(*) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND vf.fansub_id=f.id) manga_versions, (SELECT COUNT(*) FROM rel_version_fansub vf LEFT JOIN version v ON vf.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='liveaction' AND vf.fansub_id=f.id) liveaction_versions FROM fansub f ORDER BY f.status DESC, f.name ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="8" class="text-center">- No hi ha cap fansub -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle">
<?php

		$links = '';
		if (!empty($row['url'])) {
			$links.='<a href="'.htmlspecialchars($row['url']) . '" target="_blank">Web'.($row['historical']==1 ? ' (morta)' : '') . '</a>';
		}
		if (!empty($row['twitter_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['twitter_url']).'" target="_blank">Twitter</a>';
		}
		if (!empty($row['archive_url'])) {
			if (!empty($links)){
				$links.=' | ';
			}
			$links.='<a href="'.htmlspecialchars($row['archive_url']).'" target="_blank">Web a Archive.org</a>';
		}
		echo $links;
?>
								</td>
								<td class="align-middle text-center"><?php echo $row['status']==1 ? 'Actiu' : 'Inactiu'; ?></td>
								<td class="align-middle text-center"><?php echo $row['news']; ?></td>
								<td class="align-middle text-center"><?php echo $row['anime_versions']; ?></td>
								<td class="align-middle text-center"><?php echo $row['manga_versions']; ?></td>
								<td class="align-middle text-center"><?php echo $row['liveaction_versions']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="fansub_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="fansub_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el fansub '".$row['name']."' i tot el seu material? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="fansub_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un fansub</a>
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
