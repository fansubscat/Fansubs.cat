<?php
require_once('db.inc.php');

$resulta = query("SELECT *, a.name, a.session_id,v.series_id FROM folder f LEFT JOIN account a ON f.account_id=a.id LEFT JOIN version v ON f.version_id=v.id");

while ($folder = mysqli_fetch_assoc($resulta)) {
	echo "Updating account ".$folder['name']. " / ".$folder['folder']."\n";

	$output = array();
	$result = 0;
	exec("./mega_list_links.sh ".$folder['session_id']." \"".$folder['folder']."\"", $output, $result);

	if ($result!=0){
		log_action("cron-error",NULL,"Error $result found when processing ".$folder['session_id']." / ".$folder['folder']);
	} else {
		foreach ($output as $line) {
			$filename = explode(":",$line, 2)[0];
			$real_link = explode(":",$line, 2)[1];
			$matches = array();
			if (preg_match('/.* - (\d+).*\.mp4/', $filename, $matches)) {
				$number = $matches[1];
				$resulte = query("SELECT e.id FROM episode e WHERE series_id=".escape($folder['series_id'])." AND number=".$number);
				if ($row = mysqli_fetch_assoc($resulte)) {
					$links = query("SELECT * FROM link WHERE episode_id=".$row['id']." AND version_id=".$folder['version_id']);
					if ($link = mysqli_fetch_assoc($links)) {
						if ($link['url']!=$real_link){
							log_action("cron-update",'link',"Updated link with id ".$link['id']." with new Mega link (file: $filename)");
							query("UPDATE link SET url='".$real_link."' WHERE episode_id=".$row['id']." AND version_id=".$folder['version_id']);
						}
					} else {
						$resultv = query("SELECT * FROM version WHERE id=".$folder['version_id']);
						if ($version = mysqli_fetch_assoc($resultv)){
							$resolution = (!empty($version['default_resolution']) ? "'".$version['default_resolution']."'" : "NULL");

							log_action("cron-insert",'link',"Inserted link with new Mega link (file: $filename)");
							query("INSERT INTO link (version_id,episode_id,extra_name,url,resolution,comments) VALUES(".$folder['version_id'].",".$row['id'].",NULL,'.$real_link',$resolution,NULL)");
							log_action("cron-update",'version',"Updated version with id ".$folder['version_id']." update date because of new Mega link added");
							query("UPDATE version SET updated=CURRENT_TIMESTAMP,updated_by='Cron' WHERE id=".$folder['version_id']);

							//Now check if we need to upgrade in progess -> complete
							$results = query("SELECT * FROM series WHERE id=".escape($folder['series_id']));
							$resultl = query("SELECT DISTINCT l.episode_id FROM link l WHERE l.version_id=".$folder['version_id']." AND l.episode_id IS NOT NULL");

							if (($series = mysqli_fetch_assoc($results)) && ()) {
								if ($series['episodes']==mysqli_num_rows($resultl) && $version['status']==2) {
									log_action("cron-update",'version',"Updated version with id ".$version['id']." to complete due to new Mega link");
									query("UPDATE version SET status=1,updated=CURRENT_TIMESTAMP,updated_by='Cron' WHERE id=".$version['id']);
								}
							} else {
								log_action("cron-match-failed",NULL,"Failed to match link $filename: series or links not found");
							}
							mysqli_free_result($results);
							mysqli_free_result($resultl);
						} else {
								log_action("cron-match-failed",NULL,"Failed to match link $filename: version not found");
						}
						mysqli_free_result($resultv);
					}
					mysqli_free_result($links);
				} else {
					log_action("cron-match-failed",NULL,"Failed to match link $filename: no episode for this number");
				}
				mysqli_free_result($resulte);
			} else {
				log_action("cron-match-failed",NULL,"Failed to match link $filename: does not match regular expression");
			}
		}
	}
}
mysqli_free_result($resulta);
?>
