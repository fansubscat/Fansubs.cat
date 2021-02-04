<?php
$header_title="Edició de versions - Manga";
$page="manga";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">De quin manga vols crear una versió?</h4>
					<hr>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col">Nom</th>
								<th class="text-center" scope="col">Tipus</th>
								<th class="text-center" scope="col">Capítols</th>
								<th class="text-center" scope="col">Versions</th>
								<th class="text-center" scope="col">Acció</th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT m.*,COUNT(DISTINCT v.id) versions FROM manga m LEFT JOIN manga_version v ON m.id=v.manga_id GROUP BY m.id ORDER BY m.name");
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle text-center"><?php echo $row['type']=='oneshot' ? 'One-shot' : 'Serialitzat'; ?></td>
								<td class="align-middle text-center"><?php echo $row['chapters']!=NULL ? ($row['chapters']==-1 ? 'Obert' : $row['chapters']) : '-'; ?></td>
								<td class="align-middle text-center"><?php echo $row['versions']; ?></td>
								<td class="align-middle text-center"><a href="manga_version_edit.php?manga_id=<?php echo $row['id']; ?>" title="Crea'n una versió" class="fa fa-plus-square p-1 text-success"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
					<div class="text-center">
						<a href="manga_edit.php" class="btn btn-primary">No hi és? Afegeix un manga nou</a>
					</div>
<?php
	}
?>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
