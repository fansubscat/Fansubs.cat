<?php
require_once('db.inc.php');
require_once('common.inc.php');

$base_path_piwigo = '/srv/websites/manga.fansubs.cat/';
$base_path_manga = '/srv/websites/mangav2.fansubs.cat/images/';
$dry_run=FALSE;

function escape($string){
	global $db_connection;
	return mysqli_real_escape_string($db_connection, $string);
}

function query($query){
	global $dry_run;
	global $db_connection;
	if ($dry_run) {
		echo "Run query: $query\n";
		return TRUE;
	} else {
		$result = mysqli_query($db_connection, $query) or die(mysqli_error($db_connection));
		return $result;
	}
}

function query_piwigo($query){
	global $db_connection_manga;
	$result = mysqli_query($db_connection_manga, $query) or die(mysqli_error($db_connection_manga));
	return $result;
}

function create_dir($name){
	global $dry_run;
	if ($dry_run) {
		echo "Create directory: $name\n";
		return TRUE;
	} else {
		return mkdir($name);
	}
}

function copy_file($origin, $destination){
	global $dry_run;
	if ($dry_run) {
		echo "Copy file from '$origin' to '$destination'\n";
		return TRUE;
	} else {
		return copy($origin, $destination);
	}
}

function move_file($origin, $destination){
	global $dry_run;
	if ($dry_run) {
		echo "Move file from '$origin' to '$destination'\n";
		return TRUE;
	} else {
		return rename($origin, $destination);
	}
}

function import_images($piwigo_category_id, $volume_cover_image_id, $manga_id, $volume_id){
	global $base_path_manga;
	global $base_path_piwigo;
	//Find the images
	$number_of_pages = 0;
	$first_upload_date = '2022-01-01 00:00:00';
	$last_upload_date = '2000-01-01 00:00:00';
	$resulti = query_piwigo("SELECT i.* FROM piwigo_image_category ic LEFT JOIN piwigo_images i ON ic.image_id=i.id WHERE ic.category_id={$piwigo_category_id}");
	while ($image = mysqli_fetch_assoc($resulti)) {
		if ($image['date_available']>$last_upload_date) {
			$last_upload_date = $image['date_available'];
		}
		if ($image['date_available']<$first_upload_date) {
			$first_upload_date = $image['date_available'];
		}
		if ($image['id']==$volume_cover_image_id) {
			copy_file("{$base_path_piwigo}{$image['path']}", "{$base_path_manga}covers/{$manga_id}_{$volume_id}.jpg");
		}
		/*if (!file_exists("{$base_path_manga}storage/{$piwigo_category_id}")) {
			create_dir("{$base_path_manga}storage/{$piwigo_category_id}");
		}
		$filename=preg_replace('/[^0-9a-zA-Z_\.]/u','_', strtolower($image['file']));
		//TODO Change to move when definitive
		copy_file("{$base_path_piwigo}{$image['path']}", "{$base_path_manga}storage/{$piwigo_category_id}/{$filename}");*/
		
		$number_of_pages++;
	}
	mysqli_free_result($resulti);

	return array(
		'number_of_pages' => $number_of_pages,
		'first_upload_date' => $first_upload_date,
		'last_upload_date' => $last_upload_date
	);
}


//Connect to the manga database
$db_connection_manga = mysqli_connect($db_host_manga,$db_user_manga,$db_passwd_manga, $db_name_manga) or die('Could not connect to manga database');
unset($db_host_manga, $db_name_manga, $db_user_manga, $db_passwd_manga);
mysqli_set_charset($db_connection_manga, 'utf8mb4') or crash(mysqli_error($db_connection_manga));

$resultm = query_piwigo("SELECT c.id, c.name, c.comment, c.id_uppercat, c.representative_picture_id, EXISTS(SELECT c2.* FROM piwigo_categories c2 WHERE c2.id_uppercat=c.id) has_subalbums FROM piwigo_categories c WHERE id_uppercat IS NULL ORDER BY id ASC");
while ($manga = mysqli_fetch_assoc($resultm)){
	if (!$manga['has_subalbums']) {
		$slug = escape(slugify($manga['name']));
		$name = escape($manga['name']);
		$description = escape($manga['comment']);
		$fansubs = array();

		//Find which fansub uploaded this (max 5 levels)
		$fansubres = query_piwigo("SELECT GROUP_CONCAT(DISTINCT u.username SEPARATOR '|') fansub_name FROM piwigo_image_category ic LEFT JOIN piwigo_images i ON ic.image_id=i.id LEFT JOIN piwigo_users u ON i.added_by=u.id WHERE category_id IN (SELECT DISTINCT IFNULL(c4.id,IFNULL(c3.id,IFNULL(c2.id,c1.id))) id FROM piwigo_categories c1 LEFT JOIN piwigo_categories c2 ON c2.id_uppercat=c1.id LEFT JOIN piwigo_categories c3 ON c3.id_uppercat=c2.id LEFT JOIN piwigo_categories c4 ON c4.id_uppercat=c3.id WHERE c1.id_uppercat=".$manga['id']." UNION SELECT ".$manga['id'].")");
		$fansub_names = explode('|', mysqli_fetch_assoc($fansubres)['fansub_name']);
		foreach ($fansub_names as $fansub_name) {
			switch ($fansub_name){
				case 'CatSub':
					$fansubs[]=1;
					break;
				case 'El Detectiu Conan':
					$fansubs[]=53;
					break;
				case 'Lluna Plena no Fansub':
					$fansubs[]=2;
					break;
				default:
					//Independent
					$fansubs[]=28;
					break;
			}
		}
		mysqli_free_result($fansubres);

		//Find the cover
		if (!empty($manga['representative_picture_id'])) {
			$resulti = query_piwigo("SELECT i.* FROM piwigo_images i WHERE i.id={$manga['representative_picture_id']}");
			if ($image = mysqli_fetch_assoc($resulti)) {
				copy_file("{$base_path_piwigo}{$image['path']}", "{$base_path_manga}manga/{$manga['id']}.jpg");
				copy_file("{$base_path_piwigo}{$image['path']}", "{$base_path_manga}featured/{$manga['id']}.jpg");
			}
			mysqli_free_result($resulti);
		}

		//Find and copy the images
		$copy_result = import_images($manga['id'], $manga['representative_picture_id'], $manga['id'], $manga['id']);
		$number_of_pages = $copy_result['number_of_pages'];
		$first_upload_date = $copy_result['first_upload_date'];
		$last_upload_date = $copy_result['last_upload_date'];

		query("INSERT INTO manga (id, slug, name, alternate_names, keywords, type, publish_date, author, rating, chapters, synopsis, myanimelist_id, tadaima_id, score, reader_type, show_volumes, show_expanded_volumes, show_chapter_numbers, show_unavailable_chapters, has_licensed_parts, order_type, created, created_by, updated, updated_by) VALUES ({$manga['id']}, '{$slug}', '{$name}',NULL,NULL,'oneshot',NULL,NULL,'TP',1,'{$description}',NULL,NULL,NULL,'paged',1,1,0,1,0,0,'{$first_upload_date}','PiwigoImport','{$last_upload_date}','PiwigoImport')");
		query("INSERT INTO manga_version (id, manga_id, status, chapters_missing, created, created_by, updated, updated_by, files_updated, files_updated_by, is_featurable, is_always_featured) VALUES ({$manga['id']}, {$manga['id']}, 1, 0, '{$first_upload_date}', 'PiwigoImport', '{$last_upload_date}', 'PiwigoImport', '{$last_upload_date}', 'PiwigoImport', 1, 0)");
		foreach ($fansubs as $fansub_id) {
			query("INSERT INTO rel_manga_version_fansub (manga_version_id, fansub_id, downloads_url) VALUES ({$manga['id']}, {$fansub_id}, NULL)");
		}
		query("INSERT INTO volume (id, manga_id, number, name, chapters, myanimelist_id) VALUES ({$manga['id']}, {$manga['id']}, 1, NULL ,1 ,NULL)");
		query("INSERT INTO chapter (id, manga_id, volume_id, number, name) VALUES ({$manga['id']}, {$manga['id']}, {$manga['id']}, 1, '{$name}')");
		query("INSERT INTO chapter_title (manga_version_id, chapter_id, title) VALUES ({$manga['id']}, {$manga['id']}, '{$name}')");
		query("INSERT INTO file (id, manga_version_id, chapter_id, extra_name, original_filename, number_of_pages, comments, created) VALUES ({$manga['id']}, {$manga['id']}, {$manga['id']}, NULL, '(Versió importada del Piwigo)', {$number_of_pages}, NULL, '{$first_upload_date}')");
	} else {
		$slug = escape(slugify($manga['name']));
		$name = escape($manga['name']);
		$description = escape($manga['comment']);
		$fansubs = array();

		//Find which fansub uploaded this (max 5 levels)
		$fansubres = query_piwigo("SELECT GROUP_CONCAT(DISTINCT u.username SEPARATOR '|') fansub_name FROM piwigo_image_category ic LEFT JOIN piwigo_images i ON ic.image_id=i.id LEFT JOIN piwigo_users u ON i.added_by=u.id WHERE category_id IN (SELECT DISTINCT IFNULL(c4.id,IFNULL(c3.id,IFNULL(c2.id,c1.id))) id FROM piwigo_categories c1 LEFT JOIN piwigo_categories c2 ON c2.id_uppercat=c1.id LEFT JOIN piwigo_categories c3 ON c3.id_uppercat=c2.id LEFT JOIN piwigo_categories c4 ON c4.id_uppercat=c3.id WHERE c1.id_uppercat=".$manga['id']." UNION SELECT ".$manga['id'].")");
		$fansub_names = explode('|', mysqli_fetch_assoc($fansubres)['fansub_name']);
		foreach ($fansub_names as $fansub_name) {
			switch ($fansub_name){
				case 'CatSub':
					$fansubs[]=1;
					break;
				case 'El Detectiu Conan':
					$fansubs[]=53;
					break;
				case 'Lluna Plena no Fansub':
					$fansubs[]=2;
					break;
				default:
					//Independent
					$fansubs[]=28;
					break;
			}
		}
		mysqli_free_result($fansubres);

		//Find the cover
		if (!empty($manga['representative_picture_id'])) {
			$resulti = query_piwigo("SELECT i.* FROM piwigo_images i WHERE i.id={$manga['representative_picture_id']}");
			if ($image = mysqli_fetch_assoc($resulti)) {
				copy_file("{$base_path_piwigo}{$image['path']}", "{$base_path_manga}manga/{$manga['id']}.jpg");
				copy_file("{$base_path_piwigo}{$image['path']}", "{$base_path_manga}featured/{$manga['id']}.jpg");
			}
			mysqli_free_result($resulti);
		}

		$total_num_chapters = 0;
		$first_upload_date = '2022-01-01 00:00:00';
		$last_upload_date = '2000-01-01 00:00:00';
		$volumes = array();
		$chapters = array();

		//Find and copy subalbums (volumes and chapters)
		$has_some_volume = FALSE;
		$resultf1 = query_piwigo("SELECT c.id, c.name, c.comment, c.id_uppercat, c.representative_picture_id, EXISTS(SELECT c2.* FROM piwigo_categories c2 WHERE c2.id_uppercat=c.id) has_subalbums FROM piwigo_categories c WHERE id_uppercat={$manga['id']} ORDER BY has_subalbums DESC, id ASC");
		while ($album = mysqli_fetch_assoc($resultf1)){
			if (!$album['has_subalbums']) {
				if (strpos(strtolower($album['name']), 'volum')!==FALSE || strpos(strtolower($album['name']), 'temporada')!==FALSE) {
					//Probably a volume with no chapters
					preg_match('/(olum|emporada|Veus en la foscor) (\d+)/', $album['name'], $matches);
					$number = count($matches)>2 ? $matches[2] : "";
					if (is_numeric($number) && $number!=0){
						echo "Found volume (id={$album['id']},num={$number}) with no chapters {$manga['name']} -> {$album['name']}\n";

						$copy_result = import_images($album['id'], $album['representative_picture_id'], $manga['id'], $album['id']);
						$number_of_pages = $copy_result['number_of_pages'];
						$ch_first_upload_date = $copy_result['first_upload_date'];
						$ch_last_upload_date = $copy_result['last_upload_date'];
						$volumes[] = array(
							'id' => $album['id'],
							'number' => $number,
							'name' => 'NULL',
							'chapters' => 0
						);
						$chapters[] = array(
							'id' => $album['id'],
							'volume_id' => $album['id'],
							'number' => 'NULL',
							'name' => "'Volum sencer'",
							'number_of_pages' => $number_of_pages,
							'upload_date' => $ch_first_upload_date
						);
						if ($ch_last_upload_date>$last_upload_date) {
							$last_upload_date = $ch_last_upload_date;
						}
						if ($ch_first_upload_date<$first_upload_date) {
							$first_upload_date = $ch_first_upload_date;
						}
						//$total_num_chapters++;
					} else {
						echo "Found EXTRA volume (id={$album['id']},num=999,name='{$album['name']}') with no chapters {$manga['name']} -> {$album['name']}\n";

						$copy_result = import_images($album['id'], $album['representative_picture_id'], $manga['id'], $album['id']);
						$number_of_pages = $copy_result['number_of_pages'];
						$ch_first_upload_date = $copy_result['first_upload_date'];
						$ch_last_upload_date = $copy_result['last_upload_date'];
						$volumes[] = array(
							'id' => $album['id'],
							'number' => 999,
							'name' => "'".escape($album['name'])."'",
							'chapters' => 0
						);
						$chapters[] = array(
							'id' => $album['id'],
							'volume_id' => $album['id'],
							'number' => 'NULL',
							'name' => "'".escape($album['name'])."'",
							'number_of_pages' => $number_of_pages,
							'upload_date' => $ch_first_upload_date
						);
						if ($ch_last_upload_date>$last_upload_date) {
							$last_upload_date = $ch_last_upload_date;
						}
						if ($ch_first_upload_date<$first_upload_date) {
							$first_upload_date = $ch_first_upload_date;
						}
						//$total_num_chapters++;
					}
				} else {
					if ($has_some_volume) {
						//Probably an extra chapter
						echo "Found EXTRA chapter (id={$album['id']},vol=NULL,num=NULL,name='{$album['name']}') {$manga['name']} -> {$album['name']}\n";

						$copy_result = import_images($album['id'], -1, $manga['id'], -1);
						$number_of_pages = $copy_result['number_of_pages'];
						$ch_first_upload_date = $copy_result['first_upload_date'];
						$ch_last_upload_date = $copy_result['last_upload_date'];
						$chapters[] = array(
							'id' => $album['id'],
							'volume_id' => 'NULL',
							'number' => 'NULL',
							'name' => "'".escape($album['name'])."'",
							'number_of_pages' => $number_of_pages,
							'upload_date' => $ch_first_upload_date
						);
						if ($ch_last_upload_date>$last_upload_date) {
							$last_upload_date = $ch_last_upload_date;
						}
						if ($ch_first_upload_date<$first_upload_date) {
							$first_upload_date = $ch_first_upload_date;
						}
						//$total_num_chapters++;
					} else {
						//Probably a chapter (single volume)

						//Find the MANGA cover since it will also be the volume one
						if (!empty($manga['representative_picture_id'])) {
							$resulti = query_piwigo("SELECT i.* FROM piwigo_images i WHERE i.id={$manga['representative_picture_id']}");
							if ($image = mysqli_fetch_assoc($resulti)) {
								copy_file("{$base_path_piwigo}{$image['path']}", "{$base_path_manga}covers/{$manga['id']}_{$manga['id']}.jpg");
							}
							mysqli_free_result($resulti);
						}
						preg_match('/tol (\d+((,|\.)\d+)?)/', $album['name'], $matchesc);
						$numberc = count($matchesc)>1 ? str_replace(',', '.', $matchesc[1]) : "";
						if (is_numeric($numberc) && $numberc!=0){
							echo "Found chapter (id={$album['id']},vol=ONE,num={$numberc}) {$manga['name']} -> {$album['name']}\n";
							$found = FALSE;
							foreach ($volumes as &$volume){
								if ($volume['id']==$manga['id']) {
									$volume['chapters']++;
									$found = TRUE;
								}
							}
							if (!$found){
								$volumes[] = array(
									'id' => $manga['id'],
									'number' => 1,
									'name' => 'NULL',
									'chapters' => 1
								);
							}

							$copy_result = import_images($album['id'], $manga['representative_picture_id'], $manga['id'], $manga['id']);
							$number_of_pages = $copy_result['number_of_pages'];
							$ch_first_upload_date = $copy_result['first_upload_date'];
							$ch_last_upload_date = $copy_result['last_upload_date'];
							$chapters[] = array(
								'id' => $album['id'],
								'volume_id' => $manga['id'],
								'number' => $numberc,
								'name' => 'NULL',
								'number_of_pages' => $number_of_pages,
								'upload_date' => $ch_first_upload_date
							);
							if ($ch_last_upload_date>$last_upload_date) {
								$last_upload_date = $ch_last_upload_date;
							}
							if ($ch_first_upload_date<$first_upload_date) {
								$first_upload_date = $ch_first_upload_date;
							}
							$total_num_chapters++;
						} else {
							echo "Found EXTRA chapter (id={$album['id']},vol=ONE,num=NULL,name='{$album['name']}') {$manga['name']} -> {$album['name']}\n";
							$found = FALSE;
							foreach ($volumes as &$volume){
								if ($volume['id']==$manga['id']) {
									$volume['chapters']++;
									$found = TRUE;
								}
							}
							if (!$found){
								$volumes[] = array(
									'id' => $manga['id'],
									'number' => 1,
									'name' => 'NULL',
									'chapters' => 0
								);
							}

							$copy_result = import_images($album['id'], $manga['representative_picture_id'], $manga['id'], $manga['id']);
							$number_of_pages = $copy_result['number_of_pages'];
							$ch_first_upload_date = $copy_result['first_upload_date'];
							$ch_last_upload_date = $copy_result['last_upload_date'];
							$chapters[] = array(
								'id' => $album['id'],
								'volume_id' => $manga['id'],
								'number' => 'NULL',
								'name' => "'".escape($album['name'])."'",
								'number_of_pages' => $number_of_pages,
								'upload_date' => $ch_first_upload_date
							);
							if ($ch_last_upload_date>$last_upload_date) {
								$last_upload_date = $ch_last_upload_date;
							}
							if ($ch_first_upload_date<$first_upload_date) {
								$first_upload_date = $ch_first_upload_date;
							}
							//$total_num_chapters++;
						}
					}
				}
			} else {
				//Probably a volume
				$has_some_volume = TRUE;
				preg_match('/(olum|emporada|Veus en la foscor) (\d+)/', $album['name'], $matches);
				$number = count($matches)>2 ? $matches[2] : "";
				if (is_numeric($number) && $number!=0){
					echo "Found volume (id={$album['id']},num={$number}) {$manga['name']} -> {$album['name']}\n";
				} else {
					echo "Found EXTRA volume (id={$album['id']},num=999,name='{$album['name']}') {$manga['name']} -> {$album['name']}\n";
				}
				$num_chapters=0;
				//Find subalbums (chapters)
				$resultf2 = query_piwigo("SELECT c.id, c.name, c.comment, c.id_uppercat, c.representative_picture_id, EXISTS(SELECT c2.* FROM piwigo_categories c2 WHERE c2.id_uppercat=c.id) has_subalbums FROM piwigo_categories c WHERE id_uppercat={$album['id']} ORDER BY id ASC");
				while ($subalbum = mysqli_fetch_assoc($resultf2)){
					if (!$subalbum['has_subalbums']) {
						//Probably a chapter
						preg_match('/tol (\d+((,|\.)\d+)?)/', $subalbum['name'], $matchesc);
						$numberc = count($matchesc)>1 ? str_replace(',', '.', $matchesc[1]) : "";
						if (is_numeric($numberc) && $numberc!=0){
							echo "Found chapter (id={$subalbum['id']},vol={$album['id']},num={$numberc}) {$manga['name']} -> {$album['name']} -> {$subalbum['name']}\n";

							$copy_result = import_images($subalbum['id'], $album['representative_picture_id'], $manga['id'], $album['id']);
							$number_of_pages = $copy_result['number_of_pages'];
							$ch_first_upload_date = $copy_result['first_upload_date'];
							$ch_last_upload_date = $copy_result['last_upload_date'];
							$chapters[] = array(
								'id' => $subalbum['id'],
								'volume_id' => $album['id'],
								'number' => $numberc,
								'name' => 'NULL',
								'number_of_pages' => $number_of_pages,
								'upload_date' => $ch_first_upload_date
							);
							if ($ch_last_upload_date>$last_upload_date) {
								$last_upload_date = $ch_last_upload_date;
							}
							if ($ch_first_upload_date<$first_upload_date) {
								$first_upload_date = $ch_first_upload_date;
							}
							$num_chapters++;
							$total_num_chapters++;
						} else {
							echo "Found EXTRA chapter (id={$subalbum['id']},vol={$album['id']},num=NULL,name='{$subalbum['name']}') {$manga['name']} -> {$album['name']} -> {$subalbum['name']}\n";

							$copy_result = import_images($subalbum['id'], $album['representative_picture_id'], $manga['id'], $album['id']);
							$number_of_pages = $copy_result['number_of_pages'];
							$ch_first_upload_date = $copy_result['first_upload_date'];
							$ch_last_upload_date = $copy_result['last_upload_date'];
							$chapters[] = array(
								'id' => $subalbum['id'],
								'volume_id' => $album['id'],
								'number' => 'NULL',
								'name' => "'".escape($subalbum['name'])."'",
								'number_of_pages' => $number_of_pages,
								'upload_date' => $ch_first_upload_date
							);
							if ($ch_last_upload_date>$last_upload_date) {
								$last_upload_date = $ch_last_upload_date;
							}
							if ($ch_first_upload_date<$first_upload_date) {
								$first_upload_date = $ch_first_upload_date;
							}
							//$num_chapters++; //Els no numerats no sumen
							//$total_num_chapters++;
						}
					} else {
						die("CRITICAL ERROR: Album '{$subalbum['id']} has a 3rd level subalbum'");
					}
				}
				mysqli_free_result($resultf2);

				//Repeat logic to set chapter numbers
				if (is_numeric($number) && $number!=0){
					$volumes[] = array(
						'id' => $album['id'],
						'number' => $number,
						'name' => 'NULL',
						'chapters' => $num_chapters
					);
				} else {
					$volumes[] = array(
						'id' => $album['id'],
						'number' => 999,
						'name' => "'".escape($album['name'])."'",
						'chapters' => $num_chapters
					);
				}

				//Find the VOLUME cover
				if (!empty($album['representative_picture_id'])) {
					$resulti = query_piwigo("SELECT i.* FROM piwigo_images i WHERE i.id={$album['representative_picture_id']}");
					if ($image = mysqli_fetch_assoc($resulti)) {
						copy_file("{$base_path_piwigo}{$image['path']}", "{$base_path_manga}covers/{$manga['id']}_{$album['id']}.jpg");
					}
					mysqli_free_result($resulti);
				}

				//Find orphaned images
				$album_orphaned_images = 0;
				$resulti = query_piwigo("SELECT i.* FROM piwigo_image_category ic LEFT JOIN piwigo_images i ON ic.image_id=i.id WHERE ic.category_id={$album['id']}");
				while ($image = mysqli_fetch_assoc($resulti)) {
					$album_orphaned_images++;
				}
				mysqli_free_result($resulti);

				if ($album_orphaned_images>1) {
					echo "NOTICE: Volume {$album['id']} has $album_orphaned_images orphaned images, they will be lost!\n";
				}
			}
		}
		mysqli_free_result($resultf1);

		//Find orphaned images
		$album_orphaned_images = 0;
		$resulti = query_piwigo("SELECT i.* FROM piwigo_image_category ic LEFT JOIN piwigo_images i ON ic.image_id=i.id WHERE ic.category_id={$manga['id']}");
		while ($image = mysqli_fetch_assoc($resulti)) {
			$album_orphaned_images++;
		}
		mysqli_free_result($resulti);

		if ($album_orphaned_images>1) {
			echo "NOTICE: Manga {$manga['id']} has $album_orphaned_images orphaned images, they will be lost!\n";
		}

		query("INSERT INTO manga (id, slug, name, alternate_names, keywords, type, publish_date, author, rating, chapters, synopsis, myanimelist_id, tadaima_id, score, reader_type, show_volumes, show_expanded_volumes, show_chapter_numbers, show_unavailable_chapters, has_licensed_parts, order_type, created, created_by, updated, updated_by) VALUES ({$manga['id']}, '{$slug}', '{$name}',NULL,NULL,'serialized',NULL,NULL,'TP',{$total_num_chapters},'{$description}',NULL,NULL,NULL,'paged',1,1,1,1,0,0,'{$first_upload_date}','PiwigoImport','{$last_upload_date}','PiwigoImport')");
		query("INSERT INTO manga_version (id, manga_id, status, chapters_missing, created, created_by, updated, updated_by, files_updated, files_updated_by, is_featurable, is_always_featured) VALUES ({$manga['id']}, {$manga['id']}, 1, 0, '{$first_upload_date}', 'PiwigoImport', '{$last_upload_date}', 'PiwigoImport', '{$last_upload_date}', 'PiwigoImport', 1, 0)");
		foreach ($fansubs as $fansub_id) {
			query("INSERT INTO rel_manga_version_fansub (manga_version_id, fansub_id, downloads_url) VALUES ({$manga['id']}, {$fansub_id}, NULL)");
		}
		foreach ($volumes as $volume) {
			query("INSERT INTO volume (id, manga_id, number, name, chapters, myanimelist_id) VALUES ({$volume['id']}, {$manga['id']}, {$volume['number']}, {$volume['name']}, {$volume['chapters']}, NULL)");
		}
		foreach ($chapters as $chapter) {
			query("INSERT INTO chapter (id, manga_id, volume_id, number, name) VALUES ({$chapter['id']}, {$manga['id']}, {$chapter['volume_id']}, {$chapter['number']}, {$chapter['name']})");
			if ($chapter['name']!="''" && $chapter['name']!='NULL'){
				query("INSERT INTO chapter_title (manga_version_id, chapter_id, title) VALUES ({$manga['id']}, {$chapter['id']}, {$chapter['name']})");
			}
			query("INSERT INTO file (id, manga_version_id, chapter_id, extra_name, original_filename, number_of_pages, comments, created) VALUES ({$chapter['id']}, {$manga['id']}, {$chapter['id']}, NULL, '(Versió importada del Piwigo)', {$chapter['number_of_pages']}, NULL, '{$chapter['upload_date']}')");
		}
	}
}
mysqli_free_result($resultm);
mysqli_close($db_connection_manga);
?>
