<?php
$header_title="Fansubs";
$page="fansub";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-fansub", "S'ha suprimit el fansub (id. de fansub: ".$_GET['delete_id'].")");
		query("DELETE FROM fansub WHERE id=".escape($_GET['delete_id']));
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
								<th scope="col">URL</th>
								<th class="text-center" scope="col">Estat</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.name ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center">- No hi ha cap fansub -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['url']); ?></td>
								<td class="align-middle text-center"><?php echo $row['status']==1 ? 'Actiu' : 'Inactiu'; ?></td>
								<td class="align-middle text-center"><a href="fansub_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="fansub_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el fansub '".$row['name']."' i tot el seu material? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
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
