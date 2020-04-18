<?php
require_once('db.inc.php');

$resulta = query("SELECT *, a.name, a.session_id,v.series_id FROM folder f LEFT JOIN account a ON f.account_id=a.id LEFT JOIN version v ON f.version_id=v.id WHERE active=1");

//For compatibility with manual fetching
file_put_contents('/tmp/mega.lock','1');

while ($folder = mysqli_fetch_assoc($resulta)) {
	echo "Updating account ".$folder['name']. " / ".$folder['folder']."\n";

	$output = array();
	$result = 0;
	exec("./mega_list_links.sh ".$folder['session_id']." \"".$folder['folder']."\"", $output, $result);

	if ($result!=0){
		log_action("cron-error","S'ha produït l'error $result en processar la carpeta '".$folder['folder']."' del compte '".$folder['name']."' (id. de versió: ".$folder['version_id'].")");
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
							query("UPDATE link SET url='".escape($real_link)."' WHERE episode_id=".$row['id']." AND version_id=".$folder['version_id']);
							log_action("cron-update-link","S'ha actualitzat automàticament l'enllaç del fitxer '$filename' (id. d'enllaç: ".$link['id'].", id. de versió: ".$folder['version_id'].")");
						}
					} else {
						$resultv = query("SELECT * FROM version WHERE id=".$folder['version_id']);
						if ($version = mysqli_fetch_assoc($resultv)){
							$resolution = (!empty($version['default_resolution']) ? "'".$version['default_resolution']."'" : "NULL");

							query("INSERT INTO link (version_id,episode_id,extra_name,url,resolution,comments) VALUES(".$folder['version_id'].",".$row['id'].",NULL,'".escape($real_link)."',$resolution,NULL)");
							query("UPDATE version SET updated=CURRENT_TIMESTAMP,updated_by='Cron' WHERE id=".$folder['version_id']);
							log_action("cron-create-link","S'ha inserit automàticament l'enllaç del fitxer '$filename' (id. de versió: ".$folder['version_id'].") i s'ha actualitzat la data de modificació de la versió");

							//Now check if we need to upgrade in progess -> complete
							$results = query("SELECT * FROM series WHERE id=".escape($folder['series_id']));
							$resultl = query("SELECT DISTINCT l.episode_id FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.version_id=".$folder['version_id']." AND l.episode_id IS NOT NULL AND e.number IS NOT NULL");

							if (($series = mysqli_fetch_assoc($results))) {
								if ($series['episodes']==mysqli_num_rows($resultl) && $version['status']==2) {
									log_action("cron-update-version","La versió (id. de versió: ".$version['id'].") s'ha marcat com a completada i se n'ha aturat la sincronització automàtica perquè ja té un enllaç per cada capítol");
									query("UPDATE version SET status=1,updated=CURRENT_TIMESTAMP,updated_by='Cron' WHERE id=".$version['id']);
									query("UPDATE folder SET active=0 WHERE version_id=".$version['id']);
								}
							} else {
								log_action("cron-match-failed","No s'ha pogut associar l'enllaç del fitxer '$filename': la sèrie no existeix");
							}
							mysqli_free_result($results);
							mysqli_free_result($resultl);
						} else {
								log_action("cron-match-failed","No s'ha pogut associar l'enllaç del fitxer '$filename': la versió no existeix");
						}
						mysqli_free_result($resultv);
					}
					mysqli_free_result($links);
				} else {
					$resultff = query("SELECT * FROM folder_failed_files WHERE folder_id=".$folder['id']." AND file_name='".escape($filename)."'");
					if (mysqli_num_rows($resultff)==0) {
						query("INSERT INTO folder_failed_files (folder_id, file_name) VALUES (".$folder['id'].",'".escape($filename)."')");
						log_action("cron-match-failed","No s'ha pogut associar l'enllaç del fitxer '$filename': no hi ha cap capítol amb aquest número");
					}
					mysqli_free_result($resultff);
				}
				mysqli_free_result($resulte);
			} else {
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

//For compatibility with manual fetching
unlink('/tmp/mega.lock');

mysqli_free_result($resulta);
?>
