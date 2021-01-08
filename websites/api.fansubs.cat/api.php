<?php
include_once('db.inc.php');
ob_start();
$request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));

function is_outdated_fansubs_app(){
	$ua = $_SERVER['HTTP_USER_AGENT'];
	return strpos($ua,'FansubsCatApp/Android/')===0 && explode(' [', explode('FansubsCatApp/Android/', $ua)[1])[0]<'1.0.2';
}

$method = array_shift($request);
if ($method == 'refresh') {
	$token = array_shift($request);
	if ($token!=NULL){
		$result = mysqli_query($db_connection, "SELECT id FROM fansubs WHERE ping_token='".mysqli_real_escape_string($db_connection, $token)."'") or crash('Internal error: '.mysqli_error($db_connection));
		if ($row = mysqli_fetch_assoc($result)){
			system("cd $services_path && /usr/bin/php fetch.php {$row['id']} > /dev/null &");
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
		http_response_code(400);
		$response = array(
			'status' => 'ko',
			'error' => array(
				'code' => 'INVALID_REQUEST',
				'description' => 'No refresh token has been provided.'
			)
		);
		echo json_encode($response);
	}
}
else if ($method == 'fansubs'){
	$active_result = mysqli_query($db_connection, "SELECT DISTINCT f.id FROM fansubs f LEFT JOIN news n ON f.id=n.fansub_id WHERE f.is_visible=1 AND f.is_historical=0 AND n.date>='".date('Y-m-d H:i:s', time()-3600*24*180)."'") or crash('Internal error: '.mysqli_error($db_connection));
	$active_fansubs = array();
	while($row = mysqli_fetch_assoc($active_result)){
		$active_fansubs[] = $row['id'];
	}

	$result = mysqli_query($db_connection, "SELECT * FROM fansubs ORDER BY name ASC") or crash('Internal error: '.mysqli_error($db_connection));
	$elements = array();
	while($row = mysqli_fetch_assoc($result)){
		$elements[] = array(
			'id' => $row['id'],
			'name' => $row['name'],
			'url' => $row['url'],
			'logo_url' => 'http://www.fansubs.cat/images/fansubs/logos/'.$row['logo_image'],
			'icon_url' => 'http://www.fansubs.cat/images/fansubs/favicons/'.$row['favicon_image'],
			'is_historical' => ($row['is_historical']==1),
			'is_active' => (in_array($row['id'], $active_fansubs)),
			'is_visible' => ($row['is_visible']==1),
			'is_own' => ($row['is_own']==1),
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
			$fansub_ids_extra = " AND n.fansub_id IN ('" . implode("', '", $fansub_ids) . "')";
		}

		$result = mysqli_query($db_connection, "SELECT n.*,f.name fansub_name FROM news n LEFT JOIN fansubs f ON n.fansub_id=f.id WHERE 1" . $search_extra . $fansub_ids_extra . " ORDER BY n.date DESC LIMIT 25 OFFSET $page") or crash('Internal error: ' . mysqli_error($db_connection));
		$elements = array();
		while($row = mysqli_fetch_assoc($result)){
			$elements[] = array(
				'date' => date_create_from_format('Y-m-d H:i:s', $row['date'])->getTimestamp(),
				'fansub_id' => $row['fansub_id'],
				'fansub_name' => $row['fansub_name'],
				'title' => $row['title'],
				'contents' => $row['contents'],
				'url' => $row['url'],
				'image_url' => $row['image']!=NULL ? 'http://www.fansubs.cat/images/news/'.$row['fansub_id'].'/'.$row['image'] : NULL
			);
		}

		$response = array(
			'status' => 'ok',
			'result' => $elements
		);
		echo json_encode($response);
	}
	else{
		http_response_code(400);
		$response = array(
			'status' => 'ko',
			'error' => array(
				'code' => 'INVALID_REQUEST',
				'description' => 'You can not fetch news if you don\'t provide a valid page number.'
			)
		);
		echo json_encode($response);
	}
}
else{
	http_response_code(400);
	$response = array(
		'status' => 'ko',
		'error' => array(
				'code' => 'INVALID_REQUEST',
				'description' => 'No valid method specified.'
			)
	);
	echo json_encode($response);
}

mysqli_close($db_connection);
?>
