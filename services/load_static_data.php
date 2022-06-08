<?php
//This script is designed for very sporadic data insertions
//Mostly used only for the initial inserts of historic fansubs, via CSVs with data extracted from Archive.org
//If the database is already full of news, bad things could happen (all fansub news will be removed, but not files, etc.)
//The images included in the CSV data MUST already be in the server storage (uploaded manually). This script doesn't fetch them.

require_once('db.inc.php');
require_once('functions.inc.php');
require_once('libs/parsecsv.php');

if (isset($argv) && isset($argv[1]) && isset($argv[2])){
	$fansub_id = $argv[1];
	$file = $argv[2];
}
else{
	die("No fansub or file provided!\nUsage: php load_static_data.php <fansub_id> <file>\n");
}

if (!file_exists($file)){
	die("The file does not exist!\n");
}

$csv = new parseCSV($file);

mysqli_query($db_connection, "DELETE FROM news WHERE fansub_id=$fansub_id") or (mysqli_rollback($db_connection) && die('SQL error'.mysqli_error($db_connection)));

foreach ($csv->data as $element){
	mysqli_query($db_connection, "INSERT INTO news (fansub_id, fetcher_id, title, contents, original_contents, date, url, image) VALUES ($fansub_id, NULL, '".mysqli_real_escape_string($db_connection, $element['title'])."','".mysqli_real_escape_string($db_connection, str_replace("\n","<br />",$element['contents']))."','".mysqli_real_escape_string($db_connection, $element['contents'])."','".$element['date']."','".mysqli_real_escape_string($db_connection, $element['url'])."',".($element['image']!=NULL ? "'".mysqli_real_escape_string($db_connection, $element['image'])."'" : 'NULL').")") or (mysqli_rollback($db_connection) && die('SQL error'.mysqli_error($db_connection)));
}

mysqli_query($db_connection, "INSERT INTO admin_log (action, text, author, date) VALUES ('load-static-data','S\'han carregat notícies via CSV', '(Servei intern)', CURRENT_TIMESTAMP)") or (mysqli_rollback($db_connection) && die('SQL error'.mysqli_error($db_connection)));

mysqli_close($db_connection);
?>
