<?php
$header_title="Detalls del compte remot i carpetes associades - Fansubs";
$page="fansub";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2 && isset($_GET['id']) && is_numeric($_GET['id'])) {
	$result = query("SELECT a.* FROM remote_account a WHERE id=".escape($_GET['id']));
	$row = mysqli_fetch_assoc($result) or crash('Remote account not found');
	mysqli_free_result($result);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1">Detalls del compte remot i carpetes associades</h4>
				<hr>
				<div class="mb-3">
					<label for="form-name">Compte</label>
					<input class="form-control" name="name" type="email" id="form-name" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>" readonly>
				</div>
				<div class="mb-3">
					<label for="form-storage">Ús de l’emmagatzematge <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="Ús de l’emmagatzematge" data-bs-contents="Indica l’emmagatzematge utilitzat al compte. S’actualitza una vegada al dia. Si has afegit el compte fa poc, no està disponible."></small></label>
					<input class="form-control" name="storage" id="form-storage" value="<?php echo $row['total_storage']!=0 ? (number_format($row['used_storage']/$row['total_storage']*100, 2, ',')).'% ('.number_format($row['used_storage']/1024/1024/1024, 2, ',').'/'.number_format($row['total_storage']/1024/1024/1024, 2, ',').' GB)' : 'No disponible'; ?>" readonly>
				</div>
				<div class="mb-3">
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col">Versió associada</th>
								<th scope="col">Nom de carpeta</th>
							</tr>
						</thead>
						<tbody>
<?php
		$resultf = query("SELECT GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name, s.name series_name, rf.folder FROM remote_folder rf LEFT JOIN version v ON rf.version_id=v.id  LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE rf.remote_account_id=".escape($_GET['id'])." GROUP BY v.id, rf.id ORDER BY fansub_name, s.name");
		if (mysqli_num_rows($resultf)==0) {
?>
							<tr>
								<td colspan="2" class="text-center">- No hi ha cap carpeta associada -</td>
							</tr>
<?php
		} else {
			while ($folder = mysqli_fetch_assoc($resultf)) {
?>
							<tr>
								<th scope="row"><?php echo htmlspecialchars($folder['fansub_name'].' - '.$folder['series_name']); ?></th>
								<td><?php echo htmlspecialchars($folder['folder']); ?></td>
							</tr>
<?php
			}
		}
		mysqli_free_result($resultf);
?>
						</tbody>
					</table>
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
