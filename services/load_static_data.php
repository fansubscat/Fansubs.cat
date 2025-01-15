<?php
//This script is designed for very sporadic data insertions
//Mostly used only for the initial inserts of historic fansubs, via CSVs with data extracted from Archive.org
//If the database is already full of news, bad things could happen (all fansub news will be removed, but not files, etc.)
//The images included in the CSV data MUST already be in the server storage (uploaded manually). This script doesn't fetch them.

require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/functions.inc.php');
require_once(__DIR__.'/../common/libraries/parsecsv.php');

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

query("DELETE FROM news WHERE fansub_id=$fansub_id");

foreach ($csv->data as $element){
	query("INSERT INTO news (fansub_id, news_fetcher_id, title, contents, original_contents, date, url, image) VALUES ($fansub_id, NULL, '".escape($element['title'])."','".escape(str_replace("\n","<br />",$element['contents']))."','".escape($element['contents'])."','".$element['date']."','".escape($element['url'])."',".($element['image']!=NULL ? "'".escape($element['image'])."'" : 'NULL').")");
}

log_action('load-static-data', 'S’han carregat notícies via CSV');
?>
