<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.remote_account_details.header');
$page="fansub";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2 && isset($_GET['id']) && is_numeric($_GET['id'])) {
	$result = query("SELECT a.* FROM remote_account a WHERE id=".escape($_GET['id']));
	$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.remote_account_not_found'));
	mysqli_free_result($result);
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.remote_account_details.title'); ?></h4>
				<hr>
				<div class="mb-3">
					<label for="form-name"><?php echo lang('admin.remote_account_details.account'); ?></label>
					<input class="form-control" name="name" type="email" id="form-name" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>" readonly>
				</div>
				<div class="mb-3">
					<label for="form-storage"><?php echo lang('admin.remote_account_details.storage_usage'); ?> <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="<?php echo lang('admin.remote_account_details.storage_usage'); ?>" data-bs-contents="<?php echo lang('admin.remote_account_details.storage_usage.help'); ?>"></small></label>
					<input class="form-control" name="storage" id="form-storage" value="<?php echo $row['total_storage']!=0 ? sprintf(lang('admin.remote_account_details.storage_usage.details'), (number_format($row['used_storage']/$row['total_storage']*100, 2, ',')), number_format($row['used_storage']/1024/1024/1024, 2, ','), number_format($row['total_storage']/1024/1024/1024, 2, ',')) : lang('admin.remote_account_details.storage_usage.details.unavailable'); ?>" readonly>
				</div>
				<div class="mb-3">
					<table class="table table-hover table-striped">
						<thead class="table-dark">
							<tr>
								<th scope="col"><?php echo lang('admin.remote_account_details.related_version'); ?></th>
								<th scope="col"><?php echo lang('admin.remote_account_details.folder_name'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
		$resultf = query("SELECT GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name, s.name series_name, rf.folder FROM remote_folder rf LEFT JOIN version v ON rf.version_id=v.id  LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN series s ON v.series_id=s.id WHERE rf.remote_account_id=".escape($_GET['id'])." GROUP BY v.id, rf.id ORDER BY fansub_name, s.name");
		if (mysqli_num_rows($resultf)==0) {
?>
							<tr>
								<td colspan="2" class="text-center"><?php echo lang('admin.remote_account_details.empty'); ?></td>
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
