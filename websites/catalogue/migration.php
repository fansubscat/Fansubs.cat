<?php
ob_start();
require_once("queries.inc.php");

$migration_type = (!empty($_GET['migration_type']) ? $_GET['migration_type'] : 'piwigo');

if ($migration_type=='piwigo') {
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
		$result = query_manga_division_data_from_file_with_old_piwigo_id($file_id_for_file);
		if ($row = mysqli_fetch_assoc($result)) {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: /".$row['slug'].(!empty($row['division_number']) ? '#version-'.$row['version_id'].'-division-'.floatval($row['division_number']) : ''));
			mysqli_free_result($result);
		} else {
			mysqli_free_result($result);
			$file_id_for_division=$file_id+1000;
			$result = query_manga_division_data_from_division_with_old_piwigo_id($file_id_for_division);
			if ($row = mysqli_fetch_assoc($result)) {
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: /".$row['slug'].(!empty($row['division_number']) ? '#version-'.$row['version_id'].'-division-'.floatval($row['division_number']) : ''));
				mysqli_free_result($result);
			} else {
				mysqli_free_result($result);
				$file_id_for_series=$file_id+1000;
				$result = query_manga_series_data_from_series_with_old_piwigo_id($file_id_for_division);
				if ($row = mysqli_fetch_assoc($result)) {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: /".$row['slug']);
					mysqli_free_result($result);
				} else {
					mysqli_free_result($result);
					log_action('manga-migration-invalid-id', "No s’ha trobat el manga/volum/capítol migrat amb l’identificador $file_id");
					http_response_code(404);
					include('error.php');
					die(); //Avoids error because mysqli is already closed
				}
			}
		}
	}
} else if ($migration_type=='tachiyomi_cache') {
	$series_id = (!empty($_GET['id']) ? intval($_GET['id']) : 0)+1000;
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ".STATIC_URL."/images/covers/".$series_id.".jpg");
} else if ($migration_type=='v4_slug') {
	$series_slug = (!empty($_GET['id']) ? $_GET['id'] : '');
	$type = (!empty($_GET['type']) ? $_GET['type'] : '');
	$result = query_series_data_from_slug_and_type($series_slug, $type);
	if ($row = mysqli_fetch_assoc($result)) {
		if ($row['rating']!='XXX') {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: /".$row['slug']);
			mysqli_free_result($result);
		} else if ($type=='manga') {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".HENTAI_MANGA_URL."/".$row['slug']);
			mysqli_free_result($result);
		} else {
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".HENTAI_ANIME_URL."/".$row['slug']);
			mysqli_free_result($result);
		}
	} else {
		$result_old_slug = query_series_by_old_slug($series_slug);
		if ($new_slug = mysqli_fetch_assoc($result_old_slug)) {
			$result = query_series_data_from_slug_and_type($new_slug['slug'], $type);
			if ($row = mysqli_fetch_assoc($result)) {
				if ($row['rating']!='XXX') {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: /".$row['slug']);
					mysqli_free_result($result);
				} else if ($type=='manga') {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ".HENTAI_MANGA_URL."/".$row['slug']);
					mysqli_free_result($result);
				} else {
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ".HENTAI_ANIME_URL."/".$row['slug']);
					mysqli_free_result($result);
				}
			} else {
				http_response_code(404);
				include('error.php');
				die(); //Avoids error because mysqli is already closed
			}
		} else {
			http_response_code(404);
			include('error.php');
			die(); //Avoids error because mysqli is already closed
		}
		mysqli_free_result($result_old_slug);
	}
}

ob_flush();
mysqli_close($db_connection);
?>
