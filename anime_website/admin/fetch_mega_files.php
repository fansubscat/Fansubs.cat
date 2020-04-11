<?php
require_once("../db.inc.php");

session_set_cookie_params(3600 * 24 * 30); // 30 days
session_start();

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	$series_id = $_GET['series_id'];
	$account_ids = $_GET['account_ids'];
	$folders = $_GET['folders'];

	$account_folders = array();
	for($i=0;$i<count($account_ids);$i++){
		$account_folder = array();

		if (is_numeric($account_ids[$i])){
			$result = query("SELECT session_id FROM account WHERE id=".escape($account_ids[$i]));
			$row = mysqli_fetch_assoc($result);
			$account_folder['session_id']=$row['session_id'];
			$account_folder['folder']=$folders[$i];
			array_push($account_folders, $account_folder);
		}
	}

	foreach ($account_folders as $account_folder) {
		//Awfully ugly logic, will probably break, but for now it works.
		//Had to do this crap with a helper script because Mega-CMD cannot be run inside the Apache process.
		while (file_exists('/tmp/mega.lock')){
			sleep(1);
		}
		file_put_contents('/tmp/mega.lock','1');
		file_put_contents('/tmp/mega.request',$account_folder['session_id'].":".$account_folder['folder']);
		while (file_exists('/tmp/mega.request')){
			sleep(1);
		}
		$results = file_get_contents('/tmp/mega.response');
		unlink('/tmp/mega.response');
		unlink('/tmp/mega.lock');

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
		}
	}

	$links = explode("\n",$results);

	$response = array();

	foreach ($links as $link){
		$filename = explode(":",$link, 2)[0];
		$real_link = explode(":",$link, 2)[1];
		$matches = array();
		if (preg_match('/.* - (\d+).*\.mp4/', $filename, $matches)) {
			$number = $matches[1];
			$result = query("SELECT e.id FROM episode e WHERE series_id=".escape($series_id)." AND number=".$number);
			if ($row = mysqli_fetch_assoc($result)) {
				$element = array();
				$element['id'] = $row['id'];
				$element['link'] = $real_link;
				array_push($response, $element);
			}
		}
	}

	echo json_encode(array(
		"status" => 'ok',
		"results" => $response
	));
}

mysqli_close($db_connection);
?>
