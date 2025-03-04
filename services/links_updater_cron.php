<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/../common/libraries/preview_image_generator.php');

log_action('cron-links-updater-started', "Automatic link retrieval has started");

$resulta = query("SELECT f.*, a.id remote_account_id, a.name, a.token, v.series_id FROM remote_folder f LEFT JOIN remote_account a ON f.remote_account_id=a.id LEFT JOIN version v ON f.version_id=v.id WHERE is_active=1");

$lock_pointer = fopen(MEGA_LOCK_FILE, "w+");

//We acquire a file lock to prevent two invocations at the same time.
//This could happen if a user is asking for manual file sync while this cron runs.
if (flock($lock_pointer, LOCK_EX)) {
	while ($folder = mysqli_fetch_assoc($resulta)) {
		echo "Updating remote account ".$folder['name']. " / ".$folder['folder']."\n";

		$output = array();
		$result = 0;
		exec("./mega_list_links.sh ".$folder['token']." \"".$folder['folder']."\"", $output, $result);

		if ($result!=0){
			log_action("cron-error","Found error $result when processing remote folder «".$folder['folder']."» for remote account «".$folder['name']."» (version id ".$folder['version_id'].")");
		} else {
			$processed_numbers = array();
			foreach ($output as $line) {
				$filename = explode(":::",$line, 2)[0];
				$real_link = explode(":::",$line, 2)[1];
				$matches = array();
				if (preg_match('/.* - (\d+).*\.(?:mp4|mkv|avi)/', $filename, $matches)) {
					$number = $matches[1];
					if (!in_array($number, $processed_numbers)) {
						$resulte = query("SELECT e.id FROM episode e WHERE series_id=".escape($folder['series_id'])." AND linked_episode_id IS NULL AND number=".$number.(!empty($folder['division_id']) ? " AND division_id=".$folder['division_id'] : ''));
						if ($row = mysqli_fetch_assoc($resulte)) {
							$resultv = query("SELECT v.*, s.subtype FROM version v LEFT JOIN series s ON v.series_id=s.id WHERE v.id=".$folder['version_id']);
							if ($version = mysqli_fetch_assoc($resultv)){
								$resolution = escape($folder['default_resolution']);
								$files = query("SELECT * FROM file WHERE episode_id=".$row['id']." AND version_id=".$folder['version_id']);
								//WARNING: We must prevent the version from having multiple links if autofetch is enabled, or bad things will happen!!!
								if ($file = mysqli_fetch_assoc($files)) {
									//File exists, let's check the links and replace the first one...
									$links = query("SELECT * FROM link WHERE file_id=".$file['id']." AND url LIKE 'https://mega.nz/%'");
									if ($link = mysqli_fetch_assoc($links)) {
										if ($link['url']!=$real_link){
											query("UPDATE link SET url='".escape($real_link)."',updated=CURRENT_TIMESTAMP,updated_by='Cron' WHERE id=".$link['id']);
											//Remove the storage link so it gets regenerated by the watcher script
											query("DELETE FROM link WHERE file_id=".$file['id']." AND url LIKE 'storage://%'");
											log_action("cron-update-link","File link updated automatically for file «${filename}» (link id ".$link['id'].", file id ".$file['id'].", version id ".$folder['version_id'].")");
											//We do not update the files_updated field because we don't want the series to show in "last updated"
										}
									} else {
										query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES(".$file['id'].",'".escape($real_link)."','$resolution',CURRENT_TIMESTAMP,'Cron',CURRENT_TIMESTAMP,'Cron')");
										query("UPDATE version SET files_updated=CURRENT_TIMESTAMP,files_updated_by='Cron' WHERE id=".$folder['version_id']);
										log_action("cron-create-link","File link inserted automatically for file «${filename}» (version id ".$folder['version_id']."), version files update date has also been changed");
										update_version_preview($folder['version_id']);
									}
								} else {
									$duration=$folder['default_duration'];
									query("INSERT INTO file (version_id,episode_id,variant_name,extra_name,length,comments,created,created_by,updated,updated_by) VALUES(".$folder['version_id'].",".$row['id'].",'Única',NULL,".$duration.",NULL,CURRENT_TIMESTAMP,'Cron',CURRENT_TIMESTAMP,'Cron')");
									query("INSERT INTO link (file_id,url,resolution,created,created_by,updated,updated_by) VALUES(".mysqli_insert_id($db_connection).",'".escape($real_link)."','$resolution',CURRENT_TIMESTAMP,'Cron',CURRENT_TIMESTAMP,'Cron')");
									query("UPDATE version SET is_hidden=0,files_updated=CURRENT_TIMESTAMP,files_updated_by='Cron' WHERE id=".$folder['version_id']);
									log_action("cron-create-link","File link inserted automatically for file «${filename}» (version id ".$folder['version_id']."), version files update date has also been changed");
									update_version_preview($folder['version_id']);
								}
								//Now check if we need to upgrade in progress -> complete
								$results = query("SELECT * FROM series WHERE id=".escape($folder['series_id']));
								$resultl = query("SELECT DISTINCT f.episode_id FROM file f LEFT JOIN episode e ON f.episode_id=e.id WHERE f.version_id=".$folder['version_id']." AND f.episode_id IS NOT NULL AND e.number IS NOT NULL");

								if (($series = mysqli_fetch_assoc($results))) {
									if ($series['number_of_episodes']==mysqli_num_rows($resultl) && $version['status']==2) {
										log_action("cron-update-version","Version id ".$version['id']." has been marked as complete and automatic synchronization has been stopped because it already has one file per episode");
										query("UPDATE version SET status=1,updated=CURRENT_TIMESTAMP,updated_by='Cron',completed_date=CURRENT_TIMESTAMP WHERE id=".$version['id']);
										query("UPDATE remote_folder SET is_active=0 WHERE version_id=".$version['id']);
										update_version_preview($folder['version_id']);
									}
								} else {
									log_action("cron-match-failed","Could not associate the link for file «${filename}»: series does not exist");
								}
								array_push($processed_numbers, $number);
							} else {
								log_action("cron-match-failed","Could not associate the link for file «${filename}»: version does not exist");
							}
						} else {
							//Episode number does not exist
							$resultff = query("SELECT * FROM remote_folder_failed_files WHERE remote_folder_id=".$folder['id']." AND file_name='".escape($filename)."'");
							if (mysqli_num_rows($resultff)==0) {
								query("INSERT INTO remote_folder_failed_files (remote_folder_id, file_name) VALUES (".$folder['id'].",'".escape($filename)."')");
								log_action("cron-match-failed","Could not associate the link for file «${filename}»: no episode exists with this number");
							}
						}
					} else {
						//More than one link per episode - only first gets accepted
						$resultff = query("SELECT * FROM remote_folder_failed_files WHERE remote_folder_id=".$folder['id']." AND file_name='".escape($filename)."'");
						if (mysqli_num_rows($resultff)==0) {
							query("INSERT INTO remote_folder_failed_files (remote_folder_id, file_name) VALUES (".$folder['id'].",'".escape($filename)."')");
							log_action("cron-match-failed","Could not associate the link for file «${filename}»: more than one link exists with this episode number, only the first was imported");
						}
					}
				} else {
					//Link does not match regexp
					$resultff = query("SELECT * FROM remote_folder_failed_files WHERE remote_folder_id=".$folder['id']." AND file_name='".escape($filename)."'");
					if (mysqli_num_rows($resultff)==0) {
						query("INSERT INTO remote_folder_failed_files (remote_folder_id, file_name) VALUES (".$folder['id'].",'".escape($filename)."')");
						log_action("cron-match-failed","Could not associate the link for file «${filename}»: file does not match the regular expression");
					}
				}
			}
		}
	}
	flock($lock_pointer, LOCK_UN);
} else {
	log_action("cron-error","Could not lock MEGA lock file");
}

log_action('cron-links-updater-finished', "Automatic link retrieval has finished");
?>
