<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.remote_account_edit.header');
$page="fansub";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.id_missing'));
		}
		if (!empty($_POST['fansub_id']) && is_numeric($_POST['fansub_id'])) {
			$data['fansub_id']=escape($_POST['fansub_id']);
		} else {
			$data['fansub_id']='NULL';
		}
		if (!empty($_POST['name'])) {
			$data['name']=escape($_POST['name']);
		} else {
			crash(lang('admin.error.name_missing'));
		}
		if (!empty($_POST['token'])) {
			$data['token']=escape($_POST['token']);
		} else {
			crash(lang('admin.error.token_missing'));
		}
		
		if ($_POST['action']=='edit') {
			$old_result = query("SELECT * FROM remote_account WHERE id=".$data['id']);
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash(lang('admin.error.remote_account_edit_concurrency_error'));
			}
			
			log_action("update-remote-account", "Remote account «".$_POST['name']."» (remote account id: ".$data['id'].") updated");
			query("UPDATE remote_account SET name='".$data['name']."',token='".$data['token']."',fansub_id=".$data['fansub_id'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
		}
		else {
			log_action("create-remote-account", "Remote account «".$_POST['name']."» created");
			query("INSERT INTO remote_account (name,token,fansub_id,total_storage, used_storage, created,created_by,updated,updated_by) VALUES ('".$data['name']."','".$data['token']."',".$data['fansub_id'].",0,0,CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
		}

		$_SESSION['message']=lang('admin.generic.data_saved');

		header("Location: remote_account_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT a.* FROM remote_account a WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.remote_account_not_found'));
		mysqli_free_result($result);
	} else {
		$row = array();
		$row['id'] = '';
		$row['name'] = '';
		$row['updated'] = '';
		$row['token'] = '';
		$row['fansub_id'] = '';
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? lang('admin.remote_account_edit.edit_title') : lang('admin.remote_account_edit.create_title'); ?></h4>
				<hr>
				<form method="post" action="remote_account_edit.php">
					<div class="mb-3">
						<label for="form-name"><?php echo lang('admin.remote_account_edit.account'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.remote_account_edit.account'), lang('admin.remote_account_edit.account.help')); ?></label>
						<input class="form-control" name="name" type="email" id="form-name" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>">
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
						<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-token"><?php echo lang('admin.remote_account_edit.token'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.remote_account_edit.token'), lang('admin.remote_account_edit.token.help')); ?></label>
						<input class="form-control" name="token" id="form-token" required maxlength="200" value="<?php echo htmlspecialchars($row['token']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-fansub_id"><?php echo lang('admin.remote_account_edit.fansub'); ?></label> <?php print_helper_box(lang('admin.remote_account_edit.fansub'), sprintf(lang('admin.remote_account_edit.fansub.help'), MAIN_SITE_NAME)); ?>
						<select name="fansub_id" class="form-select" id="form-fansub_id">
							<option value=""><?php echo sprintf(lang('admin.remote_account_edit.fansub.internal'), MAIN_SITE_NAME); ?></option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
							<option value="<?php echo $frow['id']; ?>"<?php echo $row['fansub_id']==$frow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
						</select>
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? lang('admin.generic.save_changes') : lang('admin.remote_account_edit.create_button'); ?></button>
					</div>
				</form>
			</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
