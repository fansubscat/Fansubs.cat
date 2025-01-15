<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/../common/libraries/preview_image_generator.php');

log_action('cron-regenerate-all-previews-started', "S’ha iniciat la regeneració de previsualitzacions de totes les versions");

$result = query("SELECT * FROM version ORDER BY title ASC");

while ($version = mysqli_fetch_assoc($result)) {
	echo "Regenerating preview image for version «".$version['title']."»\n";
	update_version_preview($version['id']);
}

log_action('cron-regenerate-all-previews-finished', "S’ha completat la regeneració de previsualitzacions de totes les sèries");
echo "All done!\n";
?>
