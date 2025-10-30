<?php
require_once(__DIR__.'/db.inc.php');

session_name(ADMIN_COOKIE_NAME);
session_set_cookie_params(ADMIN_COOKIE_DURATION, '/', COOKIE_DOMAIN, TRUE, FALSE);
session_start();

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (is_numeric($_POST['series_id'])) {
		$series_id=escape($_POST['series_id']);
	} else {
		crash(lang('admin.error.series_id_not_numeric'));
	}
	if (is_numeric($_POST['division_id'])) {
		$division_id=escape($_POST['division_id']);
	} else {
		crash(lang('admin.error.series_id_not_numeric'));
	}
	if ((!empty($_POST['number']) || $_POST['number']=='0') && is_numeric($_POST['number'])) {
		$number=escape($_POST['number']);
	} else {
		$number="NULL";
	}
	if (!empty($_POST['description'])) {
		$description="'".escape($_POST['description'])."'";
	} else {
		$description="NULL";
	}
	
	query("INSERT INTO episode (series_id,division_id,number,description,linked_episode_id,created,created_by,updated,updated_by) VALUES (".$series_id.",".$division_id.",".$number.",".$description.",NULL,CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
	$inserted_id=mysqli_insert_id($db_connection);
	query("UPDATE division SET number_of_episodes=number_of_episodes+1 WHERE id=".$division_id);
	query("UPDATE series SET number_of_episodes=number_of_episodes+1,updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$series_id);
	
	$result = query("SELECT e.*,
				REPLACE(TRIM(e.number)+0, '.', ',') formatted_number,
				IF(s.subtype='movie' OR s.subtype='oneshot',
					IF(e.number IS NOT NULL,
						IF(s.number_of_episodes=1,
							s.name,
							CONCAT(d.name, ' - ".lang('generic.query.movie_space')."', REPLACE(TRIM(e.number)+0, '.', ','))
						),
						e.description
					),
					IF(e.number IS NOT NULL,
						CONCAT(d.name, ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', ',')),
						CONCAT(d.name, ' - ', e.description)
					)
				) episode_title,
				NULL title
			FROM episode e
			LEFT JOIN series s ON e.series_id=s.id
			LEFT JOIN division d ON e.division_id=d.id
			WHERE e.id=".$inserted_id);
	$inserted_episode = mysqli_fetch_assoc($result);

	echo json_encode(array(
		"status" => 'ok',
		"inserted_id" => $inserted_id,
		"formatted_number" => $inserted_episode['formatted_number'],
		"episode_title" => $inserted_episode['episode_title'],
		"division_id" => $inserted_episode['division_id']
	));
}

mysqli_close($db_connection);
?>
