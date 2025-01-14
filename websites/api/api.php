<?php
require_once(__DIR__.'/../common/initialization.inc.php');
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/queries.inc.php');
ob_start();
$request = explode('/', trim(explode('?', $_SERVER['REQUEST_URI'])[0], '/'));
$max_items = 20;

function get_random_string_from_seed($seed) {
	srand(crc32($seed));
	$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$charactersLength = strlen($characters);
	$randomString = '';
	for ($i = 0; $i < 16; $i++) {
		$randomString .= $characters[rand(0, $charactersLength - 1)];
	}
	return $randomString;
}

function get_api_session_id() {
	if (!empty($_GET['api_session_id'])) {
		return 'API-'.$_GET['api_session_id'];
	}
	$seed = (!empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown');
	$seed .= (!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown');
	return 'API-'.get_random_string_from_seed($seed);
}

function is_outdated_fansubs_app(){
	$ua = $_SERVER['HTTP_USER_AGENT'];
	return strpos($ua,'FansubsCatApp/Android/')===0 && explode(' [', explode('FansubsCatApp/Android/', $ua)[1])[0]<'1.0.2';
}

function show_invalid($reason) {
	http_response_code(400);
	$response = array(
		'status' => 'ko',
		'error' => array(
				'code' => 'INVALID_REQUEST',
				'description' => $reason
			)
	);
	echo json_encode($response);
}

function get_storage_url($url, $clean=FALSE) {
	if (count(STORAGES)>0 && strpos($url, "storage://")===0) {
		$rand = rand(0, count(STORAGES)-1);
		if ($clean) {
			return str_replace("storage://", STORAGES[$rand], $url);
		} else {
			return generate_storage_url(str_replace("storage://", STORAGES[$rand], $url));
		}
	} else {
		return $url;
	}
}

function list_remote_image_files($url) {
	$contents = @file_get_contents($url);
	preg_match_all("|href=[\"'](.*?)[\"']|", $contents, $hrefs);
	$hrefs = array_slice($hrefs[1], 1);
	
	$files = array();
	foreach ($hrefs as $href) {
		//We filter out audio files
		if (preg_match('/.*\.(jpe?g|png)$/i', $href)) {
			array_push($files, $url.$href);
		}
	}
	return $files;
}

function get_manga_chapter_title($series_subtype, $series_name, $show_episode_numbers, $episode_number, $title, $is_extra, $extra_name, $number_of_divisions, $division_name, $division_number) {
	if ($is_extra) {
		return lang('catalogue.generic.extra_prefix_short').$extra_name;
	}

	return (!empty($division_name) ? $division_name.' - ' : '').$title;
}

function convert_version_slug_to_series_slug($version_slug) {
	return explode('/', $version_slug)[0];
}

$method = array_shift($request);
if ($method == 'refresh') {
	$token = array_shift($request);
	if ($token!=NULL){
		$result = query_fansub_slug_from_ping_token($token);
		if ($row = mysqli_fetch_assoc($result)){
			system("cd ".SERVICES_DIRECTORY." && /usr/bin/php fetch.php {$row['slug']} > /dev/null &");
			$response = array(
				'status' => 'ok',
				'result' => 'A refresh operation has been scheduled for your fansub.'
			);
			echo json_encode($response);
		}
		else{
			http_response_code(401);
			$response = array(
				'status' => 'ko',
				'error' => array(
					'code' => 'UNAUTHORIZED',
					'description' => 'The provided refresh token is invalid.'
				)
			);
			echo json_encode($response);
		}
	}
	else{
		show_invalid('No refresh token has been provided.');
	}
}
else if ($method == 'fansubs'){
	$result = query_fansubs_for_api_response();
	$elements = array();
	while($row = mysqli_fetch_assoc($result)){
		$elements[] = array(
			'id' => $row['slug'],
			'name' => $row['name'],
			'url' => $row['url'],
			'logo_url' => ($row['slug']=='fansubs-cat' ? STATIC_URL.'/favicons/main/android-chrome-192x192.png' : STATIC_URL.'/images/icons/'.$row['id'].'.png'),
			'icon_url' => ($row['slug']=='fansubs-cat' ? STATIC_URL.'/favicons/main/android-chrome-192x192.png' : STATIC_URL.'/images/icons/'.$row['id'].'.png'),
			'is_historical' => ($row['is_historical']==1),
			'is_active' => ($row['is_active']==1),
			'is_visible' => ($row['slug']!='fansubs-cat'),
			'is_own' => ($row['slug']=='fansubs-cat'),
			'archive_url' => $row['archive_url']
		);
	}

	$response = array(
		'status' => is_outdated_fansubs_app() ? 'must_update' : 'ok',
		'result' => $elements
	);
	echo json_encode($response);
}
else if (substr($method, 0, 4) === "news"){
	$page = isset($_GET['page']) ? $_GET['page'] : NULL;
	$search = isset($_GET['search']) ? $_GET['search'] : '';
	$fansub_ids = isset($_GET['fansub_ids']) ? $_GET['fansub_ids'] : array();
	if ($page!=NULL && is_numeric($page) && $page>=0){
		$offset = (int)$page*25;
		$result = query_news_for_api_response($offset, $search, $fansub_ids);
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'date' => date_create_from_format('Y-m-d H:i:s', $row['date'])->getTimestamp(),
				'fansub_id' => $row['fansub_slug'],
				'fansub_name' => $row['fansub_name'],
				'title' => $row['title'],
				'contents' => $row['contents'],
				'url' => $row['url'],
				'image_url' => $row['image']!=NULL ? STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'] : NULL
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	}
	else{
		show_invalid("You can not fetch news if you don't provide a valid page number.");
	}
}
else if ($method === 'manga'){
	define('SITE_IS_HENTAI_OR_OLD_HENTAI', (!empty($_GET['hentai']) && $_GET['hentai']=='true') || SITE_IS_HENTAI);
	$submethod = array_shift($request);
	if ($submethod=='popular') {
		$page = array_shift($request);
		if ($page>0) {
			$offset = ($page-1)*$max_items;
			$result = query_popular_manga($offset, $max_items);
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => convert_version_slug_to_series_slug($row['default_version_slug']),
					'name' => $row['default_version_title'],
					'author' => $row['author'],
					'synopsis' => $row['default_version_synopsis'],
					'genres' => $row['genres'],
					'status' => $row['versions_in_progress']>=1 ? 'ongoing' : 'finished',
					'thumbnail_url' => STATIC_URL.'/images/covers/'.$row['default_version_id'].'.jpg'
				);
			}

			$response = array(
				'status' => 'ok',
				'result' => $elements
			);
			echo json_encode($response);
		} else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='recent') {
		$page = array_shift($request);
		if ($page>0) {
			$offset = ($page-1)*$max_items;
			$result = query_recent_manga($offset, $max_items);
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => convert_version_slug_to_series_slug($row['default_version_slug']),
					'name' => $row['default_version_title'],
					'author' => $row['author'],
					'synopsis' => $row['default_version_synopsis'],
					'genres' => $row['genres'],
					'status' => $row['versions_in_progress']>=1 ? 'ongoing' : 'finished',
					'thumbnail_url' => STATIC_URL.'/images/covers/'.$row['default_version_id'].'.jpg'
				);
			}

			$response = array(
				'status' => 'ok',
				'result' => $elements
			);
			echo json_encode($response);
		} else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='search') {
		$page = array_shift($request);
		if ($page>0) {
			$offset = ($page-1)*$max_items;
			$query = isset($_GET['query']) ? $_GET['query'] : '';
			$type = (isset($_GET['type']) && ($_GET['type']=='oneshot' || $_GET['type']=='serialized')) ? $_GET['type'] : 'all';
			$statuses = isset($_GET['status']) ? $_GET['status'] : array();
			$demographies = isset($_GET['demographies']) ? $_GET['demographies'] : array();
			$genres_include = isset($_GET['genres_include']) ? $_GET['genres_include'] : array();
			$genres_exclude = isset($_GET['genres_exclude']) ? $_GET['genres_exclude'] : array();
			$themes_include = isset($_GET['themes_include']) ? $_GET['themes_include'] : array();
			$themes_exclude = isset($_GET['themes_exclude']) ? $_GET['themes_exclude'] : array();
			$result = query_search_manga($offset, $max_items, $query, $type, $statuses, $demographies, $genres_include, $genres_exclude, $themes_include, $themes_exclude);
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => convert_version_slug_to_series_slug($row['default_version_slug']),
					'name' => $row['default_version_title'],
					'author' => $row['author'],
					'synopsis' => $row['default_version_synopsis'],
					'genres' => $row['genres'],
					'status' => $row['versions_in_progress']>=1 ? 'ongoing' : 'finished',
					'thumbnail_url' => STATIC_URL.'/images/covers/'.$row['default_version_id'].'.jpg'
				);
			}

			$response = array(
				'status' => 'ok',
				'result' => $elements
			);
			echo json_encode($response);
		} else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='details') {
		$slug = array_shift($request);
		$result = query_get_manga_details_by_slug($slug);
		if($row = mysqli_fetch_assoc($result)){
			if (is_numeric($row['id'])) {
				$element = array(
					'slug' => convert_version_slug_to_series_slug($row['default_version_slug']),
					'name' => $row['default_version_title'],
					'author' => $row['author'],
					'synopsis' => $row['default_version_synopsis'],
					'genres' => $row['genres'],
					'status' => $row['versions_in_progress']>=1 ? 'ongoing' : 'finished',
					'thumbnail_url' => STATIC_URL.'/images/covers/'.$row['default_version_id'].'.jpg'
				);

				$response = array(
					'status' => 'ok',
					'result' => $element
				);
				echo json_encode($response);
			} else {
				show_invalid('No valid manga specified.');
			}
		} else {
			show_invalid('No valid manga specified.');
		}
	} else if ($submethod=='chapters') {
		$slug = array_shift($request);
		$slug = escape($slug);
		$result = query_get_manga_chapters_by_slug($slug);
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'id' => convert_version_slug_to_series_slug($row['default_version_slug']).'/'.$row['id'],
				'title' => get_manga_chapter_title($row['subtype'], $row['default_version_title'], $row['show_episode_numbers'], $row['number'], $row['episode_title'], $row['is_extra'], $row['extra_name'], $row['number_of_divisions'], $row['division_name'], $row['division_number']),
				'number' => $row['number']==NULL ? 0 : floatval($row['number']),
				'fansub' => $row['fansubs'],
				'created' => strtotime($row['created'])*1000
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	} else if ($submethod=='pages') {
		//This is a temporary fix until the Tachiyomi extension is updated.
		//Version 3 of the extension sends "slug/file_id", version 4 sends "file_id"
		//Version 3 user-agent is "Tachiyomi/FansubsCat/xxx", version 4 is "Tachiyomi/xxx"
		if (strpos($_SERVER['HTTP_USER_AGENT'], 'FansubsCat')!==FALSE) {
			array_shift($request);
		}
		$file_id = intval(array_shift($request));

		//IMPORTANT: We add 10000 because when the unification of catalogues occurred,
		//we added 10000 to the manga file ids. However, this has problems with Tachiyomi.
		//We therefore use the original id from the request, and add 10000 to get the DB id if needed.
		if ($file_id<=10000) {
			$file_id+=10000;
		}

		$result = query_get_manga_chapter_pages($file_id);
		if ($row = mysqli_fetch_assoc($result)) {
			//Check if this view is already in the database: same user agent, same IP and same file in the last hour
			$exists_result = query_get_view_session_from_anon_id($file_id, get_api_session_id());
			if (mysqli_num_rows($exists_result)==0) {
				query_insert_view_session_completed(get_nanoid(), $file_id, 'manga', NULL, get_api_session_id(), $row['length'], 'api', $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
				query_save_view_completed($file_id, 'manga', date('Y-m-d'), $row['length']);
			}

			$base_path=get_storage_url("storage://Manga/$file_id/", TRUE);
			$files = list_remote_image_files($base_path);
			if (count($files)<1) {
				show_invalid('No valid file specified.');
			} else {
				natsort($files);
				$elements = array();
				foreach ($files as $file) {
					$elements[] = array(
						'url' => $file
					);
				}

				$response = array(
					'status' => 'ok',
					'result' => $elements
				);
				echo json_encode($response);
			}
		} else {
			//Not in database - no file with this id or file has been removed
			show_invalid('No valid file specified.');
		}
	} else {
		show_invalid('No valid submethod specified.');
	}
}
else if ($method === 'internal' && !empty($_GET['token']) && $_GET['token']===INTERNAL_SERVICES_TOKEN){
	$submethod = array_shift($request);
	if ($submethod=='get_manga_files') {
		$result = query_get_all_manga_files();
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'file_id' => $row['id'],
				'series' => $row['slug'],
				'original_filename' => $row['original_filename'],
				'length' => $row['length'],
				'is_extra' => $row['is_extra'] ? TRUE : FALSE
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	} else if ($submethod=='get_unconverted_links') {
		if (!empty($_POST['file_id']) && is_numeric($_POST['file_id'])) {
			$file_id=intval($file_id);
		}
		else{
			$file_id = NULL;
		}
		$result = query_get_unconverted_links($file_id);
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'file_id' => $row['file_id'],
				'type' => $row['type'],
				'url' => $row['url'],
				'resolution' => $row['resolution'],
				'is_extra' => $row['is_extra'] ? TRUE : FALSE,
				'storage_folder' => $row['storage_folder'],
				'storage_processing' => $row['storage_processing']
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	} else if ($submethod=='get_converted_links') {
		if (!empty($_POST['file_id']) && is_numeric($_POST['file_id'])) {
			$file_id=intval($file_id);
		}
		else{
			$file_id = NULL;
		}
		$result = query_get_converted_links($file_id);
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'file_id' => $row['file_id'],
				'type' => $row['type'],
				'url' => $row['url'],
				'resolution' => $row['resolution'],
				'is_extra' => $row['is_extra'] ? TRUE : FALSE,
				'duration_in_seconds' => !empty($row['length']) ? $row['length'] : 0
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	} else if ($submethod=='insert_converted_link') {
		if (!empty($_POST['file_id']) && is_numeric($_POST['file_id']) && !empty($_POST['url']) && !empty($_POST['original_url']) && !empty($_POST['resolution'])) {
			query_insert_link($_POST['file_id'], $_POST['url'], $_POST['original_url'], $_POST['resolution']);
			if (get_previous_query_num_affected_rows()>0) {
				log_action('api-insert-converted-link', "Inserted converted link «${_POST['url']}» for file id ${_POST['file_id']}");
			} else {
				log_action('api-discard-converted-link', "Discarded converted link «${_POST['url']}» for file id ${_POST['file_id']}, probably updated while converting");
			}
			
			$response = array(
				'status' => 'ok'
			);
			echo json_encode($response);
		}
		else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='change_file_thumbnail') {
		if (!empty($_POST['file_id']) && is_numeric($_POST['file_id']) && !empty($_FILES['thumbnail']) && is_uploaded_file($_FILES['thumbnail']['tmp_name'])) {
			$file_id=intval($_POST['file_id']);
			move_uploaded_file($_FILES['thumbnail']["tmp_name"], STATIC_DIRECTORY.'/images/files/'.$file_id.'.jpg');
			log_action('api-change-file-thumbnail', "Changed thumbnail for file id $file_id");
			
			$response = array(
				'status' => 'ok'
			);
			echo json_encode($response);
		}
		else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='change_file_duration') {
		if (!empty($_POST['file_id']) && is_numeric($_POST['file_id']) && !empty($_POST['duration']) && is_numeric($_POST['duration'])) {
			query_update_file_length($_POST['file_id'], $_POST['duration']);
			log_action('api-change-file-duration', "Changed duration for file id ${_POST['file_id']} to ${_POST['duration']} seconds");
			
			$response = array(
				'status' => 'ok'
			);
			echo json_encode($response);
		}
		else {
			show_invalid('No valid input provided.');
		}
	} else {
		show_invalid('No valid submethod specified.');
	}
}
else{
	show_invalid('No valid method specified.');
}
?>
