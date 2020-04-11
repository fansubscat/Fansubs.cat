<?php
$header_title="Comptes";
$page="account";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete", "account", $_GET['delete_id']);
		query("DELETE FROM folder WHERE account_id=".escape($_GET['delete_id']));
		query("DELETE FROM account WHERE id=".escape($_GET['delete_id']));
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de comptes</h4>
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
								<th scope="col">Fansub</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE a.fansub_id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT a.*, f.name fansub_name FROM account a LEFT JOIN fansub f ON a.fansub_id=f.id$where ORDER BY a.name ASC");
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars($row['fansub_name']); ?></td>
								<td class="align-middle text-center"><a href="account_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="account_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el compte '".$row['name']."'? L'acció no es podrà desfer.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="account_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un compte</a>
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
