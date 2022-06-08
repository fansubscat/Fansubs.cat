<?php
include_once('db.inc.php');
ob_start();
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
$max_items = 20;

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

$method = array_shift($request);
if ($method == 'refresh') {
	$token = array_shift($request);
	if ($token!=NULL){
		$result = mysqli_query($db_connection, "SELECT slug FROM fansub WHERE ping_token='".mysqli_real_escape_string($db_connection, $token)."'") or crash('Internal error: '.mysqli_error($db_connection));
		if ($row = mysqli_fetch_assoc($result)){
			system("cd $services_directory && /usr/bin/php fetch.php {$row['slug']} > /dev/null &");
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
	$active_result = mysqli_query($db_connection, "SELECT DISTINCT f.id FROM fansub f WHERE f.status=1 AND f.name<>'Fansub independent'") or crash('Internal error: '.mysqli_error($db_connection));
	$active_fansubs = array();
	while($row = mysqli_fetch_assoc($active_result)){
		$active_fansubs[] = $row['id'];
	}

	$result = mysqli_query($db_connection, "SELECT id, slug, name, url, is_historical, archive_url FROM fansub UNION SELECT NULL id, 'fansubs-cat' slug, 'Fansubs.cat' name, NULL, 0, NULL ORDER BY name ASC") or crash('Internal error: '.mysqli_error($db_connection));
	$elements = array();
	while($row = mysqli_fetch_assoc($result)){
		$elements[] = array(
			'id' => $row['slug'],
			'name' => $row['name'],
			'url' => $row['url'],
			'logo_url' => $static_url.'/images/logos/'.$row['id'].'.png',
			'icon_url' => ($row['slug']=='fansubs-cat' ? 'https://www.fansubs.cat/favicon.ico' : $static_url.'/images/icons/'.$row['id'].'.png'),
			'is_historical' => ($row['is_historical']==1),
			'is_active' => (in_array($row['id'], $active_fansubs)),
			'is_visible' => ($row['slug']!='fansubs-cat' && $row['slug']!='fansubs-independents'),
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
			$search = mysqli_real_escape_string($db_connection, $search);
			$search_extra = " AND (n.title LIKE '%$search%' OR n.contents LIKE '%$search%')";
		}

		if ($fansub_ids!=NULL && count($fansub_ids)>0){
			foreach ($fansub_ids as &$fansub_id){
				$fansub_id = mysqli_real_escape_string($db_connection, $fansub_id);
			}
			$fansub_ids_extra = " AND IFNULL(f.slug,'fansubs-cat') IN ('" . implode("', '", $fansub_ids) . "')";
		}

		$result = mysqli_query($db_connection, "SELECT n.*, IFNULL(f.slug,'fansubs-cat') fansub_slug, f.name fansub_name FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE 1" . $search_extra . $fansub_ids_extra . " ORDER BY n.date DESC LIMIT 25 OFFSET $page") or crash('Internal error: ' . mysqli_error($db_connection));
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'date' => date_create_from_format('Y-m-d H:i:s', $row['date'])->getTimestamp(),
				'fansub_id' => $row['fansub_slug'],
				'fansub_name' => $row['fansub_name'],
				'title' => $row['title'],
				'contents' => $row['contents'],
				'url' => $row['url'],
				'image_url' => $row['image']!=NULL ? $static_url.'/images/news/'.$row['fansub_slug'].'/'.$row['image'] : NULL
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

			$result = mysqli_query($db_connection, "SELECT a.*, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres
			FROM (SELECT SUM(vi.views) views, fi.version_id, m.* FROM series m LEFT JOIN episode c ON c.series_id=m.id LEFT JOIN file fi ON fi.episode_id=c.id LEFT JOIN views vi ON vi.file_id=fi.id WHERE m.type='manga' AND fi.episode_id IS NOT NULL GROUP BY fi.version_id, fi.episode_id) a LEFT JOIN rel_series_genre mg ON a.id=mg.series_id LEFT JOIN genre g ON mg.genre_id = g.id
			GROUP BY a.id
			ORDER BY a.rating IS NOT NULL AND a.rating='XXX' ASC, MAX(a.views) DESC, a.name ASC LIMIT $max_items OFFSET $offset") or crash('Internal error: ' . mysqli_error($db_connection));
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => ($row['subtype']=='oneshot' ? 'one-shots/' : 'serialitzats/').$row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['number_of_episodes']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => $static_url.'/images/covers/'.$row['id'].'.jpg'
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

			$result = mysqli_query($db_connection, "SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type='manga' AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 GROUP BY s.id ORDER BY last_updated DESC LIMIT $max_items OFFSET $offset") or crash('Internal error: ' . mysqli_error($db_connection));
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => ($row['subtype']=='oneshot' ? 'one-shots/' : 'serialitzats/').$row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['number_of_episodes']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => $static_url.'/images/covers/'.$row['id'].'.jpg'
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
		$page = explode('?', $page)[0];
		$query = mysqli_real_escape_string($db_connection, $_GET['query']);
		if ($page>0) {
			if (!empty($query)){
				//Improvement: We can add source (Tachiyomi) if we ever want to differentiate
				mysqli_query($db_connection, "INSERT INTO search_history (query,day) VALUES ('$query','".date('Y-m-d')."')") or crash('Internal error: ' . mysqli_error($db_connection));
			}

			$offset = ($page-1)*20;

			$result = mysqli_query($db_connection, "SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type='manga' AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%') OR s.id IN (SELECT mg.series_id FROM rel_series_genre mg LEFT JOIN genre g ON mg.genre_id=g.id WHERE g.name='$query') GROUP BY s.id ORDER BY s.name ASC LIMIT $max_items OFFSET $offset") or crash('Internal error: ' . mysqli_error($db_connection));
			$elements = array();
			while($row = mysqli_fetch_assoc($result)){
				$elements[] = array(
					'slug' => ($row['subtype']=='oneshot' ? 'one-shots/' : 'serialitzats/').$row['slug'],
					'name' => $row['name'],
					'author' => $row['author'],
					'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
					'status' => $row['number_of_episodes']>=1 ? 'finished' : 'ongoing',
					'thumbnail_url' => $static_url.'/images/covers/'.$row['id'].'.jpg'
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
		$slug = mysqli_real_escape_string($db_connection, $slug);
		$result = mysqli_query($db_connection, "SELECT m.*, GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres FROM series m LEFT JOIN rel_series_genre mg ON m.id=mg.series_id LEFT JOIN genre g ON mg.genre_id = g.id WHERE m.type='manga' AND m.slug='".$slug."'") or crash('Internal error: ' . mysqli_error($db_connection));
		if($row = mysqli_fetch_assoc($result)){
			$element = array(
				'slug' => ($row['subtype']=='oneshot' ? 'one-shots/' : 'serialitzats/').$row['slug'],
				'name' => $row['name'],
				'author' => $row['author'],
				'synopsis' => $row['synopsis'],
					'genres' => $row['genres'],
				'status' => $row['number_of_episodes']>=1 ? 'finished' : 'ongoing',
				'thumbnail_url' => $static_url.'/images/covers/'.$row['id'].'.jpg'
			);

			$response = array(
				'status' => 'ok',
				'result' => $element
			);
			echo json_encode($response);
		} else {
			show_invalid('No valid manga specified.');
		}
	} else if ($submethod=='chapters') {
		$slug = array_shift($request);
		$slug = mysqli_real_escape_string($db_connection, $slug);
		$result = mysqli_query($db_connection, "SELECT fi.id, fi.created, c.number, CONCAT(IF(fi.episode_id IS NULL,'',IF(c.division_id IS NULL,'Altres - ',IF(mv.show_divisions<>1,'Volum únic',IF(vo.name IS NOT NULL,CONCAT(vo.name, ' - '),IF((SELECT COUNT(*) FROM division WHERE series_id=m.id)=1,'Volum únic - ',CONCAT('Volum ',vo.number,' - ')))))),IF(fi.episode_id IS NULL, CONCAT('Extra - ',fi.extra_name), IF(ct.title IS NOT NULL, IF(mv.show_episode_numbers AND c.number IS NOT NULL,CONCAT('Capítol ', REPLACE(TRIM(c.number)+0,'.',','), ': ',ct.title),ct.title), IF(m.subtype='oneshot',m.name,IF(c.number IS NOT NULL AND mv.show_episode_numbers=1,CONCAT('Capítol ', REPLACE(TRIM(c.number)+0,'.',',')),IF(c.description IS NOT NULL, c.description, 'Capítol sense nom')))))) episode_title, (SELECT GROUP_CONCAT(f.name SEPARATOR ', ') FROM rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE vf.version_id=mv.id) fansubs FROM series m LEFT JOIN version mv ON mv.series_id=m.id LEFT JOIN file fi ON fi.version_id=mv.id LEFT JOIN episode_title ct ON ct.version_id=mv.id AND ct.episode_id=fi.episode_id LEFT JOIN episode c ON fi.episode_id=c.id LEFT JOIN division vo ON c.division_id=vo.id WHERE mv.is_hidden=0 AND m.type='manga' AND m.slug='$slug' AND fi.is_lost=0 ORDER BY fi.episode_id IS NULL ASC, vo.number IS NULL ASC, vo.number DESC, c.number DESC, episode_title DESC, fi.extra_name DESC, fi.created DESC") or crash('Internal error: ' . mysqli_error($db_connection));
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

		$base_path="$static_directory/storage/$file_id/";

		if (!file_exists($base_path)) {
			show_invalid('No valid file specified.');
		} else {
			$result = mysqli_query($db_connection, "SELECT f.* FROM file f WHERE f.id=$file_id");
			if ($row = mysqli_fetch_assoc($result)) {
				$user_agent = mysqli_real_escape_string($db_connection, $_SERVER['HTTP_USER_AGENT']);

				//Check if this view is already in the database: same user agent, same IP and same file in the last hour
				$exists_result = mysqli_query($db_connection, "SELECT * FROM view_log WHERE file_id=$file_id AND ip='".mysqli_real_escape_string($db_connection, $_SERVER['REMOTE_ADDR'])."' AND user_agent='$user_agent [via API]' AND date>= (NOW() - INTERVAL 1 HOUR)");
				if (mysqli_num_rows($exists_result)==0) {
					$pages_read=$row['length'];
					mysqli_query($db_connection, "REPLACE INTO views SELECT $file_id, '".date('Y-m-d')."', 'manga', IFNULL((SELECT clicks+1 FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views+1 FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), NULL, IFNULL((SELECT pages_read+$pages_read FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),$pages_read)");
					mysqli_query($db_connection, "INSERT INTO view_log (type, file_id, ip, date, user_agent, view_type) VALUES ('manga', $file_id, '".mysqli_real_escape_string($db_connection, $_SERVER['REMOTE_ADDR'])."', CURRENT_TIMESTAMP, '$user_agent [via API]', 'api')");
				}
				mysqli_free_result($exists_result);
			}
			$files = scandir($base_path);
			natsort($files);
			$elements = array();
			foreach ($files as $file) {
				if ($file=='.' || $file=='..') {
					continue;
				}
				$elements[] = array(
					'url' => $static_url.'/storage/'.$file_id.'/'.$file
				);
			}

			$response = array(
				'status' => 'ok',
				'result' => $elements
			);
			echo json_encode($response);
		}
	} else {
		show_invalid('No valid submethod specified.');
	}
}
else if ($method === 'internal' && !empty($_GET['token']) && $_GET['token']===$internal_token){
	$submethod = array_shift($request);
	if ($submethod=='get_unconverted_links') {
		$result = mysqli_query($db_connection, "SELECT l.*, s.type, v.storage_folder, v.storage_processing, IF(f.extra_name IS NULL,FALSE,TRUE) is_extra FROM link l LEFT JOIN file f ON l.file_id=f.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE url NOT LIKE 'storage://%'".((!empty($_GET['from_id']) && is_numeric($_GET['from_id'])) ? " AND f.id>=".$_GET['from_id'] : '')." AND NOT EXISTS (SELECT * FROM link l2 WHERE l2.file_id=l.file_id AND l2.url LIKE 'storage://%') ORDER BY s.name ASC, f.id ASC") or crash('Internal error: ' . mysqli_error($db_connection));
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
		$result = mysqli_query($db_connection, "SELECT l.*, s.type, v.storage_folder, v.storage_processing, IF(f.extra_name IS NULL,FALSE,TRUE) is_extra, f.length FROM link l LEFT JOIN file f ON l.file_id=f.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE url LIKE 'storage://%'".((!empty($_GET['from_id']) && is_numeric($_GET['from_id'])) ? " AND f.id>=".$_GET['from_id'] : '')." ORDER BY s.name ASC, f.id ASC") or crash('Internal error: ' . mysqli_error($db_connection));
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
			$file_id=$_POST['file_id'];
			$url=mysqli_real_escape_string($db_connection, $_POST['url']);
			$original_url=mysqli_real_escape_string($db_connection, $_POST['original_url']);
			$resolution=mysqli_real_escape_string($db_connection, $_POST['resolution']);
			$result = mysqli_query($db_connection, "INSERT INTO link (file_id, url, resolution, created,created_by,updated,updated_by) SELECT $file_id, '$url', '$resolution', CURRENT_TIMESTAMP, 'API', CURRENT_TIMESTAMP, 'API' FROM link WHERE EXISTS (SELECT url FROM link WHERE url='".$original_url."' AND file_id=".$file_id.") LIMIT 1") or crash('Internal error: ' . mysqli_error($db_connection));
			if (mysqli_affected_rows($db_connection)>0) {
				log_action('api-insert-converted-link', "S'ha inserit l'enllaç convertit '$url' del fitxer amb id. $file_id");
			} else {
				log_action('api-discard-converted-link', "S'ha descartat l'enllaç convertit '$url' del fitxer amb id. $file_id, segurament s'ha actualitzat mentre es convertia");
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
			$file_id=$_POST['file_id'];
			move_uploaded_file($_FILES['thumbnail']["tmp_name"], $static_directory.'/images/files/'.$file_id.'.jpg');
			log_action('api-change-file-thumbnail', "S'ha canviat la miniatura del fitxer amb id. $file_id");
			
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
			$file_id=$_POST['file_id'];
			$duration=$_POST['duration'];
			$result = mysqli_query($db_connection, "UPDATE file SET length=$duration WHERE id=$file_id") or crash('Internal error: ' . mysqli_error($db_connection));
			log_action('api-change-file-duration', "S'ha canviat la durada del fitxer amb id. $file_id a $duration segons");
			
			$response = array(
				'status' => 'ok'
			);
			echo json_encode($response);
		}
		else {
			show_invalid('No valid input provided.');
		}
	} else if ($submethod=='report_tracking') {
		if (!empty($_POST['view_id']) && !empty($_POST['file_id']) && is_numeric($_POST['file_id']) && !empty($_POST['bytes_read']) && is_numeric($_POST['bytes_read']) && !empty($_POST['total_bytes']) && is_numeric($_POST['total_bytes'])) {
			$view_id=mysqli_real_escape_string($db_connection, $_POST['view_id']);
			$file_id=mysqli_real_escape_string($db_connection, $_POST['file_id']);
			$bytes_read=mysqli_real_escape_string($db_connection, $_POST['bytes_read']);
			$total_bytes=mysqli_real_escape_string($db_connection, $_POST['total_bytes']);
			$ip=mysqli_real_escape_string($db_connection, $_POST['ip']);
			$user_agent=mysqli_real_escape_string($db_connection, $_POST['user_agent']);
			$result = mysqli_query($db_connection, "INSERT INTO view_session VALUES ('$view_id', 'size', $file_id, 0, (SELECT length FROM file f WHERE f.id=$link_id), $bytes_read, $total_bytes, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '$ip', NULL, '$user_agent', 0, 0, 0, '') ON DUPLICATE KEY UPDATE bytes_read=bytes_read+$bytes_read, total_bytes=$total_bytes, ip='$ip', user_agent_read=IF(user_agent IS NULL OR user_agent_read IS NULL OR user_agent<>'$user_agent','$user_agent',user_agent_read), last_update=CURRENT_TIMESTAMP") or crash('Internal error: ' . mysqli_error($db_connection));
			
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

mysqli_close($db_connection);
?>
