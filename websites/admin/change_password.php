<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.change_password.header');
$page="tools";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		$data['username']=escape($_SESSION['username']);
		if (!empty($_POST['password']) && !empty($_POST['password_confirm']) && $_POST['password']===$_POST['password_confirm'] && strlen($_POST['password'])>=7) {
			$data['password']=$password=hash('sha256', PASSWORD_SALT . $_POST['password']);
		} else {
			crash(lang('admin.error.invalid_data'));
		}
		
		log_action("change-admin-password", "Admin user password for «".$_SESSION['username']."» changed");
		query("UPDATE admin_user SET password='".$data['password']."' WHERE username='".$data['username']."'");

		$changed=TRUE;
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo lang('admin.change_password.title'); ?></h4>
					<hr>

<?php
	if (!empty($changed)) {
?>
					<p class="alert alert-success text-center"><?php echo lang('admin.change_password.result_success'); ?></p>
<?php
	} else {
?>
					<form method="post" action="change_password.php" onsubmit="if ($('#form-password').val()!=$('#form-password_confirm').val()) { alert('<?php echo lang('js.admin.change_password.mismatch'); ?>'); return false; } else { return true; }">
						<div class="mb-3">
							<label for="form-password" class="mandatory"><?php echo lang('admin.change_password.password'); ?></label>
							<input class="form-control" type="password" minlength="7" name="password" required id="form-password" autocomplete="new-password">
						</div>
						<div class="mb-3">
							<label for="form-password_confirm" class="mandatory"><?php echo lang('admin.change_password.repeat_password'); ?></label>
							<input class="form-control" type="password" minlength="7" name="password_confirm" required id="form-password_confirm" autocomplete="new-password">
						</div>
						<div class="mb-3 text-center pt-2">
							<button type="submit" name="action" value="change_password" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo lang('admin.change_password.confirm_button'); ?></button>
						</div>
					</form>
<?php
	}
?>
					
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
