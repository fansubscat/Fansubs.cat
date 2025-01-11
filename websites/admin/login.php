<?php
$header_title="Inicia la sessió";
$skip_navbar=TRUE;
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username'])) {
	header("Location: index.php");
	die();
}
else if (isset($_POST['username']) && isset($_POST['password'])) {
	//No need to escape the password string since it will be hashed anyway, and the hash does not cause SQL injections
	$username=escape($_POST['username']);
	$password=escape(hash('sha256', PASSWORD_SALT . $_POST['password']));
	
	$successful_login = FALSE;
	
	if (mysqli_num_rows(query("SELECT * FROM admin_user u LIMIT 1"))==0) {
		$result=query("INSERT INTO admin_user (username, password, admin_level, fansub_id, default_storage_processing, created, created_by, updated, updated_by) VALUES
('$username', '$password', 3, NULL, 5, CURRENT_TIMESTAMP, '$username', CURRENT_TIMESTAMP, '$username')");
	}
	
	$result=query("SELECT * FROM admin_user u WHERE username='".$username."' AND password='".$password."'");
	$successful_login = (mysqli_num_rows($result)==1);

	if ($successful_login) {
		$row = mysqli_fetch_assoc($result);
		$_SESSION['username']=$row['username'];
		$_SESSION['admin_level']=$row['admin_level'];
		$_SESSION['default_storage_processing']=$row['default_storage_processing'];
		$_SESSION['fansub_id']=$row['fansub_id'];
		header("Location: index.php");
		mysqli_free_result($result);
		die();
	}
	else{
		$invalid=TRUE;
		mysqli_free_result($result);
	}
}
?>
		<div class="container d-flex flex-column h-100 justify-content-center align-items-center">
<?php
$result=query("SELECT * FROM admin_user u LIMIT 1");

if (mysqli_num_rows($result)==0) {
?>
			<p class="alert alert-danger text-center">Instal·lació nova sense cap administrador donat d’alta.<br>Inicia sessió amb l’usuari i contrasenya que vulguis: esdevindran l’usuari i contrasenya del primer administrador.</p>
<?php
}
?>
			<div class="card">
				<article class="card-body text-center">
					<h4 class="card-title text-center mb-4 mt-1">Inicia la sessió</h4>
					<hr>
<?php
	if (!empty($invalid)) {
?>
					<p class="text-center text-danger">Usuari o contrasenya invàlids.</p>
<?php
	} else {
?>
					<p class="text-center">Inicia la sessió per a continuar.</p>
<?php
	}
?>
					<form method="post" action="login.php">
						<div class="mb-3">
							<div class="input-group">
								<div class="input-group-text"><i class="fa fa-user"></i></div>
								<input name="username" class="form-control" placeholder="Nom d’usuari" required autofocus>
							</div>
						</div>
						<div class="mb-3">
							<div class="input-group">
								<div class="input-group-text"><i class="fa fa-lock"></i></div>
								<input name="password" class="form-control" placeholder="Contrasenya" type="password" required>
							</div>
						</div>
						<div class="mb-3">
							<button type="submit" class="btn btn-primary btn-block">Inicia la sessió</button>
						</div>
					</form>
				</article>
			</div>
		</div>
<?php
include(__DIR__.'/footer.inc.php');
?>
