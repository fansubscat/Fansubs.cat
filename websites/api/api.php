<?php
require_once("../common.fansubs.cat/config.inc.php");
require_once('db.inc.php');
ob_start();
$request = explode('/', trim(explode('?', $_SERVER['REQUEST_URI'])[0], '/'));
$max_items = 20;

function get_nanoid($size=24) {
	//Adapted from: https://github.com/hidehalo/nanoid-php/blob/master/src/Core.php
	$alphabet = '_-0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$len = strlen($alphabet);
	$mask = (2 << (int) (log($len - 1) / M_LN2)) - 1;
	$step = (int) ceil(1.6 * $mask * $size / $len);
	$id = '';
	while (true) {
		$bytes = unpack('C*', random_bytes($step));
		foreach ($bytes as $byte) {
			$byte &= $mask;
			if (isset($alphabet[$byte])) {
				$id .= $alphabet[$byte];
				if (strlen($id) === $size) {
					return $id;
				}
			}
		}
	}
}

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
		return $_GET['api_session_id'];
	}
	$seed = (!empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'Unknown');
	$seed .= (!empty($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'Unknown');
	return get_random_string_from_seed($seed);
}

function is_hentai() {
	if (!empty($_GET['hentai'])) {
		return TRUE;
	}
	return FALSE;
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

function list_remote_files($url) {
	$contents = @file_get_contents($url);
	preg_match_all("|href=[\"'](.*?)[\"']|", $contents, $hrefs);
	$hrefs = array_slice($hrefs[1], 1);
	
	$files = array();
	foreach ($hrefs as $href) {
		array_push($files, $url.$href);
	}
	return $files;
}

$method = array_shift($request);
if ($method == 'refresh') {
	$token = array_shift($request);
	if ($token!=NULL){
		$result = query("SELECT slug FROM fansub WHERE ping_token='".escape($token)."'");
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
	$active_result = query("SELECT DISTINCT f.id FROM fansub f WHERE f.status=1");
	$active_fansubs = array();
	while($row = mysqli_fetch_assoc($active_result)){
		$active_fansubs[] = $row['id'];
	}

	$result = query("SELECT id, slug, name, url, is_historical, archive_url FROM fansub UNION SELECT NULL id, 'fansubs-cat' slug, 'Fansubs.cat' name, NULL, 0, NULL ORDER BY name ASC");
	$elements = array();
	while($row = mysqli_fetch_assoc($result)){
		$elements[] = array(
			'id' => $row['slug'],
			'name' => $row['name'],
			'url' => $row['url'],
			'logo_url' => ($row['slug']=='fansubs-cat' ? STATIC_URL.'/favicons/main/android-chrome-192x192.png' : STATIC_URL.'/images/icons/'.$row['id'].'.png'),
			'icon_url' => ($row['slug']=='fansubs-cat' ? STATIC_URL.'/favicons/main/android-chrome-192x192.png' : STATIC_URL.'/images/icons/'.$row['id'].'.png'),
			'is_historical' => ($row['is_historical']==1),
			'is_active' => (in_array($row['id'], $active_fansubs)),
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
	$search = isset($_GET['search']) ? $_GET['search'] : NULL;
	$fansub_ids = isset($_GET['fansub_ids']) ? $_GET['fansub_ids'] : NULL;
	if ($page!=NULL && is_numeric($page) && $page>=0){
		$page = (int)$page*25;

		$search_extra="";
		$fansub_ids_extra="";

		if ($search!=NULL && $search!=''){
			$search = escape($search);
			$search_extra = " AND (n.title LIKE '%$search%' OR n.contents LIKE '%$search%')";
		}

		if ($fansub_ids!=NULL && count($fansub_ids)>0){
			foreach ($fansub_ids as &$fansub_id){
				$fansub_id = escape($fansub_id);
			}
			$fansub_ids_extra = " AND IFNULL(f.slug,'fansubs-cat') IN ('" . implode("', '", $fansub_ids) . "')";
		}

		$result = query("SELECT n.*, IFNULL(f.slug,'fansubs-cat') fansub_slug, f.name fansub_name FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE 1" . $search_extra . $fansub_ids_extra . " ORDER BY n.date DESC LIMIT 25 OFFSET $page");
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
		show_invalid('You can not fetch news if you don\'t provide a valid page number.');
	}
}
else if ($method === 'manga'){
	$submethod = array_shift($request);
	if ($submethod=='popular') {
		$page = array_shift($request);
		if ($page>0) {
			$offset = ($page-1)*20;

			$result = query("SELECT a.*, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres
			FROM (SELECT SUM(vi.views) views, fi.version_id, m.* FROM series m LEFT JOIN episode c ON c.series_id=m.id LEFT JOIN file fi ON fi.episode_id=c.id LEFT JOIN views vi ON vi.file_id=fi.id WHERE m.type='manga' AND (SELECT COUNT(*) FROM version v WHERE v.series_id=m.id AND v.is_hidden=0)>0 AND fi.episode_id IS NOT NULL AND ".(is_hentai() ? "m.rating='XXX'" : "m.rating<>'XXX'")." GROUP BY fi.version_id, fi.episode_id) a LEFT JOIN rel_series_genre mg ON a.id=mg.series_id LEFT JOIN genre g ON mg.genre_id = g.id
			GROUP BY a.id
			ORDER BY MAX(a.views) DESC, a.name ASC LIMIT $max_items OFFSET $offset");
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => $row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['number_of_episodes']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => STATIC_URL.'/images/covers/'.$row['id'].'.jpg'
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
			$offset = ($page-1)*20;

			$result = query("SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type='manga' AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND ".(is_hentai() ? "s.rating='XXX'" : "s.rating<>'XXX'")." GROUP BY s.id ORDER BY last_updated DESC LIMIT $max_items OFFSET $offset");
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => $row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['number_of_episodes']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => STATIC_URL.'/images/covers/'.$row['id'].'.jpg'
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
		$query = escape($_GET['query']);
		if ($page>0) {
			$offset = ($page-1)*20;

			$result = query("SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type='manga' AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND ".(is_hentai() ? "s.rating='XXX'" : "s.rating<>'XXX'")." AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%' OR s.id IN (SELECT mg.series_id FROM rel_series_genre mg LEFT JOIN genre g ON mg.genre_id=g.id LEFT JOIN series s2 ON mg.series_id=s2.id WHERE s.type='manga' AND g.name='$query')) GROUP BY s.id ORDER BY s.name ASC LIMIT $max_items OFFSET $offset");
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => $row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['number_of_episodes']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => STATIC_URL.'/images/covers/'.$row['id'].'.jpg'
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
		$slug = escape($slug);
		$result = query("SELECT m.*, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres FROM series m LEFT JOIN rel_series_genre mg ON m.id=mg.series_id LEFT JOIN genre g ON mg.genre_id = g.id WHERE m.type='manga' AND (SELECT COUNT(*) FROM version v WHERE v.series_id=m.id AND v.is_hidden=0)>0 AND ".(is_hentai() ? "m.rating='XXX'" : "m.rating<>'XXX'")." AND m.slug='".$slug."'");
		if($row = mysqli_fetch_assoc($result)){
			if (is_numeric($row['id'])) {
				$element = array(
					'slug' => $row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['number_of_episodes']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => STATIC_URL.'/images/covers/'.$row['id'].'.jpg'
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
		$result = query("SELECT fi.id, fi.created, c.number, CONCAT(IF(fi.episode_id IS NULL,'',IF(c.division_id IS NULL,'Altres - ',IF(mv.show_divisions<>1,'Volum únic',IF(vo.name IS NOT NULL,CONCAT(vo.name, ' - '),IF((SELECT COUNT(*) FROM division WHERE series_id=m.id)=1,'Volum únic - ',CONCAT('Volum ',TRIM(vo.number)+0,' - ')))))),IF(fi.episode_id IS NULL, CONCAT('Extra - ',fi.extra_name), IF(ct.title IS NOT NULL, IF(mv.show_episode_numbers AND c.number IS NOT NULL,CONCAT('Capítol ', REPLACE(TRIM(c.number)+0,'.',','), ': ',ct.title),ct.title), IF(m.subtype='oneshot',m.name,IF(c.number IS NOT NULL AND mv.show_episode_numbers=1,CONCAT('Capítol ', REPLACE(TRIM(c.number)+0,'.',',')),IF(c.description IS NOT NULL, c.description, 'Capítol sense nom')))))) episode_title, (SELECT GROUP_CONCAT(f.name SEPARATOR ', ') FROM rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v2 ON vf.version_id=v2.id WHERE vf.version_id=mv.id) fansubs FROM series m LEFT JOIN version mv ON mv.series_id=m.id LEFT JOIN file fi ON fi.version_id=mv.id LEFT JOIN episode_title ct ON ct.version_id=mv.id AND ct.episode_id=fi.episode_id LEFT JOIN episode c ON fi.episode_id=c.id LEFT JOIN division vo ON c.division_id=vo.id WHERE mv.is_hidden=0 AND m.type='manga' AND m.slug='$slug' AND fi.is_lost=0 AND (SELECT COUNT(*) FROM version v WHERE v.series_id=m.id AND v.is_hidden=0)>0 AND ".(is_hentai() ? "m.rating='XXX'" : "m.rating<>'XXX'")." ORDER BY fi.episode_id IS NULL ASC, vo.number IS NULL ASC, vo.number DESC, c.number DESC, episode_title DESC, fi.extra_name DESC, fi.created DESC");
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'id' => $row['id'],
				'title' => $row['episode_title'],
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
		$file_id = intval(array_shift($request));

		//IMPORTANT: We add 10000 because when the unification of catalogues occurred,
		//we added 10000 to the manga file ids. However, this has problems with Tachiyomi.
		//We therefore use the original id from the request, and add 10000 to get the DB id if needed.
		if ($file_id<=10000) {
			$file_id+=10000;
		}

		$result = query("SELECT f.* FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE v.is_hidden=0 AND ".(is_hentai() ? "s.rating='XXX'" : "s.rating<>'XXX'")." AND f.id=$file_id");
		if ($row = mysqli_fetch_assoc($result)) {
			$my_api_session_id = escape(get_api_session_id());
			//Check if this view is already in the database: same user agent, same IP and same file in the last hour
			$exists_result = query("SELECT * FROM view_session WHERE file_id=$file_id AND anon_id='API-$my_api_session_id'");
			if (mysqli_num_rows($exists_result)==0) {
				$date = date('Y-m-d');
				$length = intval($row['length']);
				$ip = escape($_SERVER['REMOTE_ADDR']);
				$user_agent = escape($_SERVER['HTTP_USER_AGENT']);
				query("INSERT INTO view_session (id, file_id, type, user_id, anon_id, progress, length, created, updated, view_counted, is_casted, source, ip, user_agent) VALUES ('".get_nanoid()."', $file_id, 'manga', NULL, 'API-$my_api_session_id', $length, $length, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, 'api', '$ip', '$user_agent')");
				query("REPLACE INTO views
					SELECT $file_id,
						'$date',
						'manga',
						IFNULL((SELECT clicks+1 FROM views WHERE file_id=$file_id AND day='$date'),1),
						IFNULL((SELECT views+1 FROM views WHERE file_id=$file_id AND day='$date'),1),
						IFNULL((SELECT total_length+$length FROM views WHERE file_id=$file_id AND day='$date'),$length)");
			}

			$base_path=get_storage_url("storage://Manga/$file_id/", TRUE);
			$files = list_remote_files($base_path);
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
else if ($method === 'internal' && !empty($_GET['token']) && $_GET['token']===$internal_token){
	$submethod = array_shift($request);
	if ($submethod=='get_manga_files') {
		$result = query("SELECT f.*, s.slug, IF(f.extra_name IS NULL,FALSE,TRUE) is_extra FROM file f LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='manga' AND f.is_lost=0 ORDER BY s.name ASC, f.original_filename ASC");
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
		$result = query("SELECT l.*, s.type, v.storage_folder, v.storage_processing, IF(f.extra_name IS NULL,FALSE,TRUE) is_extra FROM link l LEFT JOIN file f ON l.file_id=f.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE url NOT LIKE 'storage://%'".(!empty($file_id) ? " AND f.id>=$file_id" : '')." AND NOT EXISTS (SELECT * FROM link l2 WHERE l2.file_id=l.file_id AND l2.url LIKE 'storage://%') ORDER BY s.name ASC, f.id ASC");
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
		$result = query("SELECT l.*, s.type, v.storage_folder, v.storage_processing, IF(f.extra_name IS NULL,FALSE,TRUE) is_extra, f.length FROM link l LEFT JOIN file f ON l.file_id=f.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE url LIKE 'storage://%'".(!empty($file_id) ? " AND f.id>=$file_id" : '')." ORDER BY s.name ASC, f.id ASC");
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
			$file_id=intval($_POST['file_id']);
			$url=escape($_POST['url']);
			$original_url=escape($_POST['original_url']);
			$resolution=escape($_POST['resolution']);
			$result = query("INSERT INTO link (file_id, url, resolution, created, created_by, updated, updated_by) SELECT $file_id, '$url', '$resolution', CURRENT_TIMESTAMP, 'API', CURRENT_TIMESTAMP, 'API' FROM link WHERE EXISTS (SELECT url FROM link WHERE url='".$original_url."' AND file_id=".$file_id.") LIMIT 1");
			if (get_previous_query_num_affected_rows()>0) {
				log_action('api-insert-converted-link', "S’ha inserit l’enllaç convertit «$url» del fitxer amb id. $file_id");
			} else {
				log_action('api-discard-converted-link', "S’ha descartat l’enllaç convertit «$url» del fitxer amb id. $file_id, segurament s’ha actualitzat mentre es convertia");
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
			log_action('api-change-file-thumbnail', "S’ha canviat la miniatura del fitxer amb id. $file_id");
			
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
			$file_id=intval($_POST['file_id']);
			$duration=intval($_POST['duration']);
			$result = query("UPDATE file SET length=$duration WHERE id=$file_id");
			log_action('api-change-file-duration', "S’ha canviat la durada del fitxer amb id. $file_id a $duration segons");
			
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
