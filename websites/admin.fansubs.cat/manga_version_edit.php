<?php
$header_title="Edició de versions de manga - Manga";
$page="manga";
include("header.inc.php");
require_once("common.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca id");
		}
		if (!empty($_POST['fansub_1']) && is_numeric($_POST['fansub_1'])) {
			$data['fansub_1']=escape($_POST['fansub_1']);
		} else {
			crash("Dades invàlides: manca fansub_1");
		}
		if (!empty($_POST['manga_id']) && is_numeric($_POST['manga_id'])) {
			$data['manga_id']=escape($_POST['manga_id']);
		} else {
			crash("Dades invàlides: manca manga_id");
		}
		if (!empty($_POST['fansub_2']) && is_numeric($_POST['fansub_2'])) {
			$data['fansub_2']=escape($_POST['fansub_2']);
		} else {
			$data['fansub_2']=NULL;
		}
		if (!empty($_POST['fansub_3']) && is_numeric($_POST['fansub_3'])) {
			$data['fansub_3']=escape($_POST['fansub_3']);
		} else {
			$data['fansub_3']=NULL;
		}
		if (!empty($_POST['downloads_url_1'])) {
			$data['downloads_url_1']="'".escape($_POST['downloads_url_1'])."'";
		} else {
			$data['downloads_url_1']="NULL";
		}
		if (!empty($_POST['downloads_url_2'])) {
			$data['downloads_url_2']="'".escape($_POST['downloads_url_2'])."'";
		} else {
			$data['downloads_url_2']="NULL";
		}
		if (!empty($_POST['downloads_url_3'])) {
			$data['downloads_url_3']="'".escape($_POST['downloads_url_3'])."'";
		} else {
			$data['downloads_url_3']="NULL";
		}
		if (!empty($_POST['status']) && is_numeric($_POST['status'])) {
			$data['status']=escape($_POST['status']);
		} else {
			crash("Dades invàlides: manca status");
		}
		if (!empty($_POST['is_featurable'])){
			$data['is_featurable']=1;
		} else {
			$data['is_featurable']=0;
		}
		if (!empty($_POST['is_always_featured'])){
			$data['is_always_featured']=1;
		} else {
			$data['is_always_featured']=0;
		}
		if (!empty($_POST['hidden'])){
			$data['hidden']=1;
		} else {
			$data['hidden']=0;
		}
		if (!empty($_POST['show_volumes'])){
			$data['show_volumes']=1;
		} else {
			$data['show_volumes']=0;
		}
		if (!empty($_POST['show_expanded_volumes'])){
			$data['show_expanded_volumes']=1;
		} else {
			$data['show_expanded_volumes']=0;
		}
		if (!empty($_POST['show_chapter_numbers'])){
			$data['show_chapter_numbers']=1;
		} else {
			$data['show_chapter_numbers']=0;
		}
		if (!empty($_POST['show_unavailable_chapters'])){
			$data['show_unavailable_chapters']=1;
		} else {
			$data['show_unavailable_chapters']=0;
		}
		if (!empty($_POST['show_expanded_extras'])){
			$data['show_expanded_extras']=1;
		} else {
			$data['show_expanded_extras']=0;
		}
		if (!empty($_POST['order_type'])){
			$data['order_type']=escape($_POST['order_type']);
		} else {
			$data['order_type']=0;
		}

		$volumes=array();

		$resultv = query("SELECT v.* FROM volume v WHERE v.manga_id=".$data['manga_id']);

		while ($rowv = mysqli_fetch_assoc($resultv)) {
			array_push($volumes, $rowv);
		}

		mysqli_free_result($resultv);

		$files=array();
		$chapters=array();

		$resultc = query("SELECT c.* FROM chapter c WHERE c.manga_id=".$data['manga_id']);

		$data['chapters_missing']=0; //By default.. will be calculated depending on files
		
		while ($rowc = mysqli_fetch_assoc($resultc)) {
			$chapter_id=$rowc['id'];

			$chapter = array();
			$chapter['id'] = $chapter_id;

			if (!empty($_POST['form-files-list-'.$chapter_id.'-title'])) {
				$chapter['title'] = "'".escape($_POST['form-files-list-'.$chapter_id.'-title'])."'";
			} else {
				$chapter['title'] = "NULL";
			}
			array_push($chapters, $chapter);
			
			$i=1;
			while (!empty($_POST['form-files-list-'.$chapter_id.'-id-'.$i])) {
				$file = array();
				if (is_numeric($_POST['form-files-list-'.$chapter_id.'-id-'.$i])) {
					$file['id']=escape($_POST['form-files-list-'.$chapter_id.'-id-'.$i]);
				} else {
					crash("Dades invàlides: manca id del fitxer");
				}
				if (is_uploaded_file($_FILES['form-files-list-'.$chapter_id.'-file-'.$i]['tmp_name'])) {
					$file['original_filename']="'".escape($_FILES['form-files-list-'.$chapter_id.'-file-'.$i]["name"])."'";
					$file['original_filename_unescaped']=$_FILES['form-files-list-'.$chapter_id.'-file-'.$i]['name'];
					$file['temporary_filename']=$_FILES['form-files-list-'.$chapter_id.'-file-'.$i]['tmp_name'];
				} else {
					$file['original_filename']='NULL';
				}
				if (!empty($_POST['form-files-list-'.$chapter_id.'-number_of_pages-'.$i])) {
					$file['number_of_pages']=escape($_POST['form-files-list-'.$chapter_id.'-number_of_pages-'.$i]);
				} else {
					$file['number_of_pages']=0;
				}
				if (!empty($_POST['form-files-list-'.$chapter_id.'-variant_name-'.$i])) {
					$file['variant_name']="'".escape($_POST['form-files-list-'.$chapter_id.'-variant_name-'.$i])."'";
				} else {
					$file['variant_name']="NULL";
				}
				if (!empty($_POST['form-files-list-'.$chapter_id.'-comments-'.$i])) {
					$file['comments']="'".escape($_POST['form-files-list-'.$chapter_id.'-comments-'.$i])."'";
				} else {
					$file['comments']="NULL";
				}
				$file['chapter_id']=$chapter_id;

				if ($file['original_filename']=="NULL" && !empty($_POST['form-files-list-'.$chapter_id.'-lost-'.$i])) {
					$data['chapters_missing']=1;
				}

				if ($file['id']!=-1 || $file['original_filename']!="NULL" || !empty($_POST['form-files-list-'.$chapter_id.'-lost-'.$i])) {
					array_push($files, $file);
				}
				$i++;
			}
		}
		if (!empty($files)) {
			$data['hidden']=0;
		}
		mysqli_free_result($resultc);

		$extras=array();
		$i=1;
		while (!empty($_POST['form-extras-list-id-'.$i])) {
			$extra = array();
			if (is_numeric($_POST['form-extras-list-id-'.$i])) {
				$extra['id']=escape($_POST['form-extras-list-id-'.$i]);
			} else {
				crash("Dades invàlides: manca id de l'extra");
			}
			if (!empty($_POST['form-extras-list-name-'.$i])) {
				$extra['name']=escape($_POST['form-extras-list-name-'.$i]);
			} else {
				crash("Dades invàlides: manca name de l'extra");
			}
			if (is_uploaded_file($_FILES['form-extras-list-file-'.$i]['tmp_name'])) {
				$extra['original_filename']=escape($_FILES['form-extras-list-file-'.$i]["name"]);
				$extra['original_filename_unescaped']=$_FILES['form-extras-list-file-'.$i]['name'];
				$extra['temporary_filename']=$_FILES['form-extras-list-file-'.$i]['tmp_name'];
			} else if ($extra['id']==-1) {
				crash("Dades invàlides: manca fitxer de l'extra");
			} else {
				$extra['original_filename']=NULL;
			}
			if (!empty($_POST['form-extras-list-number_of_pages-'.$i])) {
				$extra['number_of_pages']=escape($_POST['form-extras-list-number_of_pages-'.$i]);
			} else {
				$extra['number_of_pages']=0;
			}
			if (!empty($_POST['form-extras-list-comments-'.$i])) {
				$extra['comments']="'".escape($_POST['form-extras-list-comments-'.$i])."'";
			} else {
				$extra['comments']="NULL";
			}
			array_push($extras, $extra);
			$i++;
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-manga-version", "S'ha actualitzat la versió del manga (id. de manga: ".$data['manga_id'].") (id. de versió: ".$data['id'].")");
			query("UPDATE manga_version SET status=".$data['status'].",chapters_missing=".$data['chapters_missing'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."',is_featurable=".$data['is_featurable'].",is_always_featured=".$data['is_always_featured'].",show_volumes=".$data['show_volumes'].",show_expanded_volumes=".$data['show_expanded_volumes'].",show_chapter_numbers=".$data['show_chapter_numbers'].",show_unavailable_chapters=".$data['show_unavailable_chapters'].",show_expanded_extras=".$data['show_expanded_extras'].",order_type=".$data['order_type'].",hidden=".$data['hidden']." WHERE id=".$data['id']);
			query("DELETE FROM rel_manga_version_fansub WHERE manga_version_id=".$data['id']);
			query("DELETE FROM chapter_title WHERE manga_version_id=".$data['id']);
			if ($data['fansub_1']!=NULL) {
				query("INSERT INTO rel_manga_version_fansub (manga_version_id,fansub_id,downloads_url) VALUES (".$data['id'].",".$data['fansub_1'].",".$data['downloads_url_1'].")");
			}
			if ($data['fansub_2']!=NULL) {
				query("INSERT INTO rel_manga_version_fansub (manga_version_id,fansub_id,downloads_url) VALUES (".$data['id'].",".$data['fansub_2'].",".$data['downloads_url_2'].")");
			}
			if ($data['fansub_3']!=NULL) {
				query("INSERT INTO rel_manga_version_fansub (manga_version_id,fansub_id,downloads_url) VALUES (".$data['id'].",".$data['fansub_3'].",".$data['downloads_url_3'].")");
			}

			foreach ($chapters as $chapter) {
				if ($chapter['title']!="NULL") {
					query("INSERT INTO chapter_title (manga_version_id,chapter_id,title) VALUES (".$data['id'].",".$chapter['id'].",".$chapter['title'].")");
				}
			}

			$ids=array();
			foreach ($files as $file) {
				if ($file['id']!=-1) {
					array_push($ids,$file['id']);
				}
			}
			//Views will be removed too because their FK is set to cascade
			query("DELETE FROM file WHERE manga_version_id=".$data['id']." AND chapter_id IS NOT NULL AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			//We do not count removing files as updating them, only insertions and real updates
			foreach ($files as $file) {
				if ($file['id']==-1) {
					query("INSERT INTO file (manga_version_id,chapter_id,variant_name,extra_name,original_filename,number_of_pages,comments,created) VALUES (".$data['id'].",".$file['chapter_id'].",".$file['variant_name'].",NULL,".$file['original_filename'].",".$file['number_of_pages'].",".$file['comments'].",CURRENT_TIMESTAMP)");
					if ($file['original_filename']!='NULL') {
						decompress_manga_file(mysqli_insert_id($db_connection), $file['temporary_filename'], $file['original_filename_unescaped']);
					}
					if (empty($_POST['do_not_count_as_update'])) {
						query("UPDATE manga_version SET files_updated=CURRENT_TIMESTAMP,files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				} else {
					$resultcr = query("SELECT * FROM file WHERE id=".$file['id']);
					if ($current_file = mysqli_fetch_assoc($resultcr)) {
						query("UPDATE file SET ".($file['original_filename']!='NULL' ? "original_filename=".$file['original_filename'].",number_of_pages=".$file['number_of_pages']."," : "")."variant_name=".$file['variant_name'].",comments=".$file['comments']." WHERE id=".$file['id']);
						if (empty($_POST['do_not_count_as_update']) && (empty($current_file['original_filename']) ? "NULL" : "'".escape($current_file['original_filename'])."'")!=$file['original_filename']) {
							query("UPDATE manga_version SET files_updated=CURRENT_TIMESTAMP,files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
							if (empty($current_file['original_filename']) && $file['original_filename']!='NULL') {
								query("UPDATE file SET created=CURRENT_TIMESTAMP WHERE id=".$file['id']);
							}
						}
					}
					mysqli_free_result($resultcr);
					if ($file['original_filename']!='NULL') {
						decompress_manga_file($file['id'], $file['temporary_filename'], $file['original_filename_unescaped']);
					}
				}
			}

			$ids=array();
			foreach ($extras as $extra) {
				if ($extra['id']!=-1) {
					array_push($ids,$extra['id']);
				}
			}
			//Views will be removed too because their FK is set to cascade
			query("DELETE FROM file WHERE manga_version_id=".$data['id']." AND chapter_id IS NULL AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($extras as $extra) {
				if ($extra['id']==-1) {
					query("INSERT INTO file (manga_version_id,chapter_id,variant_name,extra_name,original_filename,number_of_pages,comments,created) VALUES (".$data['id'].",NULL,NULL,'".$extra['name']."','".$extra['original_filename']."',".$extra['number_of_pages'].",".$extra['comments'].",CURRENT_TIMESTAMP)");
					decompress_manga_file(mysqli_insert_id($db_connection), $extra['temporary_filename'], $extra['original_filename_unescaped']);
					if (empty($_POST['do_not_count_as_update'])) {
						query("UPDATE manga_version SET files_updated=CURRENT_TIMESTAMP,files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
					}
				} else {
					$resultcr = query("SELECT * FROM file WHERE id=".$extra['id']);
					if ($current_extra = mysqli_fetch_assoc($resultcr)) {
						query("UPDATE file SET extra_name='".$extra['name']."',".($extra['original_filename']!=NULL ? "original_filename='".$extra['original_filename']."'," : "")."number_of_pages=".$extra['number_of_pages'].",comments=".$extra['comments']." WHERE id=".$extra['id']);
						if (empty($_POST['do_not_count_as_update']) && (empty($current_extra['original_filename']) ? NULL : escape($current_extra['original_filename']))!=$extra['original_filename']) {
							query("UPDATE manga_version SET files_updated=CURRENT_TIMESTAMP,files_updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
						}
					}
					mysqli_free_result($resultcr);
					if ($extra['original_filename']!=NULL) {
						decompress_manga_file($extra['id'], $extra['temporary_filename'], $extra['original_filename_unescaped']);
					}
				}
			}

			foreach ($volumes as $volume) {
				if (is_uploaded_file($_FILES['volume_cover_'.$volume['id']]['tmp_name'])) {
					move_uploaded_file($_FILES['volume_cover_'.$volume['id']]['tmp_name'], "../manga.fansubs.cat/images/covers/".$data['id']."_".$volume['id'].".jpg");
				}
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}
		else {
			log_action("create-manga-version", "S'ha creat una versió del manga (id. de manga: ".$data['manga_id'].")");
			query("INSERT INTO manga_version (manga_id,status,chapters_missing,created,created_by,updated,updated_by,files_updated,files_updated_by,is_featurable,is_always_featured,show_volumes,show_expanded_volumes,show_chapter_numbers,show_unavailable_chapters,show_expanded_extras,order_type,hidden) VALUES (".$data['manga_id'].",".$data['status'].",".$data['chapters_missing'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',".$data['is_featurable'].",".$data['is_always_featured'].",".$data['show_volumes'].",".$data['show_expanded_volumes'].",".$data['show_chapter_numbers'].",".$data['show_unavailable_chapters'].",".$data['show_expanded_extras'].",".$data['order_type'].",".$data['hidden'].")");
			$inserted_id=mysqli_insert_id($db_connection);
			if ($data['fansub_1']!=NULL) {
				query("INSERT INTO rel_manga_version_fansub (manga_version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_1'].",".$data['downloads_url_1'].")");
			}
			if ($data['fansub_2']!=NULL) {
				query("INSERT INTO rel_manga_version_fansub (manga_version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_2'].",".$data['downloads_url_1'].")");
			}
			if ($data['fansub_3']!=NULL) {
				query("INSERT INTO rel_manga_version_fansub (manga_version_id,fansub_id,downloads_url) VALUES (".$inserted_id.",".$data['fansub_3'].",".$data['downloads_url_1'].")");
			}
			foreach ($chapters as $chapter) {
				if ($chapter['title']!="NULL") {
					query("INSERT INTO chapter_title (manga_version_id,chapter_id,title) VALUES (".$inserted_id.",".$chapter['id'].",".$chapter['title'].")");
				}
			}
			foreach ($files as $file) {
				query("INSERT INTO file (manga_version_id,chapter_id,variant_name,extra_name,original_filename,number_of_pages,comments,created) VALUES (".$inserted_id.",".$file['chapter_id'].",".$file['variant_name'].",NULL,".$file['original_filename'].",".$file['number_of_pages'].",".$file['comments'].",CURRENT_TIMESTAMP)");
				if ($file['original_filename']!='NULL') {
					decompress_manga_file(mysqli_insert_id($db_connection), $file['temporary_filename'], $file['original_filename_unescaped']);
				}
			}
			foreach ($extras as $extra) {
				query("INSERT INTO file (manga_version_id,chapter_id,variant_name,extra_name,original_filename,number_of_pages,comments,created) VALUES (".$inserted_id.",NULL,NULL,'".$extra['name']."','".$extra['original_filename']."',".$extra['number_of_pages'].",".$extra['comments'].",CURRENT_TIMESTAMP)");
				decompress_manga_file(mysqli_insert_id($db_connection), $extra['temporary_filename'], $extra['original_filename_unescaped']);
			}

			foreach ($volumes as $volume) {
				if (is_uploaded_file($_FILES['volume_cover_'.$volume['id']]['tmp_name'])) {
					move_uploaded_file($_FILES['volume_cover_'.$volume['id']]['tmp_name'], "../manga.fansubs.cat/images/covers/".$inserted_id."_".$volume['id'].".jpg");
				}
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}

		header("Location: manga_version_list.php");
		die();
	}

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT v.* FROM manga_version v WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Version not found');
		mysqli_free_result($result);

		$resultm = query("SELECT m.* FROM manga m WHERE id=".$row['manga_id']);
		$manga = mysqli_fetch_assoc($resultm) or crash('Manga not found');
		mysqli_free_result($resultm);

		$resultf = query("SELECT fansub_id, downloads_url FROM rel_manga_version_fansub vf WHERE vf.manga_version_id=".$row['id']);
		$fansubs = array();
		while ($rowf = mysqli_fetch_assoc($resultf)) {
			array_push($fansubs, array($rowf['fansub_id'], $rowf['downloads_url']));
		}
		mysqli_free_result($resultf);

		$resultv = query("SELECT v.* FROM volume v LEFT JOIN manga m ON v.manga_id=m.id WHERE v.manga_id=".$row['manga_id']." ORDER BY v.number ASC");
		$volumes = array();
		while ($rowv = mysqli_fetch_assoc($resultv)) {
			array_push($volumes, $rowv);
		}
		mysqli_free_result($resultv);

		$resultc = query("SELECT c.*, ct.title, v.number volume_number FROM chapter c LEFT JOIN volume v ON c.volume_id=v.id LEFT JOIN chapter_title ct ON c.id=ct.chapter_id AND ct.manga_version_id=".escape($_GET['id'])." WHERE c.manga_id=".$row['manga_id']." ORDER BY v.number IS NULL ASC, v.number ASC, c.number IS NULL ASC, c.number ASC, c.name ASC");
		$chapters = array();
		while ($rowc = mysqli_fetch_assoc($resultc)) {
			array_push($chapters, $rowc);
		}
		mysqli_free_result($resultc);
	} else if (!empty($_GET['manga_id']) && is_numeric($_GET['manga_id'])) {
		$row = array();

		$resultm = query("SELECT m.* FROM manga m WHERE id=".escape($_GET['manga_id']));
		$manga = mysqli_fetch_assoc($resultm) or crash('Manga not found');
		mysqli_free_result($resultm);

		if ($manga['type']=='oneshot') {
			$row['hidden']=0;
			$row['show_volumes']=1;
			$row['show_expanded_volumes']=1;
			$row['show_expanded_extras']=1;
			$row['show_chapter_numbers']=0;
			$row['show_unavailable_chapters']=1;
			$row['order_type']=0;
		} else {
			$row['hidden']=0;
			$row['show_volumes']=1;
			$row['show_expanded_volumes']=1;
			$row['show_expanded_extras']=1;
			$row['show_chapter_numbers']=1;
			$row['show_unavailable_chapters']=1;
			$row['order_type']=0;
		}

		$fansubs = array();

		$resultv = query("SELECT v.* FROM volume v LEFT JOIN manga m ON v.manga_id=m.id WHERE v.manga_id=".escape($_GET['manga_id'])." ORDER BY v.number ASC");
		$volumes = array();
		while ($rowv = mysqli_fetch_assoc($resultv)) {
			array_push($volumes, $rowv);
		}
		mysqli_free_result($resultv);

		$resultc = query("SELECT c.*, NULL title, v.number volume_number FROM chapter c LEFT JOIN volume v ON c.volume_id=v.id WHERE c.manga_id=".escape($_GET['manga_id'])." ORDER BY v.number IS NULL ASC, v.number ASC, c.number IS NULL ASC, c.number ASC, c.name ASC");
		$chapters = array();
		while ($rowc = mysqli_fetch_assoc($resultc)) {
			array_push($chapters, $rowc);
		}
		mysqli_free_result($resultc);
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita la versió" : "Afegeix una versió"; ?></h4>
					<hr>
					<form method="post" action="manga_version_edit.php" enctype="multipart/form-data" onsubmit="return checkCoverListImages()">
						<div class="form-group">
							<label for="form-manga" class="mandatory">Manga</label>
							<div id="form-manga" class="font-weight-bold form-control"><?php echo htmlspecialchars($manga['name']); ?></div>
							<input name="manga_id" type="hidden" value="<?php echo $manga['id']; ?>"/>
							<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
						</div>
						<div class="row align-items-end">
							<div class="col-sm">
								<div class="form-group">
									<label for="form-fansub-1" class="mandatory">Fansub</label>
									<select name="fansub_1" class="form-control" id="form-fansub-1" required>
										<option value="">- Selecciona un fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>0 && $fansubs[0][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-fansub-2">Fansub 2 <small class="text-muted">(en cas que sigui una col·laboració)</small></label>
									<select name="fansub_2" class="form-control" id="form-fansub-2">
										<option value="">- Cap més fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>1 && $fansubs[1][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-fansub-3">Fansub 3 <small class="text-muted">(en cas que sigui una col·laboració)</small></label>
									<select name="fansub_3" class="form-control" id="form-fansub-3">
										<option value="">- Cap més fansub -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.status DESC, f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
										<option value="<?php echo $frow['id']; ?>" <?php echo (count($fansubs)>2 && $fansubs[2][0]==$frow['id']) ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="form-group">
									<label for="form-downloads_url_1">Enllaç de baixada dels fitxers originals 1<br /><small class="text-muted">(o fitxa del fansub; separa'ls amb un punt i coma, si cal)</small></label>
									<input id="form-downloads_url_1" name="downloads_url_1" type="url" class="form-control" value="<?php echo (count($fansubs)>0 ? htmlspecialchars($fansubs[0][1]) : ''); ?>" maxlength="200"/>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-downloads_url_2">Enllaç de baixada dels fitxers originals 2<br /><small class="text-muted">(o fitxa del fansub; separa'ls amb un punt i coma, si cal)</small></label>
									<input id="form-downloads_url_2" name="downloads_url_2" type="url" class="form-control" value="<?php echo (count($fansubs)>1 ? htmlspecialchars($fansubs[1][1]) : ''); ?>" maxlength="200"/>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-downloads_url_3">Enllaç de baixada dels fitxers originals 3<br /><small class="text-muted">(o fitxa del fansub; separa'ls amb un punt i coma, si cal)</small></label>
									<input id="form-downloads_url_3" name="downloads_url_3" type="url" class="form-control" value="<?php echo (count($fansubs)>2 ? htmlspecialchars($fansubs[2][1]) : ''); ?>" maxlength="200"/>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-8">
								<div class="form-group">
									<label for="form-status" class="mandatory">Estat</label>
									<select class="form-control" name="status" id="form-status" required>
										<option value="">- Selecciona un estat -</option>
										<option value="1"<?php echo $row['status']==1 ? " selected" : ""; ?>>Completat</option>
										<option value="2"<?php echo $row['status']==2 ? " selected" : ""; ?>>En procés</option>
										<option value="3"<?php echo $row['status']==3 ? " selected" : ""; ?>>Parcialment completat (algun volum completat)</option>
										<option value="4"<?php echo $row['status']==4 ? " selected" : ""; ?>>Abandonat</option>
										<option value="5"<?php echo $row['status']==5 ? " selected" : ""; ?>>Cancel·lat</option>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-featurable_check">Recomanacions</label>
									<div id="form-featurable_check" class="row pl-3 pr-3">
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="is_featurable" id="form-is_featurable" value="1"<?php echo $row['is_featurable']==1? " checked" : ""; ?>>
											<label class="form-check-label" for="form-is_featurable">Té qualitat per a ser recomanada</label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="is_always_featured" id="form-is_always_featured" value="1"<?php echo $row['is_always_featured']==1? " checked" : ""; ?>>
											<label class="form-check-label" for="form-is_always_featured">Mostra-la sempre com a recomanada</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-volume-list">Portades dels volums <small class="text-muted">(JPEG, ~156x220, ≤300x400, ≤100 KiB)</small></label>
							<div class="row flex" id="form-volume-list">
<?php
	foreach ($volumes as $volume) {
?>
								<div class="col-sm-2 text-center pr-1 pl-1">
										<label><?php echo "Volum ".$volume['number'].(!empty($volume['name']) ? " (".$volume['name'].")" : ""); ?>:</label>
<?php
		$file_exists = !empty($row['id']) && file_exists('../manga.fansubs.cat/images/covers/'.$row['id'].'_'.$volume['id'].'.jpg');
?>
										<img id="form-volume_cover_<?php echo $volume['id']; ?>_preview" style="width: 128px; height: 180px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px; margin-bottom: 0.5em;"<?php echo $file_exists ? ' src="https://manga.fansubs.cat/images/covers/'.$row['id'].'_'.$volume['id'].'.jpg" data-original="https://manga.fansubs.cat/images/covers/'.$row['id'].'_'.$volume['id'].'.jpg"' : ''; ?> alt=""><br />
										<label for="form-volume_cover_<?php echo $volume['id']; ?>" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'info' ; ?>"><span class="fa fa-upload pr-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
										<input id="form-volume_cover_<?php echo $volume['id']; ?>" name="volume_cover_<?php echo $volume['id']; ?>" type="file" class="d-none" accept="image/jpeg" onchange="checkImageUpload(this, 102400, 'form-volume_cover_<?php echo $volume['id']; ?>_preview');"/>
								</div>
<?php
	}
?>
							</div>
						</div>
						<div class="form-group">
							<label for="form-chapter-list">Capítols, variants i fitxers</label>
							<div class="container" id="form-chapter-list">
<?php
	if ($row['show_chapter_numbers']==0 && $row['order_type']!=0) {
?>
								<div class="alert alert-warning">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>Aquest manga <b>NO</b> mostra els números de capítols a la fitxa pública. Assegura't d'afegir-los allà on sigui necessari.<br /><span class="fa fa-exclamation-triangle mr-2"></span>L'ordenació dels capítols a la fitxa pública mostra els capítols normals i els especials junts, per ordre alfabètic <?php echo $row['order_type']==1 ? 'estricte' : 'natural'; ?>, assegura't que n'introdueixes bé els títols (revisa-ho a la fitxa pública en acabar).</div>
								</div>
<?php
	} else if ($row['show_chapter_numbers']==0) {
?>
								<div class="alert alert-warning">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>Aquest manga <b>NO</b> mostra els números de capítols a la fitxa pública. Assegura't d'afegir-los allà on sigui necessari.</div>
								</div>
<?php
	} else if ($row['order_type']!=0) {
?>
								<div class="alert alert-warning">
									<div><span class="fa fa-exclamation-triangle mr-2"></span>L'ordenació dels capítols a la fitxa pública mostra els capítols normals i els especials junts, per ordre alfabètic <?php echo $row['order_type']==1 ? 'estricte' : 'natural'; ?>, assegura't que n'introdueixes bé els títols (revisa-ho a la fitxa pública en acabar).</div>
								</div>
<?php
	}
	for ($i=0;$i<count($chapters);$i++) {
		$chapter_name='';
		if (!empty($chapters[$i]['volume_number'])) {
			$chapter_name.='Volum '.$chapters[$i]['volume_number'].' - ';
		} else {
			$chapter_name.='Altres - ';
		}
		if (!empty($chapters[$i]['number'])) {
			if (!empty($chapters[$i]['name'])) {
				$chapter_name.='Capítol '.floatval($chapters[$i]['number']).' <small class="text-muted">(Títol intern: '.htmlspecialchars($chapters[$i]['name']).')</small>';
			} else {
				$chapter_name.='Capítol '.floatval($chapters[$i]['number']);
			}
		} else {
			$chapter_name.=$chapters[$i]['name'].' <small class="text-muted">(Aquest títol NO és intern: es mostrarà si no introdueixes cap títol!)</small>';
		}

		if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
			$resultf = query("SELECT f.* FROM file f WHERE f.manga_version_id=".escape($_GET['id'])." AND f.chapter_id=".$chapters[$i]['id']." ORDER BY f.id ASC");
			$files = array();
			while ($rowf = mysqli_fetch_assoc($resultf)) {
				array_push($files, $rowf);
			}
			mysqli_free_result($resultf);
		} else {
			$files=array();
		}
?>
								<div class="form-group">
									<label for="form-files-list-<?php echo $chapters[$i]['id']; ?>-title"><span class="fa fa-caret-square-right pr-2 text-primary"></span><?php echo $chapter_name; ?></label>
									<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-title" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-title" type="text" class="form-control" value="<?php echo htmlspecialchars($chapters[$i]['title']); ?>" maxlength="200" placeholder="(Sense títol)"/>
									<div class="container" id="form-files-list-<?php echo $chapters[$i]['id']; ?>">
										<div class="row mb-3">
											<div class="w-100 column">
												<table class="table table-bordered table-hover table-sm" id="files-list-table-<?php echo $chapters[$i]['id']; ?>" data-count="<?php echo max(count($files),1); ?>">
													<thead>
														<tr>
															<th style="width: 12%;"><span class="mandatory">Variant</span> <span class="fa fa-question-circle small text-secondary" style="cursor: help;" title="Cada capítol pot tenir diferents variants (per dialectes, estils, etc.), però normalment només n'hi ha una ('Única')"></span></th>
															<th>Fitxer</th>
															<th style="width: 15%;">Pujada</th>
															<th style="width: 15%;">Comentaris</th>
															<th class="text-center" style="width: 5%;">Perduda</th>
															<th class="text-center" style="width: 5%;">Acció</th>
														</tr>
													</thead>
													<tbody>
<?php
		for ($j=0;$j<count($files);$j++) {
?>
														<tr id="form-files-list-<?php echo $chapters[$i]['id']; ?>-row-<?php echo $j+1; ?>">
															<td class="align-middle">
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-variant_name-<?php echo $j+1; ?>" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-variant_name-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($files[$j]['variant_name']); ?>" required maxlength="200" placeholder="- Variant -"/>
															</td>
															<td class="align-middle">
																<div id="form-files-list-<?php echo $chapters[$i]['id']; ?>-file_details-<?php echo $j+1; ?>" class="small"><?php echo !empty($files[$j]['original_filename']) ? '<span style="color: black;"><span class="fa fa-check fa-fw"></span> Ja hi ha pujat el fitxer <strong>'.htmlspecialchars($files[$j]['original_filename']).'</strong>.</span>' : '<span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span>'; ?></div>
															</td>
															<td class="align-middle">
																<label style="margin-bottom: 0;" for="form-files-list-<?php echo $chapters[$i]['id']; ?>-file-<?php echo $j+1; ?>" class="btn btn-sm btn-<?php echo !empty($files[$j]['original_filename']) ? 'warning' : 'info' ; ?> w-100"><span class="fa fa-upload pr-2"></span><?php echo !empty($files[$j]['original_filename']) ? 'Canvia el fitxer...' : 'Puja un fitxer...' ; ?></label>
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-file-<?php echo $j+1; ?>" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-file-<?php echo $j+1; ?>" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);"/>
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-id-<?php echo $j+1; ?>" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $files[$j]['id']; ?>"/>
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-number_of_pages-<?php echo $j+1; ?>" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-number_of_pages-<?php echo $j+1; ?>" type="hidden" value="<?php echo $files[$j]['number_of_pages']; ?>"/>
															</td>
															<td class="align-middle">
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-comments-<?php echo $j+1; ?>" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-comments-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($files[$j]['comments']); ?>" maxlength="200"/>
															</td>
															<td class="text-center align-middle">
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-lost-<?php echo $j+1; ?>" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-lost-<?php echo $j+1; ?>" type="checkbox" value="1""<?php echo empty($files[$j]['original_filename']) ? ' checked' : ''; ?>/>
															</td>
															<td class="text-center align-middle">
																<button id="form-files-list-<?php echo $chapters[$i]['id']; ?>-delete-<?php echo $j+1; ?>" onclick="deleteFileRow(<?php echo $chapters[$i]['id']; ?>,<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
		}
		if (count($files)==0) {
?>
														<tr id="form-files-list-<?php echo $chapters[$i]['id']; ?>-row-1">
															<td class="align-middle">
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-variant_name-1" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-variant_name-1" type="text" class="form-control" value="Única" required maxlength="200" placeholder="- Variant -"/>
															</td>
															<td class="align-middle">
																<div id="form-files-list-<?php echo $chapters[$i]['id']; ?>-file_details-1" class="small"><span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span></div>
															</td>
															<td class="align-middle">
																<label style="margin-bottom: 0;" for="form-files-list-<?php echo $chapters[$i]['id']; ?>-file-1" class="btn btn-sm btn-info w-100"><span class="fa fa-upload pr-2"></span>Puja un fitxer...</label>
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-file-1" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-file-1" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);"/>
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-id-1" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-id-1" type="hidden" value="-1"/>
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-number_of_pages-1" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-number_of_pages-1" type="hidden" value="0"/>
															</td>
															<td class="align-middle">
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-comments-1" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-comments-1" type="text" class="form-control" value="" maxlength="200"/>
															</td>
															<td class="text-center align-middle">
																<input id="form-files-list-<?php echo $chapters[$i]['id']; ?>-lost-1" name="form-files-list-<?php echo $chapters[$i]['id']; ?>-lost-1" type="checkbox" value="1"/>
															</td>
															<td class="text-center align-middle">
																<button id="form-files-list-<?php echo $chapters[$i]['id']; ?>-delete-1" onclick="deleteFileRow(<?php echo $chapters[$i]['id']; ?>,1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
		}
?>
													</tbody>
												</table>
											</div>
											<div class="w-100 text-center"><button onclick="addFileRow(<?php echo $chapters[$i]['id']; ?>);" type="button" class="btn btn-info btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix una altra variant per a aquest capítol</button></div>
										</div>
									</div>
								</div>
<?php
	}
?>
							</div>
						</div>
						<div class="form-group">
							<label for="form-extras-list">Material extra</label>
							<div class="container" id="form-extras-list">
<?php

	if (!empty($_GET['id']) && is_numeric($_GET['id'])) {
		$resultex = query("SELECT f.* FROM file f WHERE f.manga_version_id=".escape($_GET['id'])." AND f.chapter_id IS NULL ORDER BY f.extra_name ASC");
		$extras = array();
		while ($rowex = mysqli_fetch_assoc($resultex)) {
			array_push($extras, $rowex);
		}
		mysqli_free_result($resultex);
	} else {
		$extras=array();
	}
?>
								<div class="form-group">
									<div class="container" id="form-extras-list">
										<div class="row mb-3">
											<div class="w-100 column">
												<table class="table table-bordered table-hover table-sm" id="extras-list-table" data-count="<?php echo count($extras); ?>">
													<thead>
														<tr>
															<th style="width: 20%;" class="mandatory">Nom</th>
															<th class="mandatory">Fitxer</th>
															<th style="width: 15%;">Pujada</th>
															<th style="width: 22%;">Comentaris</th>
															<th class="text-center" style="width: 5%;">Acció</th>
														</tr>
													</thead>
													<tbody>
														<tr id="extras-list-table-empty" class="<?php echo count($extras)>0 ? 'd-none' : ''; ?>">
															<td colspan="5" class="text-center">- No hi ha cap extra -</td>
														</tr>
<?php
	for ($j=0;$j<count($extras);$j++) {
?>
														<tr id="form-extras-list-row-<?php echo $j+1; ?>">
															<td class="align-middle">
																<input id="form-extras-list-name-<?php echo $j+1; ?>" name="form-extras-list-name-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['extra_name']); ?>" maxlength="200" required placeholder="- Introdueix un nom -"/>
																<input id="form-extras-list-id-<?php echo $j+1; ?>" name="form-extras-list-id-<?php echo $j+1; ?>" type="hidden" value="<?php echo $extras[$j]['id']; ?>"/>
																<input id="form-extras-list-number_of_pages-<?php echo $j+1; ?>" name="form-extras-list-number_of_pages-<?php echo $j+1; ?>" type="hidden" value="<?php echo $extras[$j]['number_of_pages']; ?>"/>
															</td>
															<td class="align-middle">
																<div id="form-extras-list-file_details-<?php echo $j+1; ?>" class="small"><?php echo !empty($extras[$j]['original_filename']) ? '<span style="color: black;"><span class="fa fa-check fa-fw"></span> Ja hi ha pujat el fitxer <strong>'.htmlspecialchars($extras[$j]['original_filename']).'</strong>.</span>' : '<span style="color: gray;"><span class="fa fa-times fa-fw"></span> No hi ha cap fitxer pujat.</span>'; ?></div>
															</td>
															<td class="align-middle">
																<label style="margin-bottom: 0;" for="form-extras-list-file-<?php echo $j+1; ?>" class="btn btn-sm btn-<?php echo !empty($extras[$j]['original_filename']) ? 'warning' : 'primary' ; ?> w-100"><span class="fa fa-upload pr-2"></span><?php echo !empty($extras[$j]['original_filename']) ? 'Canvia el fitxer...' : 'Puja un fitxer...' ; ?></label>
																<input id="form-extras-list-file-<?php echo $j+1; ?>" name="form-extras-list-file-<?php echo $j+1; ?>" type="file" accept=".zip,.rar,.cbz" class="form-control d-none" onchange="uncompressFile(this);"/>
															</td>
															<td class="align-middle">
																<input id="form-extras-list-comments-<?php echo $j+1; ?>" name="form-extras-list-comments-<?php echo $j+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($extras[$j]['comments']); ?>" maxlength="200"/>
															</td>
															<td class="text-center align-middle">
																<button id="form-extras-list-delete-<?php echo $j+1; ?>" onclick="deleteFileExtraRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
															</td>
														</tr>
<?php
	}
?>
													</tbody>
												</table>
											</div>
											<div class="w-100 text-center"><button onclick="addFileExtraRow();" type="button" class="btn btn-info btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un altre material extra</button></div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-view-options">Opcions de visualització de la fitxa pública</label>
							<div id="form-view-options" class="row pl-3 pr-3">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="hidden" id="form-hidden" value="1"<?php echo $row['hidden']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-hidden">Amaga aquesta versió mentre sigui buida <small class="text-muted">(no es mostrarà enlloc fins que no tingui fitxers; si en té, es desmarcarà automàticament)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_chapter_numbers" id="form-show_chapter_numbers" value="1"<?php echo $row['show_chapter_numbers']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_chapter_numbers">Mostra el número dels capítols <small class="text-muted">(normalment activat només en serialitzats; afegeix "Capítol X: " davant del nom dels capítols no especials)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_volumes" id="form-show_volumes" value="1"<?php echo $row['show_volumes']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_volumes">Separa per volums i mostra'n els noms <small class="text-muted">(normalment activat; si només n'hi ha un, no es mostrarà)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_expanded_volumes" id="form-show_expanded_volumes" value="1"<?php echo $row['show_expanded_volumes']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_expanded_volumes">Mostra els volums desplegats per defecte <small class="text-muted">(normalment activat; si n'hi ha molts, es pot desmarcar)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_expanded_extras" id="form-show_expanded_extras" value="1"<?php echo $row['show_expanded_extras']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_expanded_extras">Mostra els extres desplegats per defecte <small class="text-muted">(normalment activat; si n'hi ha molts o poc rellevants, es pot desmarcar)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_unavailable_chapters" id="form-show_unavailable_chapters" value="1"<?php echo $row['show_unavailable_chapters']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_unavailable_chapters">Mostra els capítols que no tinguin cap enllaç <small class="text-muted">(normalment activat; apareixen en gris)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="order_type" id="form-order_type_standard" value="0"<?php echo $row['order_type']==0 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-order_type_standard">Aplica l'ordenació estàndard <small class="text-muted">(primer capítols normals per ordre numèric, després especials per ordre alfabètic estricte)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="order_type" id="form-order_type_alphabetic" value="1"<?php echo $row['order_type']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-order_type_alphabetic">Aplica l'ordenació alfabètica estricta <small class="text-muted">(capítols i especials barrejats, ordre: 1, 10, 11, 12..., 2, 3...)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="radio" name="order_type" id="form-order_type_natural" value="2"<?php echo $row['order_type']==2 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-order_type_natural">Aplica l'ordenació alfabètica natural <small class="text-muted">(capítols i especials barrejats, ordre: 1, 2, 3... 10, 11, 12...)</small></label>
								</div>
							</div>
						</div>
						<div class="form-group text-center pt-2">
<?php
	if (!empty($row['id'])) {
?>
							<div class="form-check form-check-inline mb-2">
								<input class="form-check-input" type="checkbox" name="do_not_count_as_update" id="form-do_not_count_as_update" value="1">
								<label class="form-check-label" for="form-do_not_count_as_update">No moguis a "Darreres actualitzacions"</label>
							</div>
							<br />
<?php
	}
?>
							<button type="submit" name="action" value="<?php echo $row['id']!=NULL? "edit" : "add"; ?>" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix la versió"; ?></button>
						</div>
					</form>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
