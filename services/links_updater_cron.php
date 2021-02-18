<?php
require_once('db.inc.php');
require_once("googledrive.inc.php");

log_action('cron-updater-started', "S'ha iniciat l'obtenció automàtica d'enllaços");

$resulta = query("SELECT f.*, a.id account_id, a.name, a.type, a.session_id, v.series_id FROM folder f LEFT JOIN account a ON f.account_id=a.id LEFT JOIN version v ON f.version_id=v.id WHERE active=1");

$lock_pointer = fopen($mega_lock_file, "w+");

//We acquire a file lock to prevent two invocations at the same time.
//This could happen if a user is asking for manual file sync while this cron runs.
if (flock($lock_pointer, LOCK_EX)) {
	while ($folder = mysqli_fetch_assoc($resulta)) {
		echo "Updating account ".$folder['name']. " / ".$folder['folder']."\n";

		$output = array();
		$result = 0;
		if ($folder['type']=='mega') {
			exec("./mega_list_links.sh ".$folder['session_id']." \"".$folder['folder']."\"", $output, $result);
		} else if ($folder['type']=='googledrive') {
			$res = get_google_drive_files($folder['account_id'], $folder['session_id'], $folder['folder']);
			if ($res['status']=='ko') {
				$result = $res['code'];
			} else {
				$output = $res['files'];
			}
		}

		if ($result!=0){
			log_action("cron-error","S'ha produït l'error $result en processar la carpeta '".$folder['folder']."' del compte '".$folder['name']."' (id. de versió: ".$folder['version_id'].")");
		} else {
			$processed_numbers = array();
			foreach ($output as $line) {
				$filename = explode(":::",$line, 2)[0];
				$real_link = explode(":::",$line, 2)[1];
				$matches = array();
				if (preg_match('/.* - (\d+).*\.(?:mp4|mkv|avi)/', $filename, $matches)) {
					$number = $matches[1];
					if (!in_array($number, $processed_numbers)) {
						$resulte = query("SELECT e.id FROM episode e WHERE series_id=".escape($folder['series_id'])." AND number=".$number.(!empty($folder['season_id']) ? " AND season_id=".$folder['season_id'] : ''));
						if ($row = mysqli_fetch_assoc($resulte)) {
							$resultv = query("SELECT * FROM version WHERE id=".$folder['version_id']);
							if ($version = mysqli_fetch_assoc($resultv)){
								$resolution = (!empty($version['default_resolution']) ? "'".$version['default_resolution']."'" : "NULL");
								$links = query("SELECT * FROM link WHERE episode_id=".$row['id']." AND version_id=".$folder['version_id']);
								//WARNING: We must prevent the version from having multiple links if autofetch is enabled, or bad things will happen!!!
								if ($link = mysqli_fetch_assoc($links)) {
									//Link exists, let's check the instances and replace the first one...
									if ($folder['type']=='mega') {
										$pattern="https://mega.nz/%";
									} else if ($folder['type']=='googledrive') {
										$pattern="https://drive.google.com/%";
									}
									$instances = query("SELECT * FROM link_instance WHERE link_id=".$link['id']." AND url LIKE '$pattern'");
									if ($instance = mysqli_fetch_assoc($instances)) {
										if ($instance['url']!=$real_link){
											query("UPDATE link_instance SET url='".escape($real_link)."' WHERE id=".$instance['id']);
											log_action("cron-update-link","S'ha actualitzat automàticament l'enllaç del fitxer '$filename' (id. d'instància d'enllaç: ".$instance['id'].", id. d'enllaç: ".$link['id'].", id. de versió: ".$folder['version_id'].")");
											//We do not update the links_updated field because we don't want the series to show in "last updated"
										}
									} else {
										query("INSERT INTO link (version_id,episode_id,variant_name,extra_name,comments,created) VALUES(".$folder['version_id'].",".$row['id'].",'Única',NULL,NULL,CURRENT_TIMESTAMP)");
										query("INSERT INTO link_instance (link_id,url,resolution,created) VALUES(".mysqli_insert_id($db_connection).",'".escape($real_link)."',$resolution,CURRENT_TIMESTAMP)");
										query("UPDATE version SET links_updated=CURRENT_TIMESTAMP,links_updated_by='Cron' WHERE id=".$folder['version_id']);
										log_action("cron-create-link","S'ha inserit automàticament l'enllaç del fitxer '$filename' (id. de versió: ".$folder['version_id'].") i s'ha actualitzat la data de modificació de la versió");
									}
								} else {
									query("INSERT INTO link (version_id,episode_id,variant_name,extra_name,comments,created) VALUES(".$folder['version_id'].",".$row['id'].",'Única',NULL,NULL,CURRENT_TIMESTAMP)");
									query("INSERT INTO link_instance (link_id,url,resolution,created) VALUES(".mysqli_insert_id($db_connection).",'".escape($real_link)."',$resolution,CURRENT_TIMESTAMP)");
									query("UPDATE version SET links_updated=CURRENT_TIMESTAMP,links_updated_by='Cron' WHERE id=".$folder['version_id']);
									log_action("cron-create-link","S'ha inserit automàticament l'enllaç del fitxer '$filename' (id. de versió: ".$folder['version_id'].") i s'ha actualitzat la data de modificació de la versió");
								}
								//Now check if we need to upgrade in progress -> complete
								$results = query("SELECT * FROM series WHERE id=".escape($folder['series_id']));
								$resultl = query("SELECT DISTINCT l.episode_id FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.version_id=".$folder['version_id']." AND l.episode_id IS NOT NULL AND e.number IS NOT NULL");

								if (($series = mysqli_fetch_assoc($results))) {
									if ($series['episodes']==mysqli_num_rows($resultl) && $version['status']==2) {
										log_action("cron-update-version","La versió (id. de versió: ".$version['id'].") s'ha marcat com a completada i se n'ha aturat la sincronització automàtica perquè ja té un enllaç per cada capítol");
										query("UPDATE version SET status=1,updated=CURRENT_TIMESTAMP,updated_by='Cron' WHERE id=".$version['id']);
										query("UPDATE folder SET active=0 WHERE version_id=".$version['id']);
									}
								} else {
									log_action("cron-match-failed","No s'ha pogut associar l'enllaç del fitxer '$filename': l'anime no existeix");
								}
								mysqli_free_result($results);
								mysqli_free_result($resultl);
								mysqli_free_result($links);
								array_push($processed_numbers, $number);
							} else {
									log_action("cron-match-failed","No s'ha pogut associar l'enllaç del fitxer '$filename': la versió no existeix");
							}
							mysqli_free_result($resultv);
						} else {
							//Episode number does not exist
							$resultff = query("SELECT * FROM folder_failed_files WHERE folder_id=".$folder['id']." AND file_name='".escape($filename)."'");
							if (mysqli_num_rows($resultff)==0) {
								query("INSERT INTO folder_failed_files (folder_id, file_name) VALUES (".$folder['id'].",'".escape($filename)."')");
								log_action("cron-match-failed","No s'ha pogut associar l'enllaç del fitxer '$filename': no hi ha cap capítol amb aquest número");
							}
							mysqli_free_result($resultff);
						}
						mysqli_free_result($resulte);
					} else {
						//More than one link per episode - only first gets accepted
						$resultff = query("SELECT * FROM folder_failed_files WHERE folder_id=".$folder['id']." AND file_name='".escape($filename)."'");
						if (mysqli_num_rows($resultff)==0) {
							query("INSERT INTO folder_failed_files (folder_id, file_name) VALUES (".$folder['id'].",'".escape($filename)."')");
							log_action("cron-match-failed","No s'ha pogut associar l'enllaç del fitxer '$filename': hi ha més d'un enllaç amb aquest número de capítol, s'importa només el primer");
						}
						mysqli_free_result($resultff);
					}
				} else {
					//Link does not match regexp
					$resultff = query("SELECT * FROM folder_failed_files WHERE folder_id=".$folder['id']." AND file_name='".escape($filename)."'");
					if (mysqli_num_rows($resultff)==0) {
						query("INSERT INTO folder_failed_files (folder_id, file_name) VALUES (".$folder['id'].",'".escape($filename)."')");
						log_action("cron-match-failed","No s'ha pogut associar l'enllaç del fitxer '$filename': no coincideix amb l'expressió regular");
					}
					mysqli_free_result($resultff);
				}
			}
		}
	}
	flock($lock_pointer, LOCK_UN);
} else {
	log_action("cron-error","No s'ha pogut blocar el fitxer de blocatge de MEGA");
}

log_action('cron-updater-finished', "S'ha completat l'obtenció automàtica d'enllaços");

mysqli_free_result($resulta);
?>