<?php
header('Content-Type: application/json');
$profiles_file = "profiles.json";
$profiles_json = file_get_contents($profiles_file);
$profiles = json_decode($profiles_json, true);
$names = array_keys($profiles);
echo json_encode($names);
?>
