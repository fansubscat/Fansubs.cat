<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.admin_edit.header');
$page="other";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['username'])) {
			$data['username']=escape($_POST['username']);
		} else {
			crash(lang('admin.error.username_missing'));
		}
		if (!empty($_POST['username_old'])) {
			$data['username_old']=escape($_POST['username_old']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.username_old_missing'));
		} else {
			$data['username_old']=NULL;
		}
		if (!empty($_POST['password'])) {
			$data['password']=$password=hash('sha256', PASSWORD_SALT . $_POST['password']);
		} else if ($_POST['action']=='edit') {
			$data['password']=NULL;
		} else {
			crash(lang('admin.error.password_missing'));
		}
		if (!empty($_POST['admin_level']) && is_numeric($_POST['admin_level'])) {
			$data['admin_level']=escape($_POST['admin_level']);
		} else {
			crash(lang('admin.error.admin_level_missing'));
		}
		if (!empty($_POST['fansub_id']) && is_numeric($_POST['fansub_id'])) {
			$data['fansub_id']=escape($_POST['fansub_id']);
		} else {
			$data['fansub_id']="NULL";
		}
		if (!empty($_POST['default_storage_processing']) && is_numeric($_POST['default_storage_processing'])) {
			$data['default_storage_processing']=escape($_POST['default_storage_processing']);
		} else {
			crash(lang('admin.error.default_storage_processing_missing'));
		}
		if (!empty($_POST['disabled']) && $_POST['disabled']==1) {
			$data['disabled']=1;
		} else {
			$data['disabled']=0;
		}
		
		if ($_POST['action']=='edit') {
			$old_result = query("SELECT * FROM admin_user WHERE username='".$data['username_old']."'");
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash(lang('admin.error.admin_edit_concurrency_error'));
			}
			
			log_action("update-admin-user", "Admin user «".$_POST['username']."» updated");
			if ($data['password']!=NULL) {
				query("UPDATE admin_user SET username='".$data['username']."',password='".$data['password']."',admin_level=".$data['admin_level'].",fansub_id=".$data['fansub_id'].",default_storage_processing=".$data['default_storage_processing'].",disabled=".$data['disabled'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE username='".$data['username_old']."'");
			} else {
				query("UPDATE admin_user SET username='".$data['username']."',admin_level=".$data['admin_level'].",fansub_id=".$data['fansub_id'].",default_storage_processing=".$data['default_storage_processing'].",disabled=".$data['disabled'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE username='".$data['username_old']."'");
			}
		}
		else {
			log_action("create-admin-user", "Admin user «".$_POST['username']."» created");
			query("INSERT INTO admin_user (username,password,admin_level,fansub_id,default_storage_processing,disabled,created,created_by,updated,updated_by) VALUES ('".$data['username']."','".$data['password']."',".$data['admin_level'].",".$data['fansub_id'].",".$data['default_storage_processing'].",".$data['disabled'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
		}

		$_SESSION['message']=lang('admin.generic.data_saved');

		header("Location: admin_list.php");
		die();
	}

	if (!empty($_GET['id'])) {
		$result = query("SELECT u.* FROM admin_user u WHERE username='".escape($_GET['id'])."'");
		$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.admin_not_found'));
		mysqli_free_result($result);
	} else {
		$row = array();
		$row['username']='';
		$row['updated']='';
		$row['admin_level']='';
		$row['fansub_id']='';
		$row['default_storage_processing']='';
		$row['disabled']='';
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['username']) ? lang('admin.admin_edit.edit_title') : lang('admin.admin_edit.create_title'); ?></h4>
					<hr>
					<form method="post" action="admin_edit.php">
						<div class="mb-3">
							<label for="form-user" class="mandatory"><?php echo lang('admin.admin_edit.username'); ?></label> <?php print_helper_box(lang('admin.admin_edit.username'), lang('admin.admin_edit.username.help')); ?>
							<input class="form-control" name="username" id="form-user" required maxlength="200" value="<?php echo $row['username']; ?>" autocomplete="new-password">
							<input type="hidden" name="username_old" value="<?php echo htmlspecialchars($row['username']); ?>">
							<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
						</div>
						<div class="mb-3">
<?php
	if ($row['username']==NULL) {
?>
							<label for="form-password" class="mandatory"><?php echo lang('admin.admin_edit.password'); ?></label> <?php print_helper_box(lang('admin.admin_edit.password'), lang('admin.admin_edit.password.help')); ?>
							<input class="form-control" type="password" name="password" required id="form-password" autocomplete="new-password">
<?php
	} else {
?>
							<label for="form-password"><?php echo lang('admin.admin_edit.password_edit'); ?></label> <?php print_helper_box(lang('admin.admin_edit.password'), lang('admin.admin_edit.password.help')); ?>
							<input class="form-control" type="password" name="password" id="form-password" autocomplete="new-password">
<?php
	}
?>
						</div>
						<div class="mb-3">
							<label for="form-admin-level" class="mandatory"><?php echo lang('admin.admin_edit.admin_level'); ?></label> <?php print_helper_box(lang('admin.admin_edit.admin_level'), lang('admin.admin_edit.admin_level.help')); ?>
							<select class="form-select" name="admin_level" id="form-admin-level" required>
								<option value=""><?php echo lang('admin.admin_edit.admin_level.select'); ?></option>
								<option value="1"<?php echo $row['admin_level']==1 ? " selected" : ""; ?>><?php echo lang('admin.admin_edit.admin_level.level_1'); ?></option>
								<option value="2"<?php echo $row['admin_level']==2 ? " selected" : ""; ?>><?php echo lang('admin.admin_edit.admin_level.level_2'); ?></option>
								<option value="3"<?php echo $row['admin_level']==3 ? " selected" : ""; ?>><?php echo lang('admin.admin_edit.admin_level.level_3'); ?></option>
							</select>
						</div>
						<div class="mb-3">
							<label for="form-fansub"><?php echo lang('admin.admin_edit.fansub'); ?></label> <?php print_helper_box(lang('admin.admin_edit.fansub'), lang('admin.admin_edit.fansub.help')); ?>
							<select name="fansub_id" class="form-select" id="form-fansub">
								<option value=""><?php echo lang('admin.admin_edit.fansub.no_fansub'); ?></option>
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
						<div class="mb-3">
							<label for="form-default_storage_processing" class="mandatory"><?php echo lang('admin.admin_edit.admin_level.default_storage_processing'); ?></label> <?php print_helper_box(lang('admin.admin_edit.admin_level.default_storage_processing'), lang('admin.admin_edit.admin_level.default_storage_processing.help')); ?>
							<select class="form-select" name="default_storage_processing" id="form-default_storage_processing" required>
								<option value=""><?php echo lang('admin.admin_edit.admin_level.default_storage_processing.select'); ?></option>
								<option value="1"<?php echo $row['default_storage_processing']==1 ? " selected" : ""; ?>><?php echo lang('admin.admin_edit.admin_level.default_storage_processing.save_copy'); ?></option>
								<option value="5"<?php echo $row['default_storage_processing']==5 ? " selected" : ""; ?>><?php echo lang('admin.admin_edit.admin_level.default_storage_processing.do_not_save_copy'); ?></option>
							</select>
						</div>
						<div class="mb-3">
							<label for="form-disabled"><?php echo lang('admin.admin_edit.admin_level.status'); ?></label> <?php print_helper_box(lang('admin.admin_edit.admin_level.status'), lang('admin.admin_edit.admin_level.status.help')); ?>
							<div id="form-status">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="disabled" id="form-disabled" value="1"<?php echo $row['disabled']==1? " checked" : ""; ?>>
									<label class="form-check-label" for="form-disabled"><?php echo lang('admin.admin_edit.admin_level.status.disabled'); ?></label>
								</div>
							</div>
						</div>
						<div class="mb-3 text-center pt-2">
							<button type="submit" name="action" value="<?php echo !empty($row['username']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['username']) ? lang('admin.generic.save_changes') : lang('admin.admin_edit.create_button'); ?></button>
						</div>
					</form>
					
				</article>
			</div>
		</div>
<?php
}

else{
	header("Location: login.php");
}



include(__DIR__.'/footer.inc.php');
?>
