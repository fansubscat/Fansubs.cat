<?php
require_once(__DIR__.'/db.inc.php');

session_name(ADMIN_COOKIE_NAME);
session_set_cookie_params(ADMIN_COOKIE_DURATION, '/', COOKIE_DOMAIN, TRUE, FALSE);
session_start();

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_POST['text'])) {
		$text=escape($_POST['text']);
	} else {
		$text='';
	}
	
	$result = query("SELECT s.id,
			CONCAT(
				IF(s.type='manga','".lang('admin.query.search_series.type_manga')."',
					IF(s.type='liveaction','".lang('admin.query.search_series.type_liveaction')."',
						'".lang('admin.query.search_series.type_anime')."'
					)
			),' - ',s.name) name
			FROM series s
			WHERE s.name LIKE '%$text%' OR s.alternate_names LIKE '%$text%' OR EXISTS(SELECT * FROM version v WHERE v.series_id=s.id AND v.title LIKE '%$text%')
			LIMIT 20");
	$series = array();
	while ($row = mysqli_fetch_assoc($result)) {
		array_push($series, $row);
	}

	echo json_encode(array(
		"status" => 'ok',
		"results" => $series
	));
}

mysqli_close($db_connection);
?>
