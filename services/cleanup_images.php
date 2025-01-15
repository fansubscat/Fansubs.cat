<?php
//This script cleans the filesystem of unused images

require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/functions.inc.php');

//define('DRY_RUN', TRUE);

$res_fansubs = query("SELECT id,slug FROM fansub ORDER BY id");

while ($row_fansubs=mysqli_fetch_assoc($res_fansubs)){
	$result = query("SELECT image FROM news WHERE fansub_id=".$row_fansubs['id']." AND image IS NOT NULL ORDER BY image");

	$values = array();
	while ($row=mysqli_fetch_assoc($result)){
		$values[] = $row['image'];
	}

	if (file_exists(STATIC_DIRECTORY.'/images/news/'.$row_fansubs['slug'])){
		$files = array_diff(scandir(STATIC_DIRECTORY.'/images/news/'.$row_fansubs['slug']), array('..', '.'));
		foreach ($files as $file){
			if (!in_array($file, $values)){
				echo "Removing ".STATIC_DIRECTORY.'/images/news/'.$row_fansubs['slug']."/$file\n";
				if (defined('DRY_RUN')) {
					continue;
				}
				unlink(STATIC_DIRECTORY.'/images/news/'.$row_fansubs['slug']."/$file");
			}
		}
	}
}

log_action('cleanup-images', 'Unused news images have been cleaned up');
?>
