<?php
$header_title="Inicia la sessió";
$skip_navbar=TRUE;
include("header.inc.php");

if (!empty($_SESSION['username'])) {
	header("Location: index.php");
	die();
}
else if (isset($_POST['username']) && isset($_POST['password'])) {
	//No need to escape the password string since it will be hashed anyway, and the hash does not cause SQL injections
	$username=escape($_POST['username']);
	$password=hash('sha256', PASSWORD_SALT . $_POST['password']);

	$result=query("SELECT * FROM admin_user u WHERE username='".$username."' AND password='".$password."'");

	if (mysqli_num_rows($result)==1) {
		$row = mysqli_fetch_assoc($result);
		$_SESSION['username']=$row['username'];
		$_SESSION['admin_level']=$row['admin_level'];
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
		<div class="container d-flex h-100 justify-content-center align-items-center">
			<div class="card">
				<article class="card-body">
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
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-user"></i></span>
								 </div>
								<input name="username" class="form-control" placeholder="Nom d'usuari" required autofocus>
							</div>
						</div>
						<div class="form-group">
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text"><i class="fa fa-lock"></i></span>
								 </div>
								<input name="password" class="form-control" placeholder="Contrasenya" type="password" required>
							</div>
						</div>
						<div class="form-group">
							<button type="submit" class="btn btn-primary btn-block">Inicia la sessió</button>
						</div>
					</form>
				</article>
			</div>
		</div>
<?php
include("footer.inc.php");
?>
