<?php
session_start();
if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	file_get_contents("https://drive.google.com/file/d/".$_GET['link']."/preview", false, stream_context_create(['http' => ['ignore_errors' => true]]));

	$status_line = $http_response_header[0];
	preg_match('{HTTP\/\S*\s(\d{3})}', $status_line, $match);
	$status = $match[1];
	if ($status !== "200") {
		echo "KO";
	} else {
		echo "OK";
	}
}
?>
