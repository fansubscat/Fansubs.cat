<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/common.inc.php');

function get_storage_url($url) {
	if (count(REMOTE_STORAGES)>0 && strpos($url, "storage://")===0) {
		//Always the first storage
		return generate_storage_url(str_replace("storage://", 'https://'.REMOTE_STORAGES[0].'/', $url));
	} else {
		return $url;
	}
}

session_name(ADMIN_COOKIE_NAME);
session_set_cookie_params(ADMIN_COOKIE_DURATION, '/', COOKIE_DOMAIN, TRUE, FALSE);
session_start();

function retrieve_remote_file_size($url){
	$ch = curl_init(str_replace("&", "%26", str_replace(" ", "%20", get_storage_url($url))));

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HEADER, TRUE);
	curl_setopt($ch, CURLOPT_NOBODY, TRUE);
	curl_setopt($ch, CURLOPT_REFERER, 'https://'.REMOTE_STORAGES[0].'/');

	$data = curl_exec($ch);
	$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	$size = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

	curl_close($ch);
	return array($code, $size);
}


if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (count(REMOTE_STORAGES)==0) {
		echo "KO,0";
	} else {
		$response = retrieve_remote_file_size($_GET['link']);
		if ($response[0] != "200" && $response[0]!="206") {
			echo "KO,0";
		} else {
			echo "OK,".$response[1];
		}
	}
}
?>
