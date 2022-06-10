<?php
require_once("db.inc.php");

function get_custom_server_files($server_base_url, $remote_folder) {
	try {
		$opts = array(
			'http' => array(
				'method' => "GET",
				'header' => "Referer: $base_url/\r\n"
			)
		);
		$context = stream_context_create($opts);
		$results = file_get_contents(generate_storage_url($server_base_url.str_replace("&", "%26", str_replace(" ", "%20", $remote_folder)).'/'), FALSE, $context);
	} catch (Exception $e) {
		return array('status' => 'ko', 'code' => 1);
	}

	$files = array();
	foreach (explode(PHP_EOL, $results) as $line) {
		preg_match('/.*href="(.*)".*/', $line, $matches, PREG_OFFSET_CAPTURE);
		$filename = '';
		if (count($matches)>1) {
			$filename = urldecode($matches[1][0]);
		}

		if (!empty($filename) && $filename!='../') {
			array_push($files, $filename.':::'.'storage://'.$remote_folder.'/'.$filename);
		}
	}
	return array('status' => 'ok', 'files' => $files);
}

session_set_cookie_params(3600 * 24 * 30); // 30 days
session_start();

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	$series_id = $_GET['series_id'];
	$remote_account_ids = $_GET['remote_account_ids'];
	$remote_folders = $_GET['remote_folders'];
	$division_ids = $_GET['division_ids'];

	$remote_account_folders = array();
	$count_mega = 0;
	for($i=0;$i<count($remote_account_ids);$i++){
		$remote_account_folder = array();

		if (is_numeric($remote_account_ids[$i]) && is_numeric($division_ids[$i])){
			$result = query("SELECT id,token,type FROM remote_account WHERE id=".escape($remote_account_ids[$i]));
			$row = mysqli_fetch_assoc($result);
			$remote_account_folder['remote_account_id']=$row['id'];
			$remote_account_folder['token']=$row['token'];
			$remote_account_folder['type']=$row['type'];
			if ($row['type']=='mega') {
				$count_mega++;
			}
			$remote_account_folder['remote_folder']=$remote_folders[$i];
			$remote_account_folder['division_id']=$division_ids[$i];
			array_push($remote_account_folders, $remote_account_folder);
		}
	}

	$import_type = $_GET['import_type'];
	$links = array();

	foreach ($remote_account_folders as $remote_account_folder) {
		if ($remote_account_folder['type']=='mega') {
			//Awfully ugly logic, will probably break, but for now it works.
			//Had to do this crap with a helper script because Mega-CMD cannot be run inside the Apache process.
			$lock_pointer = fopen($mega_lock_file, "w+");

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
						$results = "Error d'inici de sessió. El compte encara està actiu? (codi: 2)";
						break;
					case 'ERROR 3':
						$results = "Error d'accés a la carpeta. El nom de la carpeta és correcte? (codi: 3)";
						break;
					case 'ERROR 4':
						$results = "Error d'exportació d'enllaços. (codi: 4)";
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
						array_push($links,$remote_account_folder['division_id'].':::'.$line);
					}
				}
			}
		} else if ($remote_account_folder['type']=='storage') {
			$res = get_custom_server_files($remote_account_folder['token'], $remote_account_folder['remote_folder']);
			if ($res['status']=='ko') {
				echo json_encode(array(
					"status" => 'ko',
					"error" => "Error en accedir a l'emmagatzematge. (codi: ".$res['code'].")"
				));
				die();
			} else {
				foreach ($res['files'] as $file) {
					array_push($links,$remote_account_folder['division_id'].':::'.$file);
				}
			}
		}
	}

	$response = array();
	$unmatched_results = array();
	$processed_episode_ids = array();

	foreach ($links as $link){
		//log_action('get-link', "New link data: '".$link."'");
		if (count(explode(":::",$link, 3))>1) {
			$division_id = explode(":::",$link, 3)[0];
			$filename = explode(":::",$link, 3)[1];
			$real_link = explode(":::",$link, 3)[2];
			$matches = array();
			if (preg_match('/.* - (\d+).*\.(?:mp4|mkv|avi)/', $filename, $matches)) {
				$number = $matches[1];
				$result = query("SELECT e.id FROM episode e WHERE series_id=".escape($series_id)." AND number=$number".($division_id!=-1 ? " AND division_id=$division_id" : ''));
				if ($row = mysqli_fetch_assoc($result)) {
					$splitted = explode('/', $real_link);
					$start = $splitted[0].'//'.$splitted[2].'/';
					if (!in_array($row['id'].'-'.$start, $processed_episode_ids)) {
						$element = array();
						$element['id'] = $row['id'];
						$element['link'] = $real_link;
						array_push($response, $element);
						array_push($processed_episode_ids, $row['id'].'-'.$start);
					} else {
						//More than one link per episode - only first gets accepted
						$element = array();
						$element['file'] = $filename;
						$element['link'] = $real_link;
						$element['reason'] = "Múltiples enllaços";
						$element['reason_description'] = "Hi ha més d'un enllaç del mateix tipus per a aquest capítol, s'ha importat només el primer.";
						array_push($unmatched_results, $element);
					}
				} else {
					//Episode number does not exist
					$element = array();
					$element['file'] = $filename;
					$element['link'] = $real_link;
					$element['reason'] = "Capítol inexistent";
					$element['reason_description'] = "No s'ha trobat cap capítol amb aquest número.";
					array_push($unmatched_results, $element);
				}
			}
			else{
				//Link does not match regexp
				$element = array();
				$element['file'] = $filename;
				$element['link'] = $real_link;
				$element['reason'] = "Format erroni";
				$element['reason_description'] = "No coincideix amb el format correcte de nom de fitxer. Potser és un capítol especial?";
				array_push($unmatched_results, $element);
			}
		} else {
			log_action('get-link-failed', "No s'ha pogut obtenir l'enllaç, text de sortida sense el format correcte: '".$link."'");
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
