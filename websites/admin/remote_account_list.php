<?php
$header_title="Llista de comptes remots - Fansubs";
$page="fansub";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-remote-account", "S'ha suprimit el compte remot '".query_single("SELECT name FROM remote_account WHERE id=".escape($_GET['delete_id']))."' (id. de compte remot: ".$_GET['delete_id'].")");
		query("DELETE FROM remote_folder WHERE remote_account_id=".escape($_GET['delete_id']));
		query("DELETE FROM remote_account WHERE id=".escape($_GET['delete_id']));
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de comptes remots</h4>
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
								<th scope="col">Tipus</th>
								<th scope="col">Fansub</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE a.fansub_id='.$_SESSION['fansub_id'].' OR a.fansub_id IS NULL';
	} else {
		$where = '';
	}
	$result = query("SELECT a.*, f.name fansub_name FROM remote_account a LEFT JOIN fansub f ON a.fansub_id=f.id$where ORDER BY a.type='storage' DESC, a.name ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="3" class="text-center">- No hi ha cap compte remot -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle"><?php echo $row['type']=='mega' ? 'MEGA' : 'Desconegut'; ?></th>
								<td class="align-middle"><?php echo !empty($row['fansub_name']) ? htmlspecialchars($row['fansub_name']) : '(Tots)'; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="remote_account_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="remote_account_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el compte remot '".$row['name']."'? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="remote_account_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un compte remot</a>
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
