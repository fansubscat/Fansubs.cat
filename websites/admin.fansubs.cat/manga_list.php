<?php
$header_title="Llista de manga - Manga";
$page="manga";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-manga", "S'ha suprimit el manga '".query_single("SELECT name FROM manga WHERE id=".escape($_GET['delete_id']))."' (id. de manga: ".$_GET['delete_id'].")");
		query("DELETE FROM rel_manga_genre WHERE manga_id=".escape($_GET['delete_id']));
		query("DELETE FROM chapter WHERE manga_id=".escape($_GET['delete_id']));
		query("DELETE FROM manga_version WHERE manga_id=".escape($_GET['delete_id']));
		query("DELETE FROM manga WHERE id=".escape($_GET['delete_id']));
		@unlink('../manga.fansubs.cat/images/manga/'.$_GET['delete_id'].'.jpg');
		@unlink('../manga.fansubs.cat/images/featured/'.$_GET['delete_id'].'.jpg');
		//Cascaded deletions: link, rel_version_fansub, views
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de manga</h4>
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
								<th class="text-center" scope="col">Volums</th>
								<th class="text-center" scope="col">Capítols</th>
								<th class="text-center" scope="col">Versions</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT s.*,(SELECT COUNT(DISTINCT v.id) FROM manga_version v WHERE v.manga_id=s.id) versions,(SELECT COUNT(DISTINCT ss.id) FROM volume ss WHERE ss.manga_id=s.id) volumes, SUM(ISNULL(e.number)) specials FROM manga s LEFT JOIN chapter e ON s.id=e.manga_id GROUP BY s.id ORDER BY s.name");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="6" class="text-center">- No hi ha cap manga -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle text-center"><?php echo $row['type']=='oneshot' ? 'One-shot' : 'Serialitzat'; ?></td>
								<td class="align-middle text-center"><?php echo $row['volumes']; ?></td>
								<td class="align-middle text-center"><?php echo ($row['chapters']!=-1 ? $row['chapters'] : 'Oberta').($row['specials']>0 ? '<small>+'.$row['specials'].'</small>' : ''); ?></td>
								<td class="align-middle text-center"><?php echo $row['versions']; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="manga_version_edit.php?manga_id=<?php echo $row['id']; ?>" title="Crea'n una versió" class="fa fa-plus p-1 text-success"></a> <a href="manga_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="manga_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el manga '".$row['name']."' i tot el seu material? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="manga_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un manga</a>
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
