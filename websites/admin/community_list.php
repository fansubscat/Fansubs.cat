<?php
$header_title="Llista de comunitats - Altres";
$page="other";
include("header.inc.php");

function get_category_name_by_id($id) {
	switch ($id) {
		case 'featured':
			return "Destacats";
		case 'creators':
			return "Creadors";
		case 'music':
			return "Música";
		case 'podcasts':
			return "Pòdcasts";
		default:
			return "Desconeguda";
	}
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-community", "S’ha suprimit la comunitat «".query_single("SELECT name FROM community WHERE id=".escape($_GET['delete_id']))."» (id. de comunitat: ".$_GET['delete_id'].")");
		query("DELETE FROM community WHERE id=".escape($_GET['delete_id']));
		@unlink(STATIC_DIRECTORY.'/images/communities/'.$_GET['delete_id'].'.jpg');
		$_SESSION['message']="S’ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de comunitats</h4>
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
								<th class="text-center" scope="col">Categoria</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT c.* FROM community c ORDER BY c.category='featured' DESC, c.category ASC, c.name ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="8" class="text-center">- No hi ha cap comunitat -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle text-center"><?php echo get_category_name_by_id($row['category']); ?></td>
								<td class="align-middle text-center text-nowrap"><a href="community_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="community_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir la comunitat «".$row['name']."»? L’acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="community_edit.php" class="btn btn-primary"><span class="fa fa-plus pe-2"></span>Afegeix una comunitat</a>
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
