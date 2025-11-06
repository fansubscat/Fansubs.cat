<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

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

		$episode_title = get_episode_title($row['series_subtype'], $row['show_episode_numbers'],$row['episode_number'],$row['linked_episode_id'],$row['title'],$row['version_title'], $row['extra_name'], $row['is_extra']);

		$links = array();
		if (CATALOGUE_ITEM_TYPE!='manga') {
			$resulti = query_links_by_file_id($file_id);
			while ($lirow = mysqli_fetch_assoc($resulti)){
				array_push($links, $lirow);
			}
			mysqli_free_result($resulti);
			$links = filter_links($links);
		}

		$pages = array();
		$music = array();
		if (CATALOGUE_ITEM_TYPE=='manga') {
			if (!DISABLE_REMOTE_STORAGE_FOR_MANGA && count(REMOTE_STORAGES)>0) {
				$base_path=get_storage_url("storage://Manga/$file_id/", TRUE);
				$files = list_remote_files($base_path);
			} else {
				$base_path="../static/storage/$file_id/";
				$files = list_local_files($base_path, $file_id);
			}
			$pages = filter_files($files, 'images');
			$music = filter_files($files, 'audio');
			natsort($pages);
			natsort($music);

			if (count($pages)<1) {
				return array('result' => 'ko', 'code' => 3);
			}
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
				query_update_view_session_basic_attrs($view_id, $row['length'], get_view_source_type($_SERVER['HTTP_USER_AGENT'], FALSE), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
			} else {
				$view_id = get_nanoid();
				$initial_progress = 0;
				query_insert_view_session($view_id, $file_id, $row['series_type'], $user['id'], NULL, $row['length'], get_view_source_type($_SERVER['HTTP_USER_AGENT'], FALSE), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
				query_save_click($file_id, $row['series_type'], date('Y-m-d'));
			}
		} else {
			//Anon
			$resvs = query_view_session_for_anon_id_and_file_id(session_id(), $file_id);
			if ($rowvs = mysqli_fetch_assoc($resvs)) {
				$view_id = $rowvs['id'];
				$initial_progress = $rowvs['progress'];
				query_update_view_session_basic_attrs($view_id, $row['length'], get_view_source_type($_SERVER['HTTP_USER_AGENT'], FALSE), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
			} else {
				$view_id = get_nanoid();
				$initial_progress = 0;
				query_insert_view_session($view_id, $file_id, $row['series_type'], NULL, session_id(), $row['length'], get_view_source_type($_SERVER['HTTP_USER_AGENT'], FALSE), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
				query_save_click($file_id, $row['series_type'], date('Y-m-d'));
			}
		}

		$user_reader_preference = 0;
		if (!empty($user)) {
			$user_reader_preference = $user['manga_reader_type'];
		} else if (!empty($_COOKIE['manga_reader_type'])) {
			$user_reader_preference = $_COOKIE['manga_reader_type'];
		}
		$real_reader_used = $row['reader_type'];
		if ($user_reader_preference==3) {
			$real_reader_used = 'strip';
		} else if ($row['reader_type']!='strip' && $user_reader_preference==1) {
			$real_reader_used = 'rtl';
		} else if ($row['reader_type']!='strip' && $user_reader_preference==2) {
			$real_reader_used = 'ltr';
		}
		
		$division_title = (!empty($row['division_name']) && $row['series_type']!='manga') ? $row['division_name'] : $row['version_title'];

		$data = array(
			'file_id' => intval($file_id),
			'version_id' => intval($row['version_id']),
			'view_id' => $view_id,
			'fansub' => $row['fansub_name'],
			'series' => $row['version_title'],
			'cover' => STATIC_URL.'/images/covers/version_'.$row['version_id'].'.jpg',
			'title' => get_episode_player_title($row['fansub_name'], $division_title, $row['series_subtype'], $episode_title, $row['is_extra']),
			'title_short' => get_episode_player_title_short($division_title, $row['series_subtype'], $episode_title, $row['is_extra']).' | '.$row['fansub_name'].' | '.CURRENT_SITE_NAME,
			'thumbnail' => (file_exists(STATIC_DIRECTORY.'/images/files/'.$file_id.'.jpg') ? STATIC_URL.'/images/files/'.$file_id.'.jpg' : STATIC_URL.'/images/covers/version_'.$row['version_id'].'.jpg'),
			'length' => intval($row['length']),
			'data_sources' => get_data_sources($links),
			'pages' => array_values($pages),
			'music' => !empty($music) ? $music[0] : NULL,
			'reader_type' => $real_reader_used,
			'user_reader_preference' => $user_reader_preference,
			'default_reader_type' => $row['reader_type'],
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
