<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.admin_list.header');
$page="other";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['delete_id'])) {
		log_action("delete-admin-user", "Admin user «".$_GET['delete_id']."» deleted");
		query("DELETE FROM admin_user WHERE username='".escape($_GET['delete_id'])."'");
		$_SESSION['message']=lang('admin.generic.delete_successful');
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.admin_list.title'); ?></h4>
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
								<th scope="col"><?php echo lang('admin.admin_list.user'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.admin_list.admin_level'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.admin_list.fansub'); ?></th>
								<th class="text-center" scope="col"><?php echo lang('admin.generic.actions'); ?></th>
							</tr>
						</thead>
						<tbody>
<?php
	$result = query("SELECT u.*, f.name fansub_name FROM admin_user u LEFT JOIN fansub f ON u.fansub_id=f.id ORDER BY u.username ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="4" class="text-center"><?php echo lang('admin.admin_list.empty'); ?></td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo $row['username']; ?></th>
								<td class="align-middle text-center"><?php echo $row['admin_level']; ?></td>
								<td class="align-middle text-center"><?php echo $row['fansub_name']!=NULL ? $row['fansub_name'] : '-'; ?></td>
								<td class="align-middle text-center text-nowrap"><a href="admin_edit.php?id=<?php echo $row['username']; ?>" title="<?php echo lang('admin.generic.edit.title'); ?>" class="fa fa-edit p-1"></a> <a href="admin_list.php?delete_id=<?php echo $row['username']; ?>" title="<?php echo lang('admin.generic.delete.title'); ?>" onclick="return confirm(<?php echo htmlspecialchars(json_encode(sprintf(lang('admin.admin_list.delete_confirm'), $row['username']))); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="admin_edit.php" class="btn btn-primary"><span class="fa fa-plus pe-2"></span><?php echo lang('admin.admin_list.create_button'); ?></a>
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
