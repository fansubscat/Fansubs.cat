<?php
ob_start();
require_once("db.inc.php");

$highest_piwigo_category_id=2820;

$file_id = (!empty($_GET['id']) ? intval($_GET['id']) : 0);

if ($file_id>0 && $file_id<=$highest_piwigo_category_id) {
	switch ($file_id) {
		case 1172:
			$file_id=1171;
			break;
		case 1718:
			$file_id=2376;
			break;
		case 1720:
			$file_id=2373;
			break;
		case 1719:
			$file_id=2377;
			break;
		case 1721:
			$file_id=1677;
			break;
		case 2723:
			$file_id=2703;
			break;
		case 2516:
			$file_id=3377;
			break;
	}

	$file_id_for_file=$file_id+10000;
	$result = query("SELECT m.subtype,m.slug,IF(m.subtype='oneshot',NULL,vo.number) division_number FROM file f LEFT JOIN episode c ON f.episode_id=c.id LEFT JOIN division vo ON c.division_id=vo.id LEFT JOIN version v ON f.version_id=v.id LEFT JOIN series m ON v.series_id=m.id WHERE m.type='manga' AND f.id=$file_id_for_file");
	if ($row = mysqli_fetch_assoc($result)) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /".($row['subtype']=='oneshot' ? 'one-shots' : 'serialitzats').'/'.$row['slug'].(!empty($row['division_number']) ? '#volum-'.$row['division_number'] : ''));
		mysqli_free_result($result);
	} else {
		mysqli_free_result($result);
		$file_id_for_version=$file_id+1000;
		$result = query("SELECT m.subtype,m.slug,IF(m.type='oneshot',NULL,v.number) division_number FROM division v LEFT JOIN series m ON v.series_id=m.id WHERE m.type='manga' AND v.id=$file_id_for_version");
		if ($row = mysqli_fetch_assoc($result)) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: /".($row['subtype']=='oneshot' ? 'one-shots' : 'serialitzats').'/'.$row['slug'].(!empty($row['division_number']) ? '#volum-'.$row['division_number'] : ''));
			mysqli_free_result($result);
		} else {
			mysqli_free_result($result);
			$file_id_for_series=$file_id+1000;
			$result = query("SELECT m.subtype,m.slug FROM series m WHERE m.type='manga' AND m.id=$file_id_for_series");
			if ($row = mysqli_fetch_assoc($result)) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: /".($row['subtype']=='oneshot' ? 'one-shots' : 'serialitzats').'/'.$row['slug']);
				mysqli_free_result($result);
			} else {
				mysqli_free_result($result);
				log_action('manga-migration-invalid-id', "No s'ha trobat el manga/volum/capítol migrat amb l'identificador $file_id");
				http_response_code(404);
				include('error.php');
				die(); //Avoids error because mysqli is already closed
			}
		}
	}
}

ob_flush();
mysqli_close($db_connection);
?>
