<?php
require_once(__DIR__.'/db.inc.php');

log_action('cron-recommendations-started', "S’ha iniciat l’actualització periòdica de recomanacions");

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
//-Anime movies which have more than one episode and more than one version, because these tend to be a mix of fansubs and we could end up recommending movie 4 from a set of 8.
//-Recommendations from the previous set
//-Series with a score below 6.0
//First, non-hentai:
query("INSERT INTO recommendation SELECT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='anime' AND vr.is_hidden=0 AND (sr.subtype<>'movie' OR (sr.subtype='movie' AND sr.number_of_episodes=1) OR (sr.subtype='movie' AND (SELECT COUNT(vr2.id) FROM version vr2 WHERE vr2.series_id=vr.series_id)<=1)) AND sr.rating<>'XXX' AND (sr.score>=6 OR sr.score IS NULL OR vr.featurable_status>=2) AND vr.featurable_status>=1 AND ((vr.status IN (1,3) AND vr.id NOT IN (".implode(',', $previous_ids).")) OR vr.featurable_status>=2) AND vr.is_missing_episodes=0 ORDER BY vr.featurable_status DESC, RAND() LIMIT 10");
query("INSERT INTO recommendation SELECT vr.id FROM version vr LEFT JOIN series mr ON vr.series_id=mr.id WHERE mr.type='manga' AND vr.is_hidden=0 AND mr.rating<>'XXX' AND (mr.score>=6 OR mr.score IS NULL OR vr.featurable_status>=2) AND vr.featurable_status>=1 AND ((vr.status IN (1,3) AND vr.id NOT IN (".implode(',', $previous_ids).")) OR vr.featurable_status>=2) AND vr.is_missing_episodes=0 ORDER BY vr.featurable_status DESC, RAND() LIMIT 10");
query("INSERT INTO recommendation SELECT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='liveaction' AND vr.is_hidden=0 AND (sr.subtype<>'movie' OR (sr.subtype='movie' AND sr.number_of_episodes=1) OR (sr.subtype='movie' AND (SELECT COUNT(vr2.id) FROM version vr2 WHERE vr2.series_id=vr.series_id)<=1)) AND sr.rating<>'XXX' AND (sr.score>=6 OR sr.score IS NULL OR vr.featurable_status>=2) AND vr.featurable_status>=1 AND ((vr.status IN (1,3) AND vr.id NOT IN (".implode(',', $previous_ids).")) OR vr.featurable_status>=2) AND vr.is_missing_episodes=0 ORDER BY vr.featurable_status DESC, RAND() LIMIT 10");
//Now hentai:
query("INSERT INTO recommendation SELECT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='anime' AND vr.is_hidden=0 AND (sr.subtype<>'movie' OR (sr.subtype='movie' AND sr.number_of_episodes=1) OR (sr.subtype='movie' AND (SELECT COUNT(vr2.id) FROM version vr2 WHERE vr2.series_id=vr.series_id)<=1)) AND sr.rating='XXX' AND (sr.score>=6 OR sr.score IS NULL OR vr.featurable_status>=2) AND vr.featurable_status>=1 AND ((vr.status IN (1,3) AND vr.id NOT IN (".implode(',', $previous_ids).")) OR vr.featurable_status>=2) AND vr.is_missing_episodes=0 ORDER BY vr.featurable_status DESC, RAND() LIMIT 10");
//query("INSERT INTO recommendation SELECT vr.id FROM version vr LEFT JOIN series mr ON vr.series_id=mr.id WHERE mr.type='manga' AND vr.is_hidden=0 AND mr.rating='XXX' AND (mr.score>=6 OR mr.score IS NULL OR vr.featurable_status>=2) AND vr.featurable_status>=1 AND ((vr.status IN (1,3) AND vr.id NOT IN (".implode(',', $previous_ids).")) OR vr.featurable_status>=2) AND vr.is_missing_episodes=0 ORDER BY vr.featurable_status DESC, RAND() LIMIT 10");
//SPECIAL CASE: Since we have a shortage on hentai manga, just limit to 5 for now:
query("INSERT INTO recommendation SELECT vr.id FROM version vr LEFT JOIN series mr ON vr.series_id=mr.id WHERE mr.type='manga' AND vr.is_hidden=0 AND mr.rating='XXX' AND (mr.score>=6 OR mr.score IS NULL OR vr.featurable_status>=2) AND vr.featurable_status>=1 AND ((vr.status IN (1,3) AND vr.id NOT IN (".implode(',', $previous_ids).")) OR vr.featurable_status>=2) AND vr.is_missing_episodes=0 ORDER BY vr.featurable_status DESC, RAND() LIMIT 5");

log_action('cron-recommendations-finished', "S’ha completat l’actualització periòdica de recomanacions");

echo "All done!\n";
?>
