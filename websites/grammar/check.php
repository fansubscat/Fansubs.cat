<?php
header('Content-Type: application/json');

//LanguageTool URL (normally on localhost)
$LANGUAGETOOL_URL = "http://localhost:8081/v2/check";

//Set a high limit because our server is slow
set_time_limit(120);

$profiles_file = "profiles.json";
$profiles_json = file_get_contents($profiles_file);
$profiles = json_decode($profiles_json, true);

$profile_name = isset($_POST['profile']) ? $_POST['profile'] : 'Principatí (diacrítics IEC 2017)';

if (!isset($profiles[$profile_name])) {
	http_response_code(400);
	echo json_encode(["error" => "No s’ha trobat aquest perfil."]);
	exit;
}

$profile = $profiles[$profile_name];

if (!isset($_POST['text'])) {
	http_response_code(400);
	echo json_encode(["error" => "Falta el camp de text."]);
	exit;
}

$params = [
    "text" => $_POST["text"],
    "level" => 'picky',
];

//Add all profile fields
foreach ($profile as $key => $value) {
	$params[$key] = $value;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $LANGUAGETOOL_URL);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 120);
$response = curl_exec($ch);

if (curl_errno($ch)) {
	http_response_code(500);
	echo json_encode(["error" => curl_error($ch)]);
	curl_close($ch);
	exit;
}

curl_close($ch);

echo $response;
?>
