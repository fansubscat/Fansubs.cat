<?php
require_once("db.inc.php");

session_name(ADMIN_COOKIE_NAME);
session_set_cookie_params(ADMIN_COOKIE_DURATION, '/', ADMIN_COOKIE_DOMAIN, TRUE, FALSE);
session_start();

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	$series_id = $_GET['series_id'];
	$remote_account_ids = $_GET['remote_account_ids'];
	$remote_folders = $_GET['remote_folders'];
	$default_resolutions = $_GET['default_resolutions'];
	$default_durations = $_GET['default_durations'];
	$division_ids = $_GET['division_ids'];

	$remote_account_folders = array();
	for($i=0;$i<count($remote_account_ids);$i++){
		$remote_account_folder = array();

		if (is_numeric($remote_account_ids[$i]) && is_numeric($division_ids[$i])){
			$result = query("SELECT id,token FROM remote_account WHERE id=".escape($remote_account_ids[$i]));
			$row = mysqli_fetch_assoc($result);
			$remote_account_folder['remote_account_id']=$row['id'];
			$remote_account_folder['token']=$row['token'];
			$remote_account_folder['remote_folder']=$remote_folders[$i];
			$remote_account_folder['default_resolution']=$default_resolutions[$i];
			$remote_account_folder['default_duration']=$default_durations[$i];
			$remote_account_folder['division_id']=$division_ids[$i];
			array_push($remote_account_folders, $remote_account_folder);
		}
	}

	$import_type = $_GET['import_type'];
	$links = array();

	foreach ($remote_account_folders as $remote_account_folder) {
		//Awfully ugly logic, will probably break, but for now it works.
		//Had to do this crap with a helper script because Mega-CMD cannot be run inside the Apache process.
		$lock_pointer = fopen(MEGA_LOCK_FILE, "w+");

		//We acquire a file lock to prevent two invocations at the same time.
		//This could happen if a cron or another request is running while this one is done.
		if (flock($lock_pointer, LOCK_EX)) {
			file_put_contents('/tmp/mega.request',$remote_account_folder['token'].":::".$remote_account_folder['remote_folder']);
			while (file_exists('/tmp/mega.request')){
				sleep(1);
			}
			$results = file_get_contents('/tmp/mega.response');
			unlink('/tmp/mega.response');
			flock($lock_pointer, LOCK_UN);
		} else {
			echo json_encode(array(
				"status" => 'ko',
				"error" => "Error en la creació del fitxer de blocatge. Torna-ho a provar més tard. (codi: L)"
			));
			die();
		}

		if (substr($results,0,5)=='ERROR'){
			switch(substr($results,0,7)){
				case 'ERROR 1':
					$results = "Error de sessió ja iniciada. Torna-ho a provar. (codi: 1)";
					break;
				case 'ERROR 2':
					$results = "Error d’inici de sessió. El compte encara està actiu? (codi: 2)";
					break;
				case 'ERROR 3':
					$results = "Error d’accés a la carpeta. El nom de la carpeta és correcte? (codi: 3)";
					break;
				case 'ERROR 4':
					$results = "Error d’exportació d’enllaços. (codi: 4)";
					break;
				case 'ERROR 5':
					$results = "Error en tancar la sessió. (codi: 5)";
					break;
			}
			echo json_encode(array(
				"status" => 'ko',
				"error" => $results
			));
			die();
		} else {
			$lines = explode("\n",$results);
			foreach ($lines as $line) {
				if ($line!='') {
					array_push($links,$remote_account_folder['division_id'].':::'.$remote_account_folder['default_resolution'].':::'.$remote_account_folder['default_duration'].':::'.$line);
				}
			}
		}
	}

	$response = array();
	$unmatched_results = array();
	$processed_episode_ids = array();

	foreach ($links as $link){
		//log_action('get-link', "New link data: '".$link."'");
		if (count(explode(":::",$link, 5))>1) {
			$division_id = explode(":::",$link, 5)[0];
			$resolution = explode(":::",$link, 5)[1];
			$duration = explode(":::",$link, 5)[2];
			$filename = explode(":::",$link, 5)[3];
			$real_link = explode(":::",$link, 5)[4];
			$matches = array();
			if (preg_match('/.* - (\d+).*\.(?:mp4|mkv|avi)/', $filename, $matches)) {
				$number = $matches[1];
				$result = query("SELECT e.id FROM episode e WHERE series_id=".escape($series_id)." AND linked_episode_id IS NULL AND number=$number".($division_id!=-1 ? " AND division_id=$division_id" : ''));
				if ($row = mysqli_fetch_assoc($result)) {
					$splitted = explode('/', $real_link);
					$start = $splitted[0].'//'.$splitted[2].'/';
					if (!in_array($row['id'].'-'.$start, $processed_episode_ids)) {
						$element = array();
						$element['id'] = $row['id'];
						$element['link'] = $real_link;
						$element['resolution'] = $resolution;
						$element['duration'] = $duration;
						array_push($response, $element);
						array_push($processed_episode_ids, $row['id'].'-'.$start);
					} else {
						//More than one link per episode - only first gets accepted
						$element = array();
						$element['file'] = $filename;
						$element['link'] = $real_link;
						$element['resolution'] = $resolution;
						$element['duration'] = $duration;
						$element['reason'] = "Múltiples enllaços";
						$element['reason_description'] = "Hi ha més d’un enllaç del mateix tipus per a aquest capítol, s’ha importat només el primer.";
						array_push($unmatched_results, $element);
					}
				} else {
					//Episode number does not exist
					$element = array();
					$element['file'] = $filename;
					$element['link'] = $real_link;
					$element['resolution'] = $resolution;
					$element['duration'] = $duration;
					$element['reason'] = "Capítol inexistent";
					$element['reason_description'] = "No s’ha trobat cap capítol amb aquest número.";
					array_push($unmatched_results, $element);
				}
			}
			else{
				//Link does not match regexp
				$element = array();
				$element['file'] = $filename;
				$element['link'] = $real_link;
				$element['resolution'] = $resolution;
				$element['duration'] = $duration;
				$element['reason'] = "Format erroni";
				$element['reason_description'] = "No coincideix amb el format correcte de nom de fitxer. Potser és un capítol especial?";
				array_push($unmatched_results, $element);
			}
		} else {
			log_action('get-link-failed', "No s’ha pogut obtenir l’enllaç, text de sortida sense el format correcte: «".$link."»");
		}
	}

	echo json_encode(array(
		"status" => 'ok',
		"results" => $response,
		"unmatched_results" => $unmatched_results
	));
}

mysqli_close($db_connection);
?>
