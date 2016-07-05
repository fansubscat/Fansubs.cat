<?php
include_once('db.inc.php');
ob_start();
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

$method = array_shift($request);
if ($method == 'refresh') {
	$token = array_shift($request);
	if ($token!=NULL){
		$result = mysqli_query($db_connection, "SELECT id FROM fansubs WHERE ping_token='".mysqli_real_escape_string($db_connection, $token)."'") or crash('{"status": "ko", "error": "Internal error: '.mysqli_error($db_connection).'"}');
		if ($row = mysqli_fetch_assoc($result)){
			system("cd $services_path && /usr/bin/php fetch.php {$row['id']} > /dev/null &");
			echo '{"status": "ok", "result": "A refresh operation has been scheduled for your fansub."}';
		}
		else{
			http_response_code(401);
			echo '{"status": "ko", "error": "The provided refresh token is invalid."}';
		}
	}
	else{
		http_response_code(400);
		echo '{"status": "ko", error: "No refresh token has been provided."}';
	}
}
else{
	http_response_code(400);
	echo '{"status": "ko", "error": "No valid method specified."}';
}

mysqli_close($db_connection);
?>
