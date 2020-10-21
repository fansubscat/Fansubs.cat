<?php
require_once('db.inc.php');

log_action('cron-recommendations-started', "S'ha iniciat l'actualització periòdica de recomanacions");

$previous_ids=array(-1);
$result = query("SELECT version_id FROM recommendation");
while ($row = mysqli_fetch_assoc($result)) {
	array_push($previous_ids, $row['version_id']);
}
mysqli_free_result($result);

query("DELETE FROM recommendation");
//We hide:
//-In progress, cancelled or abandoned series (in progress ones will be displayed IN ADDITION to these if their "always featured" flag is active)
//-Series with files with no links
//-Not featured elements
//-Hentai
//-Movies which have more than one episode and more than one version, because these tend to be a mix of fansubs and we could end up recommending movie 4 from a set of 8.
//-Recommendations from the previous set
//-Series with a score below 6.0
query("INSERT INTO recommendation SELECT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE (sr.type<>'movie' OR (sr.type='movie' AND sr.episodes=1) OR (sr.type='movie' AND (SELECT COUNT(vr2.id) FROM version vr2 WHERE vr2.series_id=vr.series_id)<=1)) AND sr.rating<>'XXX' AND sr.score>=6 AND vr.is_featurable=1 AND vr.is_always_featured=0 AND vr.status IN (1,3) AND vr.episodes_missing=0 AND vr.id NOT IN (".implode(',', $previous_ids).") ORDER BY RAND() LIMIT 5");

log_action('cron-recommendations-finished', "S'ha completat l'actualització periòdica de recomanacions");

echo "All done!\n";
?>
