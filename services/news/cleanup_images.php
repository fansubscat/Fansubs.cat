<?php
//This script cleans the filesystem of unused images

require_once('db.inc.php');
require_once('functions.inc.php');

$res_fansubs = mysqli_query($db_connection, "SELECT id,slug FROM fansub ORDER BY id") or (mysqli_rollback($db_connection) && die('SQL error'.mysqli_error($db_connection)));

while ($row_fansubs=mysqli_fetch_assoc($res_fansubs)){
	$result = mysqli_query($db_connection, "SELECT image FROM news WHERE fansub_id=".$row_fansubs['id']." AND image IS NOT NULL ORDER BY image") or (mysqli_rollback($db_connection) && die('SQL error'.mysqli_error($db_connection)));

	$values = array();
	while ($row=mysqli_fetch_assoc($result)){
		$values[] = $row['image'];
	}

	if (file_exists($website_directory.'images/news/'.$row_fansubs['slug'])){
		$files = array_diff(scandir($website_directory.'images/news/'.$row_fansubs['slug']), array('..', '.'));
		foreach ($files as $file){
			if (!in_array($file, $values)){
				echo "Removing ".$website_directory.'images/news/'.$row_fansubs['slug']."/$file\n";
				unlink($website_directory.'images/news/'.$row_fansubs['slug']."/$file");
			}
		}
	}
}

mysqli_close($db_connection);
?>
