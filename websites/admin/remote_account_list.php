<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.remote_account_list.header');
$page="fansub";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-remote-account", "Remote account «".query_single("SELECT name FROM remote_account WHERE id=".escape($_GET['delete_id']))."» (remote account id: ".$_GET['delete_id'].") deleted");
		query("DELETE FROM remote_folder WHERE remote_account_id=".escape($_GET['delete_id']));
		query("DELETE FROM remote_account WHERE id=".escape($_GET['delete_id']));
		$_SESSION['message']=lang('admin.generic.delete_successful');
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.remote_account_list.title'); ?></h4>
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
								<th scope="col"><?php echo lang('admin.remote_account_list.account'); ?></th>
								<th scope="col"><?php echo lang('admin.remote_account_list.fansub'); ?></th>
								<th scope="col"><?php echo lang('admin.remote_account_list.usage'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE a.fansub_id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT a.*, f.name fansub_name FROM remote_account a LEFT JOIN fansub f ON a.fansub_id=f.id$where ORDER BY a.fansub_id IS NULL DESC, f.name ASC, NATURAL_SORT_KEY(a.name) ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center"><?php echo lang('admin.remote_account_list.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th class="align-middle" scope="row"><?php echo htmlspecialchars($row['name']); ?></th>
								<td class="align-middle"><?php echo !empty($row['fansub_name']) ? htmlspecialchars($row['fansub_name']) : sprintf(lang('admin.remote_account_list.fansub.internal'), MAIN_SITE_NAME); ?></td>
								<td class="align-middle text-center storage-details"><div class="progress storage-progress"><div class="progress-value" style="width: <?php echo ($row['total_storage']>0 ? number_format(($row['used_storage']/$row['total_storage']*100), 2, '.') : '0').'%; background: '.(($row['total_storage']>0 && number_format(($row['used_storage']/$row['total_storage']*100), 2, '.')>100) ? 'red' : 'blue').';'; ?>"></div></div><?php echo $row['total_storage']!=0 ? sprintf(lang('admin.remote_account_list.storage_usage.details'), number_format($row['used_storage']/1024/1024/1024, 2, ','), number_format($row['total_storage']/1024/1024/1024, 2, ',')) : lang('admin.remote_account_list.storage_usage.details.unavailable'); ?></th>
								<td class="align-middle text-center text-nowrap"><a href="remote_account_details.php?id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.remote_account_list.details.title'); ?>" class="fa fa-folder-open p-1 text-info"></a> <a href="remote_account_edit.php?id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a> <a href="remote_account_list.php?delete_id=<?php echo $row['id']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(sprintf(lang('admin.remote_account_list.delete_confirm'), $row['name']))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="remote_account_edit.php" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.remote_account_list.create_button'); ?></a>
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
