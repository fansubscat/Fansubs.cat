<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");

validate_hentai();

$result = query_random_series();
if ($row = mysqli_fetch_assoc($result)) {
	header("HTTP/1.1 302 Moved Temporarily");
	header("Location: ".$row['slug']);
	mysqli_free_result($result);
} else {
	http_response_code(404);
	include('error.php');
	die();
}
mysqli_close($db_connection);
?>
