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

	$result = query("SELECT m.type,m.slug,IF(m.type='oneshot',NULL,vo.number) volume_number FROM file f LEFT JOIN chapter c ON f.chapter_id=c.id LEFT JOIN volume vo ON c.volume_id=vo.id LEFT JOIN manga_version v ON f.manga_version_id=v.id LEFT JOIN manga m ON v.manga_id=m.id WHERE f.id=$file_id");
	if ($row = mysqli_fetch_assoc($result)) {
		header("HTTP/1.1 301 Moved Permanently");
		header("Location: /".($row['type']=='oneshot' ? 'one-shots' : 'serialitzats').'/'.$row['slug'].(!empty($row['volume_number']) ? '#volum-'.$row['volume_number'] : ''));
		mysqli_free_result($result);
	} else {
		mysqli_free_result($result);
		$result = query("SELECT m.type,m.slug,IF(m.type='oneshot',NULL,v.number) volume_number FROM volume v LEFT JOIN manga m ON v.manga_id=m.id WHERE v.id=$file_id");
		if ($row = mysqli_fetch_assoc($result)) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: /".($row['type']=='oneshot' ? 'one-shots' : 'serialitzats').'/'.$row['slug'].(!empty($row['volume_number']) ? '#volum-'.$row['volume_number'] : ''));
			mysqli_free_result($result);
		} else {
			mysqli_free_result($result);
			$result = query("SELECT m.type,m.slug FROM manga m WHERE m.id=$file_id");
			if ($row = mysqli_fetch_assoc($result)) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: /".($row['type']=='oneshot' ? 'one-shots' : 'serialitzats').'/'.$row['slug']);
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
