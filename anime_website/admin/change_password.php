<?php
$header_title="Eines";
$page="tools";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		$data['username']=escape($_SESSION['username']);
		if (!empty($_POST['password']) && !empty($_POST['password_confirm']) && $_POST['password']===$_POST['password_confirm'] && strlen($_POST['password'])>=7) {
			$data['password']=$password=hash('sha256', $password_salt . $_POST['password']);
		} else {
			crash("Dades invàlides");
		}
		
		log_action("change-password", "S'ha canviat la contrasenya de l'usuari amb nom '".$data['username']."'");
		query("UPDATE user SET password='".$data['password']."' WHERE username='".$data['username']."'");

		$changed=TRUE;
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Canvia la contrasenya</h4>
					<hr>

<?php
	if (!empty($changed)) {
?>
					<p class="alert alert-success text-center">S'ha canviat la contrasenya.</p>
<?php
	} else {
?>
					<form method="post" action="change_password.php" onsubmit="if ($('#form-password').val()!=$('#form-password_confirm').val()) { alert('Les contrasenyes no coincideixen.'); return false; } else { return true; }">
						<div class="form-group">
							<label for="form-password" class="mandatory">Contrasenya</label>
							<input class="form-control" type="password" minlength="7" name="password" required id="form-password">
						</div>
						<div class="form-group">
							<label for="form-password_confirm" class="mandatory">Repeteix la contrasenya</label>
							<input class="form-control" type="password" minlength="7" name="password_confirm" required id="form-password_confirm">
						</div>
						<div class="form-group text-center pt-2">
							<button type="submit" name="action" value="change_password" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span>Canvia la contrasenya</button>
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

include("footer.inc.php");
?>
