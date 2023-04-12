<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

function get_display_method($links){
	//Since we will not have mixed methods, we can just check the first one
	if (preg_match(REGEXP_MEGA,$links[0]['url'])){
		return "mega";
	}
	if (preg_match(REGEXP_GOOGLE_DRIVE,$links[0]['url'])){
		return "google-drive";
	}
	if (preg_match(REGEXP_YOUTUBE,$links[0]['url'])){
		return "youtube";
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
		} else if (preg_match(REGEXP_GOOGLE_DRIVE,$link['url'],$matches)){
			$elements[]=array(
				'url' => "https://www.googleapis.com/drive/v3/files/".$matches[1]."?key=".GOOGLE_DRIVE_API_KEY."&alt=media",
				'resolution' => get_resolution_single($link['resolution'])
			);
		} else if (preg_match(REGEXP_YOUTUBE,$link['url'],$matches)){
			$elements[]=array(
				'url' => "https://www.youtube.com/embed/".$matches[1]."?origin=".BASE_URL."&iv_load_policy=3&modestbranding=1&playsinline=1showinfo=0&rel=0&enablejsapi=1",
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
	//Check if we have all the data
	if (empty($_POST['file_id']) || !is_numeric($_POST['file_id'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$file_id = $_POST['file_id'];

	//Check if user exists
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

		$data = array(
			'file_id' => $file_id,
			'view_id' => 'TESTING-'.$file_id,
			'fansub' => $row['fansub_name'],
			'series' => $row['series_name'],
			'cover' => STATIC_URL.'/images/covers/'.$row['series_id'].'.jpg',
			'title' => get_episode_player_title($row['fansub_name'], $row['series_name'], $row['series_subtype'], $episode_title, $row['is_extra']),
			'title_short' => get_episode_player_title_short($fansub_names, $row['series_name'], $row['series_subtype'], $episode_title, $row['is_extra']).' | '.$row['fansub_name'].' | Fansubs.cat',
			'thumbnail' => (file_exists(STATIC_DIRECTORY.'/images/files/'.$row['version_id'].'.jpg') ? STATIC_URL.'/images/files/'.$row['version_id'].'.jpg' : STATIC_URL.'/images/covers/'.$row['series_id'].'.jpg'),
			'length' => $row['length'],
			'data_sources' => get_data_sources($links),
			'method' => get_display_method($links)
		);
	}

	return array('result' => 'ok', 'data' => $data);
}

echo json_encode(get_player_data());
?>
