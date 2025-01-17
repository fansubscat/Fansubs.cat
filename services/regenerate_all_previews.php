<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/../common/libraries/preview_image_generator.php');

log_action('cron-regenerate-all-previews-started', "Social preview regeneration for all existing versions has started");

$result = query("SELECT * FROM version ORDER BY title ASC");

while ($version = mysqli_fetch_assoc($result)) {
	echo "Regenerating preview image for version «".$version['title']."» (id ".$version['id'].")\n";
	update_version_preview($version['id']);
}

log_action('cron-regenerate-all-previews-finished', "Social preview regeneration for all existing versions has finished");
echo "All done!\n";
?>
