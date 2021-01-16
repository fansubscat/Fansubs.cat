<?php
$header_title="Edició de manga - Manga";
$page="manga";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca id");
		}
		if (!empty($_POST['name'])) {
			$data['name']=escape($_POST['name']);
		} else {
			crash("Dades invàlides: manca name");
		}
		if (!empty($_POST['slug'])) {
			$data['slug']=escape($_POST['slug']);
		} else {
			crash("Dades invàlides: manca slug");
		}
		if (!empty($_POST['myanimelist_id']) && is_numeric($_POST['myanimelist_id'])) {
			$data['myanimelist_id']=escape($_POST['myanimelist_id']);
		} else {
			$data['myanimelist_id']="NULL";
		}
		if (!empty($_POST['tadaima_id']) && is_numeric($_POST['tadaima_id'])) {
			$data['tadaima_id']=escape($_POST['tadaima_id']);
		} else {
			$data['tadaima_id']="NULL";
		}
		if (!empty($_POST['score']) && is_numeric($_POST['score'])) {
			$data['score']=escape($_POST['score']);
		} else {
			$data['score']="NULL";
		}
		if (!empty($_POST['reader_type'])) {
			$data['reader_type']=escape($_POST['reader_type']);
		} else {
			crash("Dades invàlides: manca reader_type");
		}
		if (!empty($_POST['alternate_names'])) {
			$data['alternate_names']="'".escape($_POST['alternate_names'])."'";
		} else {
			$data['alternate_names']="NULL";
		}
		if (!empty($_POST['keywords'])) {
			$data['keywords']="'".escape($_POST['keywords'])."'";
		} else {
			$data['keywords']="NULL";
		}
		if (!empty($_POST['type'])) {
			$data['type']=escape($_POST['type']);
		} else {
			crash("Dades invàlides: manca type");
		}
		if (!empty($_POST['publish_date'])) {
			$data['publish_date']="'".date('Y-m-d H:i:s', strtotime($_POST['publish_date']))."'";
		} else {
			$data['publish_date']="NULL";
		}
		if (!empty($_POST['author'])) {
			$data['author']="'".escape($_POST['author'])."'";
		} else {
			$data['author']="NULL";
		}
		if (!empty($_POST['rating'])) {
			$data['rating']="'".escape($_POST['rating'])."'";
		} else {
			$data['rating']="NULL";
		}
		if (!empty($_POST['synopsis'])) {
			$data['synopsis']=escape($_POST['synopsis']);
		} else {
			crash("Dades invàlides: manca synopsis");
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
		if (!empty($_POST['has_licensed_parts'])){
			$data['has_licensed_parts']=1;
		} else {
			$data['has_licensed_parts']=0;
		}
		if (!empty($_POST['order_type'])){
			$data['order_type']=escape($_POST['order_type']);
		} else {
			$data['order_type']=0;
		}

		$genres=array();
		if (!empty($_POST['genres'])) {
			foreach ($_POST['genres'] as $genre) {
				if (is_numeric($genre)) {
					array_push($genres, escape($genre));
				}
				else{
					crash("Dades invàlides: genre no numèric");
				}
			}
		}

		$volumes=array();
		$i=1;
		$total_eps=0;
		while (!empty($_POST['form-volume-list-id-'.$i])) {
			$volume = array();
			if (is_numeric($_POST['form-volume-list-id-'.$i])) {
				$volume['id']=escape($_POST['form-volume-list-id-'.$i]);
			} else {
				crash("Dades invàlides: id de volum no numèric");
			}
			if (!empty($_POST['form-volume-list-number-'.$i]) && is_numeric($_POST['form-volume-list-number-'.$i])) {
				$volume['number']=escape($_POST['form-volume-list-number-'.$i]);
			} else {
				crash("Dades invàlides: número de volum buit o no numèric");
			}
			if (!empty($_POST['form-volume-list-name-'.$i])) {
				$volume['name']="'".escape($_POST['form-volume-list-name-'.$i])."'";
			} else {
				$volume['name']="NULL";
			}
			if ((!empty($_POST['form-volume-list-chapters-'.$i]) && is_numeric($_POST['form-volume-list-chapters-'.$i])) || $_POST['form-volume-list-chapters-'.$i]==='0') {
				$volume['chapters']=escape($_POST['form-volume-list-chapters-'.$i]);
				$total_eps+=$_POST['form-volume-list-chapters-'.$i];
			} else {
				crash("Dades invàlides: número de capítols buit o no numèric");
			}
			if (!empty($_POST['form-volume-list-myanimelist_id-'.$i]) && is_numeric($_POST['form-volume-list-myanimelist_id-'.$i])) {
				$volume['myanimelist_id']=escape($_POST['form-volume-list-myanimelist_id-'.$i]);
			} else {
				$volume['myanimelist_id']="NULL";
			}
			array_push($volumes, $volume);
			$i++;
		}

		if (!empty($_POST['is_open'])){
			$data['chapters']=-1;
		} else {
			$data['chapters']=$total_eps;
		}

		$chapters=array();
		$i=1;
		while (!empty($_POST['form-chapter-list-id-'.$i])) {
			$chapter = array();
			if (is_numeric($_POST['form-chapter-list-id-'.$i])) {
				$chapter['id']=escape($_POST['form-chapter-list-id-'.$i]);
			} else {
				crash("Dades invàlides: id de capítol no numèric");
			}
			if (!empty($_POST['form-chapter-list-volume-'.$i]) && is_numeric($_POST['form-chapter-list-volume-'.$i])) {
				$chapter['volume']=escape($_POST['form-chapter-list-volume-'.$i]);
			} else {
				$chapter['volume']="NULL";
			}
			if (!empty($_POST['form-chapter-list-num-'.$i]) && is_numeric($_POST['form-chapter-list-num-'.$i])) {
				$chapter['number']=escape($_POST['form-chapter-list-num-'.$i]);
			} else {
				$chapter['number']="NULL";
			}
			if (!empty($_POST['form-chapter-list-name-'.$i])) {
				$chapter['name']="'".escape($_POST['form-chapter-list-name-'.$i])."'";
			} else {
				$chapter['name']="NULL";
			}
			array_push($chapters, $chapter);
			$i++;
		}

		$related_manga=array();
		$i=1;
		while (!empty($_POST['form-relatedmangamanga-list-related_manga_id-'.$i])) {
			if (is_numeric($_POST['form-relatedmangamanga-list-related_manga_id-'.$i])) {
				array_push($related_manga,escape($_POST['form-relatedmangamanga-list-related_manga_id-'.$i]));
			} else {
				crash("Dades invàlides: id de manga relacionat no numèric");
			}
			$i++;
		}

		$related_anime=array();
		$i=1;
		while (!empty($_POST['form-relatedmangaanime-list-related_anime_id-'.$i])) {
			if (is_numeric($_POST['form-relatedmangaanime-list-related_anime_id-'.$i])) {
				array_push($related_anime,escape($_POST['form-relatedmangaanime-list-related_anime_id-'.$i]));
			} else {
				crash("Dades invàlides: id d'anime relacionat no numèric");
			}
			$i++;
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-manga", "S'ha actualitzat el manga amb nom '".$data['name']."' (id. de manga: ".$data['id'].")");
			query("UPDATE manga SET slug='".$data['slug']."',name='".$data['name']."',alternate_names=".$data['alternate_names'].",keywords=".$data['keywords'].",score=".$data['score'].",reader_type='".$data['reader_type']."',type='".$data['type']."',publish_date=".$data['publish_date'].",author=".$data['author'].",rating=".$data['rating'].",chapters=".$data['chapters'].",synopsis='".$data['synopsis']."',myanimelist_id=".$data['myanimelist_id'].",tadaima_id=".$data['tadaima_id'].",show_volumes=".$data['show_volumes'].",show_expanded_volumes=".$data['show_expanded_volumes'].",show_chapter_numbers=".$data['show_chapter_numbers'].",show_unavailable_chapters=".$data['show_unavailable_chapters'].",has_licensed_parts=".$data['has_licensed_parts'].",order_type=".$data['order_type'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
			query("DELETE FROM rel_manga_genre WHERE manga_id=".$data['id']);
			foreach ($genres as $genre) {
				query("INSERT INTO rel_manga_genre (manga_id,genre_id) VALUES (".$data['id'].",".$genre.")");
			}
			$ids=array();
			foreach ($volumes as $volume) {
				if ($volume['id']!=-1) {
					array_push($ids,$volume['id']);
				}
			}
			query("DELETE FROM volume WHERE manga_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($volumes as $volume) {
				if ($volume['id']==-1) {
					query("INSERT INTO volume (manga_id,number,name,chapters,myanimelist_id) VALUES (".$data['id'].",".$volume['number'].",".$volume['name'].",".$volume['chapters'].",".$volume['myanimelist_id'].")");
				} else {
					query("UPDATE volume SET number=".$volume['number'].",name=".$volume['name'].",chapters=".$volume['chapters'].",myanimelist_id=".$volume['myanimelist_id']." WHERE id=".$volume['id']);
				}
			}
			$ids=array();
			foreach ($chapters as $chapter) {
				if ($chapter['id']!=-1) {
					array_push($ids,$chapter['id']);
				}
			}
			//Chapter_instances will be removed too because their FK is set to cascade
			query("DELETE FROM chapter WHERE manga_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($chapters as $chapter) {
				if ($chapter['id']==-1) {
					query("INSERT INTO chapter (manga_id,volume_id,number,name) VALUES (".$data['id'].",(SELECT id FROM volume WHERE number=".$chapter['volume']." AND manga_id=".$data['id']."),".$chapter['number'].",".$chapter['name'].")");
				} else {
					query("UPDATE chapter SET volume_id=(SELECT id FROM volume WHERE number=".$chapter['volume']." AND manga_id=".$data['id']."),number=".$chapter['number'].",name=".$chapter['name']." WHERE id=".$chapter['id']);
				}
			}
			query("DELETE FROM related_manga_manga WHERE manga_id=".$data['id']);
			foreach ($related_manga as $related_manga_id) {
				query("REPLACE INTO related_manga_manga (manga_id,related_manga_id) VALUES (".$data['id'].",".$related_manga_id.")");
			}
			query("DELETE FROM related_manga_anime WHERE manga_id=".$data['id']);
			foreach ($related_anime as $related_anime_id) {
				query("REPLACE INTO related_manga_anime (manga_id,related_anime_id) VALUES (".$data['id'].",".$related_anime_id.")");
			}

			if (is_uploaded_file($_FILES['image']['tmp_name'])) {
				move_uploaded_file($_FILES['image']["tmp_name"], '../mangav2.fansubs.cat/images/manga/'.$data['id'].'.jpg');
			} else if (!empty($_POST['image_url'])){
				copy($_POST['image_url'],'../mangav2.fansubs.cat/images/manga/'.$data['id'].'.jpg');
			}

			if (is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
				move_uploaded_file($_FILES['featured_image']["tmp_name"], '../mangav2.fansubs.cat/images/featured/'.$data['id'].'.jpg');
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}
		else {
			log_action("create-manga", "S'ha creat un manga amb nom '".$data['name']."'");
			query("INSERT INTO manga (slug,name,alternate_names,keywords,type,publish_date,author,rating,chapters,synopsis,myanimelist_id,tadaima_id,score,reader_type,show_volumes,show_expanded_volumes,show_chapter_numbers,show_unavailable_chapters,has_licensed_parts,order_type,created,created_by,updated,updated_by) VALUES ('".$data['slug']."','".$data['name']."',".$data['alternate_names'].",".$data['keywords'].",'".$data['type']."',".$data['publish_date'].",".$data['author'].",".$data['rating'].",".$data['chapters'].",'".$data['synopsis']."',".$data['myanimelist_id'].",".$data['tadaima_id'].",".$data['score'].",'".$data['reader_type']."',".$data['show_volumes'].",".$data['show_expanded_volumes'].",".$data['show_chapter_numbers'].",".$data['show_unavailable_chapters'].",".$data['has_licensed_parts'].",".$data['order_type'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			$inserted_id=mysqli_insert_id($db_connection);
			foreach ($genres as $genre) {
				query("INSERT INTO rel_manga_genre (manga_id,genre_id) VALUES (".$inserted_id.",".$genre.")");
			}
			foreach ($volumes as $volume) {
				query("INSERT INTO volume (manga_id,number,name,chapters,myanimelist_id) VALUES (".$inserted_id.",".$volume['number'].",".$volume['name'].",".$volume['chapters'].",".$volume['myanimelist_id'].")");
			}
			foreach ($chapters as $chapter) {
				query("INSERT INTO chapter (manga_id,volume_id,number,name) VALUES (".$inserted_id.",(SELECT id FROM volume WHERE number=".$chapter['volume']." AND manga_id=".$inserted_id."),".$chapter['number'].",".$chapter['name'].")");
			}
			foreach ($related_manga as $related_manga_id) {
				query("REPLACE INTO related_manga_manga (manga_id,related_manga_id) VALUES (".$inserted_id.",".$related_manga_id.")");
			}
			foreach ($related_anime as $related_anime_id) {
				query("REPLACE INTO related_manga_anime (manga_id,related_anime_id) VALUES (".$inserted_id.",".$related_anime_id.")");
			}

			if (is_uploaded_file($_FILES['image']['tmp_name'])) {
				move_uploaded_file($_FILES['image']["tmp_name"], '../mangav2.fansubs.cat/images/manga/'.$inserted_id.'.jpg');
			} else if (!empty($_POST['image_url'])){
				copy($_POST['image_url'],'../mangav2.fansubs.cat/images/manga/'.$inserted_id.'.jpg');
			}

			if (is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
				move_uploaded_file($_FILES['featured_image']["tmp_name"], '../mangav2.fansubs.cat/images/featured/'.$inserted_id.'.jpg');
			}

			$_SESSION['message']="S'han desat les dades correctament.<br /><a class=\"btn btn-primary mt-2\" href=\"manga_version_edit.php?manga_id=$inserted_id\"><span class=\"fa fa-plus pr-2\"></span>Crea'n una versió</a>";
		}

		header("Location: manga_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT m.* FROM manga m WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Manga not found');
		mysqli_free_result($result);

		$resultg = query("SELECT mg.* FROM rel_manga_genre mg WHERE mg.manga_id=".escape($_GET['id']));
		$genres = array();
		while ($rowg = mysqli_fetch_assoc($resultg)) {
			array_push($genres, $rowg['genre_id']);
		}
		mysqli_free_result($resultg);

		$resultv = query("SELECT v.* FROM volume v WHERE v.manga_id=".escape($_GET['id'])." ORDER BY v.number ASC");
		$volumes = array();
		while ($rowv = mysqli_fetch_assoc($resultv)) {
			array_push($volumes, $rowv);
		}
		mysqli_free_result($resultv);

		$resultc = query("SELECT c.*,v.number volume FROM chapter c LEFT JOIN volume v ON c.volume_id=v.id WHERE c.manga_id=".escape($_GET['id'])." ORDER BY v.number IS NULL ASC, v.number ASC, c.number IS NULL ASC, c.number ASC, c.name ASC");
		$chapters = array();
		while ($rowc = mysqli_fetch_assoc($resultc)) {
			array_push($chapters, $rowc);
		}
		mysqli_free_result($resultc);
	} else {
		$row = array();
		$genres = array();
		$volumes = array();
		$chapters = array();
		$row['show_volumes']=1;
		$row['show_expanded_volumes']=1;
		$row['show_chapter_numbers']=1;
		$row['show_unavailable_chapters']=1;
		$row['has_licensed_parts']=0;
		$row['order_type']=0;
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita el manga" : "Afegeix un manga"; ?></h4>
					<hr>
					<form method="post" action="manga_edit.php" enctype="multipart/form-data" onsubmit="return checkNumberOfChapters()">
						<div class="row align-items-end">
							<div class="col-sm-3">
								<div class="form-group">
									<label for="form-myanimelist_id">Identificador de MyAnimeList</label>
									<input class="form-control" name="myanimelist_id" id="form-myanimelist_id" type="number" value="<?php echo $row['myanimelist_id']; ?>">
								</div>
							</div>
							<div class="col-sm form-group">
								<button type="button" id="import-from-mal-manga" class="btn btn-primary">
									<span id="import-from-mal-loading" class="d-none spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
									<span id="import-from-mal-not-loading" class="fa fa-th-list pr-2"></span>Importa la fitxa de MyAnimeList
								</button>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-tadaima_id">Identificador de fil a Tadaima.cat</label>
									<input class="form-control" name="tadaima_id" id="form-tadaima_id" type="number" value="<?php echo $row['tadaima_id']; ?>">
								</div>
							</div>
						</div>
						<div id="import-from-mal-done" class="col-sm form-group alert alert-warning d-none">
							<span class="fa fa-exclamation-triangle pr-2"></span>S'ha importat la fitxa de MyAnimeList. Revisa que les dades siguin correctes i tradueix-ne la sinopsi i el nom, si s'escau.
						</div>
						<hr />
						<div class="row">
							<div class="col-sm">
								<div class="form-group">
									<label for="form-name-with-autocomplete" class="mandatory">Nom</label>
									<input class="form-control" name="name" id="form-name-with-autocomplete" required maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['name'])); ?>">
									<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-slug">Identificador<span class="mandatory"></span> <small class="text-muted">(autogenerat, no cal editar-lo)</small></label>
									<input class="form-control" name="slug" id="form-slug" required maxlength="200" value="<?php echo htmlspecialchars($row['slug']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-publish_status">Estat</label>
									<div id="form-publish_status" class="row pl-3 pr-3">
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="is_open" id="form-is_open" value="1"<?php echo $row['chapters']==-1? " checked" : ""; ?>>
											<label class="form-check-label" for="form-is_open">En edició (manga obert)</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-8">
								<div class="form-group">
									<label for="form-alternate_names">Altres noms</label>
									<input class="form-control" name="alternate_names" id="form-alternate_names" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['alternate_names'])); ?>">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-score">Puntuació a MyAnimeList</label>
									<input class="form-control" name="score" id="form-score" type="number" value="<?php echo $row['score']; ?>" step=".01">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="form-group">
									<label for="form-keywords">Paraules clau <small class="text-muted">(separades per espais; que no siguin ja al nom o noms alternatius, s'utilitza per a la cerca)</small></label>
									<input class="form-control" name="keywords" id="form-keywords" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['keywords'])); ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="form-group">
									<label for="form-type" class="mandatory">Tipus</label>
									<select class="form-control" name="type" id="form-type" required>
										<option value="">- Selecciona un tipus -</option>
										<option value="oneshot"<?php echo $row['type']=='oneshot' ? " selected" : ""; ?>>One-shot</option>
										<option value="serialized"<?php echo $row['type']=='serialized' ? " selected" : ""; ?>>Serialitzat</option>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-publish_date">Data d'estrena</label>
									<input class="form-control" name="publish_date" type="date" id="form-publish_date" maxlength="200" value="<?php echo !empty($row['publish_date']) ? date('Y-m-d', strtotime($row['publish_date'])) : ""; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-author">Autor</label>
									<input class="form-control" name="author" id="form-author" maxlength="200" value="<?php echo htmlspecialchars($row['author']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-rating">Valoració per edats</label>
									<select class="form-control" name="rating" id="form-rating">
										<option value="">- Selecciona una valoració -</option>
										<option value="TP"<?php echo $row['rating']=='TP' ? " selected" : ""; ?>>Tots els públics</option>
										<option value="+7"<?php echo $row['rating']=='+7' ? " selected" : ""; ?>>Majors de 7 anys</option>
										<option value="+13"<?php echo $row['rating']=='+13' ? " selected" : ""; ?>>Majors de 13 anys</option>
										<option value="+16"<?php echo $row['rating']=='+16' ? " selected" : ""; ?>>Majors de 16 anys</option>
										<option value="+18"<?php echo $row['rating']=='+18' ? " selected" : ""; ?>>Majors de 18 anys</option>
										<option value="XXX"<?php echo $row['rating']=='XXX' ? " selected" : ""; ?>>Majors de 18 anys (hentai)</option>
									</select>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-synopsis">Sinopsi<span class="mandatory"></span> <small class="text-muted">(admet <a href="https://www.markdownguide.org/cheat-sheet/" target="_blank">Markdown</a>)</small></label><br></label>
							<textarea class="form-control" name="synopsis" id="form-synopsis" required style="height: 150px;"><?php echo htmlspecialchars(str_replace('&#039;',"'",html_entity_decode($row['synopsis']))); ?></textarea>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-reader_type" class="mandatory">Tipus de lector</label>
									<select class="form-control" name="reader_type" id="form-reader_type" required>
										<option value="paged"<?php echo $row['reader_type']!='strip' ? " selected" : ""; ?>>Normal (paginat)</option>
										<option value="strip"<?php echo $row['reader_type']=='strip' ? " selected" : ""; ?>>Tira llarga (webtoon)</option>
									</select>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label>Imatge de portada<?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?><br><small class="text-muted">(JPEG, ~300x424, ≤450x600, ≤150 KiB)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists('../mangav2.fansubs.cat/images/manga/'.$row['id'].'.jpg');
?>
									<label for="form-image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'info' ; ?>"><span class="fa fa-upload pr-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
									<input class="form-control d-none" name="image" type="file" id="form-image" accept="image/jpeg" value="" onchange="checkImageUpload(this, 153600, 'form-image-preview', 'form-image-preview-link','form-image_url');">
									<input class="form-control" name="image_url" type="hidden" id="form-image_url" value="">
								</div>
							</div>
							<div class="col-sm-1">
								<div class="form-group">
									<a id="form-image-preview-link"<?php echo $file_exists ? ' href="https://mangav2.fansubs.cat/images/manga/'.$row['id'].'.jpg" data-original="https://mangav2.fansubs.cat/images/manga/'.$row['id'].'.jpg"' : ''; ?> target="_blank">
										<img id="form-image-preview" style="width: 64px; height: 90px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="https://mangav2.fansubs.cat/images/manga/'.$row['id'].'.jpg" data-original="https://mangav2.fansubs.cat/images/manga/'.$row['id'].'.jpg"' : ''; ?> alt="">
									</a>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label>Imatge de capçalera<?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?><br><small class="text-muted">(JPEG, ~1104x256, ≤1200x400, ≤300 KiB)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists('../mangav2.fansubs.cat/images/featured/'.$row['id'].'.jpg');
?>
									<label for="form-featured_image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'info' ; ?>"><span class="fa fa-upload pr-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
									<input class="d-none" name="featured_image" type="file" accept="image/jpeg" id="form-featured_image" onchange="checkImageUpload(this, 307200, 'form-featured-image-preview', 'form-featured-image-preview-link');">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="form-group">
									<a id="form-featured-image-preview-link"<?php echo $file_exists ? ' href="https://mangav2.fansubs.cat/images/featured/'.$row['id'].'.jpg" data-original="https://mangav2.fansubs.cat/images/featured/'.$row['id'].'.jpg"' : ''; ?> target="_blank">
										<img id="form-featured-image-preview" style="width: 400px; height: 85px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="https://mangav2.fansubs.cat/images/featured/'.$row['id'].'.jpg" data-original="https://mangav2.fansubs.cat/images/featured/'.$row['id'].'.jpg"' : ''; ?> alt="">
									</a>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-genres">Gèneres</label>
							<div id="form-genres" class="row pl-3 pr-3">
<?php
	$resultg = query("SELECT g.* FROM genre g ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-2">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-myanimelist-id="<?php echo $rowg['myanimelist_id_manga']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="form-group">
							<label for="form-volume-list">Volums</label>
							<div class="container" id="form-volume-list">
								<div class="row">
									<div class="w-100 column">
										<table class="table table-bordered table-hover table-sm" id="volume-list-table" data-count="<?php echo max(count($volumes),1); ?>">
											<thead>
												<tr>
													<th style="width: 10%;" class="mandatory">Núm.</th>
													<th>Nom <small class="text-muted">(només es mostra si n'hi ha més d'un i la casella "Separa per volums" està marcada)</small></th>
													<th class="mandatory" style="width: 15%;">Capítols</th>
													<th style="width: 15%;">Id. MyAnimeList</th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
<?php
	for ($i=0;$i<count($volumes);$i++) {
?>
												<tr id="form-volume-list-row-<?php echo $i+1; ?>">
													<td>
														<input id="form-volume-list-number-<?php echo $i+1; ?>" name="form-volume-list-number-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $volumes[$i]['number']; ?>" required/>
														<input id="form-volume-list-id-<?php echo $i+1; ?>" name="form-volume-list-id-<?php echo $i+1; ?>" type="hidden" value="<?php echo $volumes[$i]['id']; ?>"/>
													</td>
													<td>
														<input id="form-volume-list-name-<?php echo $i+1; ?>" name="form-volume-list-name-<?php echo $i+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($volumes[$i]['name']); ?>" placeholder="(Sense nom)"/>
													</td>
													<td>
														<input id="form-volume-list-chapters-<?php echo $i+1; ?>" name="form-volume-list-chapters-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $volumes[$i]['chapters']; ?>" required/>
													</td>
													<td>
														<input id="form-volume-list-myanimelist_id-<?php echo $i+1; ?>" name="form-volume-list-myanimelist_id-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $volumes[$i]['myanimelist_id']; ?>"/>
													</td>
													<td class="text-center align-middle">
														<button id="form-volume-list-delete-<?php echo $i+1; ?>" onclick="deleteVolumeRow(<?php echo $i+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
	if (count($volumes)==0) {
?>
												<tr id="form-volume-list-row-1">
													<td>
														<input id="form-volume-list-number-1" name="form-volume-list-number-1" type="number" class="form-control" value="1" required/>
														<input id="form-volume-list-id-1" name="form-volume-list-id-1" type="hidden" value="-1"/>
													</td>
													<td>
														<input id="form-volume-list-name-1" name="form-volume-list-name-1" type="text" class="form-control" value="" placeholder="(Sense nom)"/>
													</td>
													<td>
														<input id="form-volume-list-chapters-1" name="form-volume-list-chapters-1" type="number" class="form-control" value="" required/>
													</td>
													<td>
														<input id="form-volume-list-myanimelist_id-1" name="form-volume-list-myanimelist_id-1" type="number" class="form-control" value=""/>
													</td>
													<td class="text-center align-middle">
														<button id="form-volume-list-delete-1" onclick="deleteVolumeRow(1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="w-100 text-center"><button onclick="addVolumeRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un volum</button></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-chapter-list">Capítols</label>
							<div id="import-from-mal-chapters-done" class="col-sm form-group alert alert-warning d-none">
								<span class="fa fa-exclamation-triangle pr-2"></span>S'han importat els capítols de MyAnimeList. Revisa'n tots els camps.
							</div>
							<div class="container" id="form-chapter-list">
								<div class="row">
									<div class="w-100 column">
										<table class="table table-bordered table-hover table-sm" id="chapter-list-table" data-count="<?php echo max(count($chapters),1); ?>">
											<thead>
												<tr>
													<th style="width: 10%;">Vol.</th>
													<th style="width: 10%;">Núm.</th>
													<th>Títol <small class="text-muted">(informatiu, només es mostra públicament en el cas dels especials)</small></th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
<?php
	for ($i=0;$i<count($chapters);$i++) {
?>
												<tr id="form-chapter-list-row-<?php echo $i+1; ?>">
													<td>
														<input id="form-chapter-list-volume-<?php echo $i+1; ?>" name="form-chapter-list-volume-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $chapters[$i]['volume']; ?>" placeholder="(Altres)"/>
													</td>
													<td>
														<input id="form-chapter-list-num-<?php echo $i+1; ?>" name="form-chapter-list-num-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $chapters[$i]['number']!=NULL ? floatval($chapters[$i]['number']) : ''; ?>" placeholder="(Esp.)" step="any"/>
														<input id="form-chapter-list-id-<?php echo $i+1; ?>" name="form-chapter-list-id-<?php echo $i+1; ?>" type="hidden" value="<?php echo $chapters[$i]['id']; ?>"/>
													</td>
													<td>
														<input id="form-chapter-list-name-<?php echo $i+1; ?>" name="form-chapter-list-name-<?php echo $i+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($chapters[$i]['name']); ?>" placeholder="(Sense títol)"/>
													</td>
													<td class="text-center align-middle">
														<button id="form-chapter-list-delete-<?php echo $i+1; ?>" onclick="deleteChapterRow(<?php echo $i+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
	if (count($chapters)==0) {
?>
												<tr id="form-chapter-list-row-1">
													<td>
														<input id="form-chapter-list-volume-1" name="form-chapter-list-volume-1" type="number" class="form-control" value="1" placeholder="(Altres)" step="any"/>
													</td>
													<td>
														<input id="form-chapter-list-num-1" name="form-chapter-list-num-1" type="number" class="form-control" value="1" placeholder="(Esp.)"/>
														<input id="form-chapter-list-id-1" name="form-chapter-list-id-1" type="hidden" value="-1"/>
													</td>
													<td>
														<input id="form-chapter-list-name-1" name="form-chapter-list-name-1" type="text" class="form-control" value="" placeholder="(Sense títol)"/>
													</td>
													<td class="text-center align-middle">
														<button id="form-chapter-list-delete-1" onclick="deleteChapterRow(1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<button onclick="addChapterRow(false);" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un capítol</button>
									<button onclick="addChapterRow(true);" type="button" class="btn btn-success btn-sm ml-2"><span class="fa fa-plus pr-2"></span>Afegeix un especial</button>
									<span style="flex-grow: 1;"></span>
									<button type="button" id="generate-chapters" class="btn btn-primary btn-sm ml-2">
										<span class="fa fa-sort-numeric-down pr-2"></span>
										Genera els capítols automàticament
									</button>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-relatedmangamanga-list">Manga relacionat <small class="text-muted">(es mostrarà a la fitxa pública)</small></label>
							<div class="container" id="form-relatedmangamanga-list">
<?php

	if (!empty($row['id'])) {
		$resultrm = query("SELECT rm.* FROM related_manga_manga rm WHERE rm.manga_id=".escape($_GET['id'])." ORDER BY rm.related_manga_id ASC");
		$related_manga = array();
		while ($rowrm = mysqli_fetch_assoc($resultrm)) {
			array_push($related_manga, $rowrm);
		}
		mysqli_free_result($resultrm);
	} else {
		$related_manga=array();
	}
?>
								<div class="row mb-3">
									<div class="w-100 column">
										<select id="form-relatedmangamanga-list-related_manga_id-XXX" name="form-relatedmangamanga-list-related_manga_id-XXX" class="form-control d-none">
											<option value="">- Selecciona un manga -</option>
<?php
		$resultm = query("SELECT m.* FROM manga m WHERE id<>".(!empty($row['id']) ? $row['id'] : -1)." ORDER BY m.name ASC");
		while ($mrow = mysqli_fetch_assoc($resultm)) {
?>
											<option value="<?php echo $mrow['id']; ?>"><?php echo htmlspecialchars($mrow['name']); ?></option>
<?php
		}
		mysqli_free_result($resultm);
?>
										</select>
										<table class="table table-bordered table-hover table-sm" id="relatedmangamanga-list-table" data-count="<?php echo count($related_manga); ?>">
											<thead>
												<tr>
													<th class="mandatory">Manga</th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
												<tr id="relatedmangamanga-list-table-empty" class="<?php echo count($related_manga)>0 ? 'd-none' : ''; ?>">
													<td colspan="2" class="text-center">- No hi ha cap manga relacionat -</td>
												</tr>
<?php
	for ($j=0;$j<count($related_manga);$j++) {
?>
												<tr id="form-relatedmangamanga-list-row-<?php echo $j+1; ?>">
													<td>
														<select id="form-relatedmangamanga-list-related_manga_id-<?php echo $j+1; ?>" name="form-relatedmangamanga-list-related_manga_id-<?php echo $j+1; ?>" class="form-control" required>
															<option value="">- Selecciona un anime -</option>
<?php
		$resultm = query("SELECT m.* FROM manga m WHERE id<>".(!empty($row['id']) ? $row['id'] : -1)." ORDER BY m.name ASC");
		while ($mrow = mysqli_fetch_assoc($resultm)) {
?>
															<option value="<?php echo $mrow['id']; ?>"<?php echo $related_manga[$j]['related_manga_id']==$mrow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($mrow['name']); ?></option>
<?php
		}
		mysqli_free_result($results);
?>
														</select>
													</td>
													<td class="text-center align-middle">
														<button id="form-relatedmangamanga-list-delete-<?php echo $j+1; ?>" onclick="deleteRelatedMangaMangaRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="form-group row w-100 ml-0">
										<div class="col-sm text-center" style="padding-left: 0; padding-right: 0">
											<button onclick="addRelatedMangaMangaRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un manga relacionat</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-relatedmangaanime-list">Anime relacionat <small class="text-muted">(es mostrarà a la fitxa pública)</small></label>
							<div class="container" id="form-relatedmangaanime-list">
<?php

	if (!empty($row['id'])) {
		$resultra = query("SELECT ra.* FROM related_manga_anime ra WHERE ra.manga_id=".escape($_GET['id'])." ORDER BY ra.related_anime_id ASC");
		$related_anime = array();
		while ($rowra = mysqli_fetch_assoc($resultra)) {
			array_push($related_anime, $rowra);
		}
		mysqli_free_result($resultra);
	} else {
		$related_anime=array();
	}
?>
								<div class="row mb-3">
									<div class="w-100 column">
										<select id="form-relatedmangaanime-list-related_anime_id-XXX" name="form-relatedmangaanime-list-related_anime_id-XXX" class="form-control d-none">
											<option value="">- Selecciona un anime -</option>
<?php
		$results = query("SELECT s.* FROM series s ORDER BY s.name ASC");
		while ($srow = mysqli_fetch_assoc($results)) {
?>
											<option value="<?php echo $srow['id']; ?>"><?php echo htmlspecialchars($srow['name']); ?></option>
<?php
		}
		mysqli_free_result($results);
?>
										</select>
										<table class="table table-bordered table-hover table-sm" id="relatedmangaanime-list-table" data-count="<?php echo count($related_anime); ?>">
											<thead>
												<tr>
													<th class="mandatory">Anime</th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
												<tr id="relatedmangaanime-list-table-empty" class="<?php echo count($related_anime)>0 ? 'd-none' : ''; ?>">
													<td colspan="2" class="text-center">- No hi ha cap anime relacionat -</td>
												</tr>
<?php
	for ($j=0;$j<count($related_anime);$j++) {
?>
												<tr id="form-relatedmangaanime-list-row-<?php echo $j+1; ?>">
													<td>
														<select id="form-relatedmangaanime-list-related_anime_id-<?php echo $j+1; ?>" name="form-relatedmangaanime-list-related_anime_id-<?php echo $j+1; ?>" class="form-control" required>
															<option value="">- Selecciona un anime -</option>
<?php
		$results = query("SELECT s.* FROM series s ORDER BY s.name ASC");
		while ($srow = mysqli_fetch_assoc($results)) {
?>
															<option value="<?php echo $srow['id']; ?>"<?php echo $related_anime[$j]['related_anime_id']==$srow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($srow['name']); ?></option>
<?php
		}
		mysqli_free_result($results);
?>
														</select>
													</td>
													<td class="text-center align-middle">
														<button id="form-relatedmangaanime-list-delete-<?php echo $j+1; ?>" onclick="deleteRelatedMangaAnimeRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="form-group row w-100 ml-0">
										<div class="col-sm text-center" style="padding-left: 0; padding-right: 0">
											<button onclick="addRelatedMangaAnimeRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un anime relacionat</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label for="form-view-options">Opcions de visualització de la fitxa pública</label>
							<div id="form-view-options" class="row pl-3 pr-3">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_volumes" id="form-show_volumes" value="1"<?php echo $row['show_volumes']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_volumes">Separa per volums i mostra'n els noms <small class="text-muted">(si només n'hi ha un, no es mostrarà)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_expanded_volumes" id="form-show_expanded_volumes" value="1"<?php echo $row['show_expanded_volumes']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_expanded_volumes">Mostra els volums desplegats per defecte <small class="text-muted">(si n'hi ha molts o amb molts capítols, és recomanable desmarcar-ho)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_chapter_numbers" id="form-show_chapter_numbers" value="1"<?php echo $row['show_chapter_numbers']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_chapter_numbers">Mostra el número dels capítols normals <small class="text-muted">(afegeix "Capítol X: " davant del nom dels capítols no especials)</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="show_unavailable_chapters" id="form-show_unavailable_chapters" value="1"<?php echo $row['show_unavailable_chapters']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-show_unavailable_chapters">Mostra els capítols que no tinguin cap enllaç <small class="text-muted">(apareixen en gris i amb una nota "No disponible")</small></label>
								</div>
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="has_licensed_parts" id="form-has_licensed_parts" value="1"<?php echo $row['has_licensed_parts']==1 ? " checked" : ""; ?>>
									<label class="form-check-label" for="form-has_licensed_parts">El manga té parts llicenciades <small class="text-muted">(es mostrarà un avís indicant que sols hi ha les parts no llicenciades)</small></label>
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
							<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix el manga"; ?></button>
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
