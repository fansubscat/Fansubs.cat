<?php
require_once(__DIR__.'/db.inc.php');

session_name(ADMIN_COOKIE_NAME);
session_set_cookie_params(ADMIN_COOKIE_DURATION, '/', COOKIE_DOMAIN, TRUE, FALSE);
session_start();

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1 && !empty($_POST['view'])) {
	if ($_POST['view']==1 || $_POST['view']==2) {
		$_SESSION['default_view']=$_POST['view'];
	}
	echo json_encode(array(
		"status" => 'ok'
	));
}

mysqli_close($db_connection);
?>
