<?php
require_once('db.inc.php');

log_action('cron-recommendations-started', "S'ha iniciat l'actualització periòdica de recomanacions");

$previous_ids_anime=array(-1);
$result = query("SELECT version_id FROM recommendation");
while ($row = mysqli_fetch_assoc($result)) {
	array_push($previous_ids_anime, $row['version_id']);
}
mysqli_free_result($result);

$previous_ids_manga=array(-1);
$result = query("SELECT manga_version_id FROM manga_recommendation");
while ($row = mysqli_fetch_assoc($result)) {
	array_push($previous_ids_manga, $row['manga_version_id']);
}
mysqli_free_result($result);

query("DELETE FROM recommendation");
query("DELETE FROM manga_recommendation");
//We hide:
//-In progress, cancelled or abandoned series (in progress ones will be displayed IN ADDITION to these if their "always featured" flag is active)
//-Series with files with no links
//-Not featured elements
//-Hentai
//-Anime movies which have more than one episode and more than one version, because these tend to be a mix of fansubs and we could end up recommending movie 4 from a set of 8.
//-Recommendations from the previous set
//-Series with a score below 6.0
query("INSERT INTO recommendation SELECT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE vr.hidden=0 AND (sr.type<>'movie' OR (sr.type='movie' AND sr.episodes=1) OR (sr.type='movie' AND (SELECT COUNT(vr2.id) FROM version vr2 WHERE vr2.series_id=vr.series_id)<=1)) AND (sr.rating<>'XXX' OR sr.rating IS NULL) AND (sr.score>=6 OR sr.score IS NULL OR vr.is_always_featured=1) AND vr.is_featurable=1 AND ((vr.status IN (1,3) AND vr.id NOT IN (".implode(',', $previous_ids_anime).")) OR vr.is_always_featured=1) AND vr.episodes_missing=0 ORDER BY vr.is_always_featured DESC, RAND() LIMIT 10");
query("INSERT INTO manga_recommendation SELECT vr.id FROM manga_version vr LEFT JOIN manga mr ON vr.manga_id=mr.id WHERE vr.hidden=0 AND (mr.rating<>'XXX' OR mr.rating IS NULL) AND (mr.score>=6 PR mr.score IS NULL OR vr.is_always_featured=1) AND vr.is_featurable=1 AND ((vr.status IN (1,3) AND vr.id NOT IN (".implode(',', $previous_ids_manga).")) OR vr.is_always_featured=1) AND vr.chapters_missing=0 ORDER BY vr.is_always_featured DESC, RAND() LIMIT 10");

log_action('cron-recommendations-finished', "S'ha completat l'actualització periòdica de recomanacions");

echo "All done!\n";
?>
