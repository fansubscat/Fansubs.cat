<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

function get_display_method($links){
	if (count($links)==0) {
		return "pages";
	}
	//Since we will not have mixed methods, we can just check the first one
	if (preg_match(REGEXP_MEGA,$links[0]['url'])){
		return "mega";
	}
	if (preg_match(REGEXP_STORAGE,$links[0]['url'])){
		return "storage";
	}
	return "direct-video";
}

function get_resolution_single($resolution){
	if (count(explode('x',$resolution))>1) {
		return explode('x',$resolution)[1];
	} else {
		return preg_replace("/[^0-9]/", '', $resolution);
	}
}

function get_data_sources($links){
	$elements = array();
	foreach ($links as $link) {
		$matches = array();
		if (preg_match(REGEXP_MEGA,$link['url'],$matches)){
			$elements[]=array(
				'url' => $link['url'],
				'resolution' => get_resolution_single($link['resolution'])
			);
		} else if (preg_match(REGEXP_STORAGE,$link['url'],$matches)){
			$elements[]=array(
				'url' => get_storage_url($link['url']),
				'resolution' => get_resolution_single($link['resolution'])
			);
		} else {
			$elements[]=array(
				'url' => $link['url'],
				'resolution' => get_resolution_single($link['resolution'])
			);
		}
	}
	return $elements;
}

function get_player_data(){
	global $user;
	//Check if we have all the data
	if (empty($_POST['file_id']) || !is_numeric($_POST['file_id'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$file_id = $_POST['file_id'];

	//Check if file exists
	$result = query_player_details_by_file_id($file_id);
	if (mysqli_num_rows($result)==0){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	} else {
		$row = mysqli_fetch_assoc($result);

		$episode_title = get_episode_title($row['series_subtype'], $row['show_episode_numbers'],$row['episode_number'],$row['linked_episode_id'],$row['title'],$row['series_name'], $row['extra_name'], $row['is_extra']);

		$links = array();
		if (CATALOGUE_ITEM_TYPE!='manga') {
			$resulti = query_links_by_file_id($file_id);
			while ($lirow = mysqli_fetch_assoc($resulti)){
				array_push($links, $lirow);
			}
			mysqli_free_result($resulti);
			$links = filter_links($links);
		}

		//Use session:
		//	id:		nanoid
		//	file_id:	file_id
		//	user_id:	user_id or NULL if anon
		//	anon_id:	session_id or NULL if user
		//	last_progress:	darrer instant vist (es recupera entre sessions), unitat=segon/pàgina
		//	total_progress:	total vist (se suma entre sessions), unitat: segon/pàgina
		//	length:		durada total del fitxer, unitat: segon/pàgina
		//	created, last_update, ip, user_agent: obvi
		//	casted:		1 en el moment de fer cast (es compta com a visualització instantàniament)
		//	closed:		1 si s'ha tancat el reproductor (es torna a posar a 0 en obrir)
		//	view_counted:	1 si s'ha computat com a visualització a les estadístiques generals
		//	archived:	1 if discarded after 3 days

		//Load progress if it exists
		if (!empty($user)) {
			$resfp = query_user_file_seen_status_by_file_id($user['id'], $file_id);
			if ($rowfp = mysqli_fetch_assoc($resfp)) {
				$current_position = $rowfp['position'];
				$is_seen = ($rowfp['is_seen']==1);
			} else {
				$current_position = 0;
				$is_seen = FALSE;
			}
		} else {
			$current_position = 0;
			$is_seen = FALSE;
		}

		if (!empty($user)) {
			//Logged user
			$resvs = query_view_session_for_user_and_file_id($user['id'], $file_id);
			if ($rowvs = mysqli_fetch_assoc($resvs)) {
				$view_id = $rowvs['id'];
				$initial_progress = $rowvs['progress'];
				query_update_view_session_basic_attrs($view_id, $row['length'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
			} else {
				$view_id = get_nanoid();
				$initial_progress = 0;
				query_insert_view_session($view_id, $file_id, $user['id'], NULL, $row['length'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
			}
		} else {
			//Anon
			$resvs = query_view_session_for_anon_id_and_file_id(session_id(), $file_id);
			if ($rowvs = mysqli_fetch_assoc($resvs)) {
				$view_id = $rowvs['id'];
				$initial_progress = $rowvs['progress'];
				query_update_view_session_basic_attrs($view_id, $row['length'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
			} else {
				$view_id = get_nanoid();
				$initial_progress = 0;
				query_insert_view_session($view_id, $file_id, NULL, session_id(), $row['length'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
			}
		}

		$data = array(
			'file_id' => intval($file_id),
			'view_id' => $view_id,
			'fansub' => $row['fansub_name'],
			'series' => $row['series_name'],
			'cover' => STATIC_URL.'/images/covers/'.$row['series_id'].'.jpg',
			'title' => get_episode_player_title($row['fansub_name'], $row['series_name'], $row['series_subtype'], $episode_title, $row['is_extra']),
			'title_short' => get_episode_player_title_short($row['series_name'], $row['series_subtype'], $episode_title, $row['is_extra']).' | '.$row['fansub_name'].' | Fansubs.cat',
			'thumbnail' => (file_exists(STATIC_DIRECTORY.'/images/files/'.$file_id.'.jpg') ? STATIC_URL.'/images/files/'.$file_id.'.jpg' : STATIC_URL.'/images/covers/'.$row['series_id'].'.jpg'),
			'length' => intval($row['length']),
			'data_sources' => get_data_sources($links),
			'method' => get_display_method($links),
			'initial_position' => intval($current_position),
			'initial_progress' => intval($initial_progress),
			'is_seen' => $is_seen
		);
	}

	return array('result' => 'ok', 'data' => $data);
}

echo json_encode(get_player_data());
?>
