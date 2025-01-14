<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');

validate_hentai();

$result = query_random_series($user);
if ($row = mysqli_fetch_assoc($result)) {
	header("HTTP/1.1 302 Moved Temporarily");
	header("Location: ".$row['default_version_slug']);
	mysqli_free_result($result);
} else {
	http_response_code(404);
	include(__DIR__.'/error.php');
	die();
}
mysqli_close($db_connection);
?>
