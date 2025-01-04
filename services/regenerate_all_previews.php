<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/libs/preview_image_generator.php');

log_action('cron-regenerate-all-previews-started', "S’ha iniciat la regeneració de previsualitzacions de totes les sèries");

$result = query("SELECT * FROM series ORDER BY name ASC");

while ($series = mysqli_fetch_assoc($result)) {
	echo "Regenerating preview image for series «".$series['name']."»\n";
	update_series_preview($series['id']);
}

log_action('cron-regenerate-all-previews-finished', "S’ha completat la regeneració de previsualitzacions de totes les sèries");
echo "All done!\n";
?>
