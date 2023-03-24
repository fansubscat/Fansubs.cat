<?php
$type='anime';

if (!empty($_GET['type']) && ($_GET['type']=='anime' || $_GET['type']=='manga' || $_GET['type']=='liveaction')) {
	$type=$_GET['type'];
} else if (!empty($_POST['type']) && ($_POST['type']=='anime' || $_POST['type']=='manga' || $_POST['type']=='liveaction')) {
	$type=$_POST['type'];
}

switch ($type) {
	case 'anime':
		$header_title="Edició d'anime - Anime";
		$page="anime";
	break;
	case 'manga':
		$header_title="Edició de manga - Manga";
		$page="manga";
	break;
	case 'liveaction':
		$header_title="Edició de contingut d'acció real - Acció real";
		$page="liveaction";
	break;
}

include("header.inc.php");

switch ($type) {
	case 'anime':
		$division_name='Temporades';
		$division_name_singular='Temporada';
		$division_name_short='Temp.';
		$division_one="una temporada";
		$more_than_one="més d'una";
		$open_series="Encara en emissió";
		$content_apos="l'anime";
		$content_one="un anime";
		$external_provider='MyAnimeList';
		break;
	case 'manga':
		$division_name='Volums';
		$division_name_singular='Volum';
		$division_name_short='Vol.';
		$division_one="un volum";
		$more_than_one="més d'un";
		$open_series="Encara en publicació";
		$content_apos="el manga";
		$content_one="un manga";
		$external_provider='MyAnimeList';
		break;
	case 'liveaction':
		$division_name='Temporades';
		$division_name_singular='Temporada';
		$division_name_short='Temp.';
		$division_one="una temporada";
		$more_than_one="més d'una";
		$open_series="Encara en emissió";
		$content_apos="el contingut d'acció real";
		$content_one="un contingut d'acció real";
		$external_provider='MyDramaList';
		break;
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca id");
		}
		if (!empty($_POST['slug'])) {
			$data['slug']=escape($_POST['slug']);
		} else {
			crash("Dades invàlides: manca slug");
		}
		if (!empty($_POST['name'])) {
			$data['name']=escape($_POST['name']);
		} else {
			crash("Dades invàlides: manca name");
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
		$data['type']=$type;
		if (!empty($_POST['subtype'])) {
			$data['subtype']=escape($_POST['subtype']);
		} else {
			crash("Dades invàlides: manca subtype");
		}
		if (!empty($_POST['publish_date'])) {
			$data['publish_date']="'".date('Y-m-d', strtotime($_POST['publish_date']))."'";
		} else {
			$data['publish_date']="NULL";
		}
		if (!empty($_POST['author'])) {
			$data['author']="'".escape($_POST['author'])."'";
		} else {
			$data['author']="NULL";
		}
		if (!empty($_POST['director'])) {
			$data['director']="'".escape($_POST['director'])."'";
		} else {
			$data['director']="NULL";
		}
		if (!empty($_POST['studio'])) {
			$data['studio']="'".escape($_POST['studio'])."'";
		} else {
			$data['studio']="NULL";
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
		if (!empty($_POST['external_id'])) {
			$data['external_id']="'".escape($_POST['external_id'])."'";
		} else {
			$data['external_id']="NULL";
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
		if (!empty($_POST['has_licensed_parts'])){
			$data['has_licensed_parts']=1;
		} else {
			$data['has_licensed_parts']=0;
		}
		if (!empty($_POST['duration'])) {
			$data['duration']="'".escape($_POST['duration'])."'";
		} else {
			$data['duration']="NULL";
		}
		if (!empty($_POST['comic_type'])) {
			$data['comic_type']="'".escape($_POST['comic_type'])."'";
		} else {
			$data['comic_type']="NULL";
		}
		if (!empty($_POST['comic_type'])) {
			$data['reader_type']="'".escape($_POST['reader_type'])."'";
		} else {
			$data['reader_type']="NULL";
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

		$divisions=array();
		$i=1;
		$total_eps=0;
		while (!empty($_POST['form-division-list-id-'.$i])) {
			$division = array();
			if (is_numeric($_POST['form-division-list-id-'.$i])) {
				$division['id']=escape($_POST['form-division-list-id-'.$i]);
			} else {
				crash("Dades invàlides: id de divisió no numèric");
			}
			if (!empty($_POST['form-division-list-number-'.$i]) && is_numeric($_POST['form-division-list-number-'.$i])) {
				$division['number']=escape($_POST['form-division-list-number-'.$i]);
			} else {
				crash("Dades invàlides: número de divisió buit o no numèric");
			}
			if (!empty($_POST['form-division-list-name-'.$i])) {
				$division['name']="'".escape($_POST['form-division-list-name-'.$i])."'";
			} else {
				$division['name']="NULL";
			}
			if ((!empty($_POST['form-division-list-number_of_episodes-'.$i]) && is_numeric($_POST['form-division-list-number_of_episodes-'.$i])) || $_POST['form-division-list-number_of_episodes-'.$i]==='0') {
				$division['number_of_episodes']=escape($_POST['form-division-list-number_of_episodes-'.$i]);
				$total_eps+=$_POST['form-division-list-number_of_episodes-'.$i];
			} else {
				crash("Dades invàlides: número de capítols buit o no numèric");
			}
			if (!empty($_POST['form-division-list-external_id-'.$i])) {
				$division['external_id']="'".escape($_POST['form-division-list-external_id-'.$i])."'";
			} else {
				$division['external_id']="NULL";
			}
			array_push($divisions, $division);
			$i++;
		}

		if (!empty($_POST['is_open'])){
			$data['number_of_episodes']=-1;
		} else {
			$data['number_of_episodes']=$total_eps;
		}

		$episodes=array();
		$i=1;
		while (!empty($_POST['form-episode-list-id-'.$i])) {
			$episode = array();
			if (is_numeric($_POST['form-episode-list-id-'.$i])) {
				$episode['id']=escape($_POST['form-episode-list-id-'.$i]);
			} else {
				crash("Dades invàlides: id de capítol no numèric");
			}
			if (!empty($_POST['form-episode-list-division-'.$i]) && is_numeric($_POST['form-episode-list-division-'.$i])) {
				$episode['division']=escape($_POST['form-episode-list-division-'.$i]);
			} else {
				$episode['division']="NULL";
			}
			if ((!empty($_POST['form-episode-list-num-'.$i]) || $_POST['form-episode-list-num-'.$i]=='0') && is_numeric($_POST['form-episode-list-num-'.$i])) {
				$episode['number']=escape($_POST['form-episode-list-num-'.$i]);
			} else {
				$episode['number']="NULL";
			}
			if (!empty($_POST['form-episode-list-description-'.$i])) {
				$episode['description']="'".escape($_POST['form-episode-list-description-'.$i])."'";
			} else {
				$episode['description']="NULL";
			}
			if (!empty($_POST['form-episode-list-linked_episode_id-'.$i]) && is_numeric($_POST['form-episode-list-linked_episode_id-'.$i])) {
				$episode['linked_episode_id']=escape($_POST['form-episode-list-linked_episode_id-'.$i]);
			} else {
				$episode['linked_episode_id']="NULL";
			}
			array_push($episodes, $episode);
			$i++;
		}

		$related_series=array();
		$i=1;
		while (!empty($_POST['form-related-list-related_series_id-'.$i])) {
			if (is_numeric($_POST['form-related-list-related_series_id-'.$i])) {
				array_push($related_series,escape($_POST['form-related-list-related_series_id-'.$i]));
			} else {
				crash("Dades invàlides: id d'element relacionat no numèric");
			}
			$i++;
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-series", "S'ha actualitzat la sèrie '".$data['name']."' (id. de sèrie: ".$data['id'].")");
			query("UPDATE series SET slug='".$data['slug']."',name='".$data['name']."',alternate_names=".$data['alternate_names'].",keywords=".$data['keywords'].",subtype='".$data['subtype']."',publish_date=".$data['publish_date'].",author=".$data['author'].",director=".$data['director'].",studio=".$data['studio'].",rating=".$data['rating'].",number_of_episodes=".$data['number_of_episodes'].",synopsis='".$data['synopsis']."',external_id=".$data['external_id'].",tadaima_id=".$data['tadaima_id'].",score=".$data['score'].",has_licensed_parts=".$data['has_licensed_parts'].",duration=".$data['duration'].",comic_type=".$data['comic_type'].",reader_type=".$data['reader_type'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
			query("DELETE FROM rel_series_genre WHERE series_id=".$data['id']);
			foreach ($genres as $genre) {
				query("INSERT INTO rel_series_genre (series_id,genre_id) VALUES (".$data['id'].",".$genre.")");
			}
			$ids=array();
			foreach ($divisions as $division) {
				if ($division['id']!=-1) {
					array_push($ids,$division['id']);
				}
			}
			query("DELETE FROM division WHERE series_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($divisions as $division) {
				if ($division['id']==-1) {
					query("INSERT INTO division (series_id,number,name,number_of_episodes,external_id) VALUES (".$data['id'].",".$division['number'].",".$division['name'].",".$division['number_of_episodes'].",".$division['external_id'].")");
				} else {
					query("UPDATE division SET number=".$division['number'].",name=".$division['name'].",number_of_episodes=".$division['number_of_episodes'].",external_id=".$division['external_id']." WHERE id=".$division['id']);
				}
			}
			$ids=array();
			foreach ($episodes as $episode) {
				if ($episode['id']!=-1) {
					array_push($ids,$episode['id']);
				}
			}
			//Links and episode_titles will be removed too because their FK is set to cascade
			//Views will NOT be removed in order to keep consistent stats history
			query("DELETE FROM episode WHERE series_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($episodes as $episode) {
				if ($episode['id']==-1) {
					query("INSERT INTO episode (series_id,division_id,number,description,linked_episode_id,created,created_by,updated,updated_by) VALUES (".$data['id'].",(SELECT id FROM division WHERE number=".$episode['division']." AND series_id=".$data['id']."),".$episode['number'].",".$episode['description'].",".$episode['linked_episode_id'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
				} else {
					query("UPDATE episode SET division_id=(SELECT id FROM division WHERE number=".$episode['division']." AND series_id=".$data['id']."),number=".$episode['number'].",description=".$episode['description'].",linked_episode_id=".$episode['linked_episode_id'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$episode['id']);
				}
			}
			query("DELETE FROM related_series WHERE series_id=".$data['id']." OR related_series_id=".$data['id']);
			foreach ($related_series as $related_series_id) {
				query("REPLACE INTO related_series (series_id,related_series_id) VALUES (".$data['id'].",".$related_series_id.")");
				query("REPLACE INTO related_series (series_id,related_series_id) VALUES (".$related_series_id.",".$data['id'].")");
			}

			if (is_uploaded_file($_FILES['image']['tmp_name'])) {
				move_uploaded_file($_FILES['image']["tmp_name"], STATIC_DIRECTORY.'/images/covers/'.$data['id'].'.jpg');
			} else if (!empty($_POST['image_url'])){
				copy($_POST['image_url'], STATIC_DIRECTORY.'/images/covers/'.$data['id'].'.jpg');
			}

			if (is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
				move_uploaded_file($_FILES['featured_image']["tmp_name"], STATIC_DIRECTORY.'/images/featured/'.$data['id'].'.jpg');
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}
		else {
			log_action("create-series", "S'ha creat la sèrie '".$data['name']."'");
			query("INSERT INTO series (slug,name,alternate_names,keywords,type,subtype,publish_date,author,director,studio,rating,number_of_episodes,synopsis,external_id,tadaima_id,score,has_licensed_parts,duration,comic_type,reader_type,created,created_by,updated,updated_by) VALUES ('".$data['slug']."','".$data['name']."',".$data['alternate_names'].",".$data['keywords'].",'".$data['type']."','".$data['subtype']."',".$data['publish_date'].",".$data['author'].",".$data['director'].",".$data['studio'].",".$data['rating'].",".$data['number_of_episodes'].",'".$data['synopsis']."',".$data['external_id'].",".$data['tadaima_id'].",".$data['score'].",".$data['has_licensed_parts'].",".$data['duration'].",".$data['comic_type'].",".$data['reader_type'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			$inserted_id=mysqli_insert_id($db_connection);
			foreach ($genres as $genre) {
				query("INSERT INTO rel_series_genre (series_id,genre_id) VALUES (".$inserted_id.",".$genre.")");
			}
			foreach ($divisions as $division) {
				query("INSERT INTO division (series_id,number,name,number_of_episodes,external_id) VALUES (".$inserted_id.",".$division['number'].",".$division['name'].",".$division['number_of_episodes'].",".$division['external_id'].")");
			}
			foreach ($episodes as $episode) {
				query("INSERT INTO episode (series_id,division_id,number,description,linked_episode_id,created,created_by,updated,updated_by) VALUES (".$inserted_id.",(SELECT id FROM division WHERE number=".$episode['division']." AND series_id=".$inserted_id."),".$episode['number'].",".$episode['description'].",".$episode['linked_episode_id'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			}
			foreach ($related_series as $related_series_id) {
				query("INSERT INTO related_series (series_id,related_series_id) VALUES (".$inserted_id.",".$related_series_id.")");
				query("INSERT INTO related_series (series_id,related_series_id) VALUES (".$related_series_id.",".$inserted_id.")");
			}

			if (is_uploaded_file($_FILES['image']['tmp_name'])) {
				move_uploaded_file($_FILES['image']["tmp_name"], STATIC_DIRECTORY.'/images/covers/'.$inserted_id.'.jpg');
			} else if (!empty($_POST['image_url'])){
				copy($_POST['image_url'],STATIC_DIRECTORY.'/images/covers/'.$inserted_id.'.jpg');
			}

			if (is_uploaded_file($_FILES['featured_image']['tmp_name'])) {
				move_uploaded_file($_FILES['featured_image']["tmp_name"], STATIC_DIRECTORY.'/images/featured/'.$inserted_id.'.jpg');
			}

			$_SESSION['message']="S'han desat les dades correctament.<br /><a class=\"btn btn-primary mt-2\" href=\"version_edit.php?type=$type&series_id=$inserted_id\"><span class=\"fa fa-plus pe-2\"></span>Crea'n una versió</a>";
		}

		header("Location: series_list.php?type=$type");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT s.* FROM series s WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Series not found');
		mysqli_free_result($result);
		if ($row['type']!=$type) {
			crash('Wrong type specified');
		}

		$resultg = query("SELECT sg.* FROM rel_series_genre sg WHERE sg.series_id=".escape($_GET['id']));
		$genres = array();
		while ($rowg = mysqli_fetch_assoc($resultg)) {
			array_push($genres, $rowg['genre_id']);
		}
		mysqli_free_result($resultg);

		$resultd = query("SELECT d.* FROM division d WHERE d.series_id=".escape($_GET['id'])." ORDER BY d.number ASC");
		$divisions = array();
		while ($rowd = mysqli_fetch_assoc($resultd)) {
			array_push($divisions, $rowd);
		}
		mysqli_free_result($resultd);

		$resulte = query("SELECT e.*,d.number division, EXISTS(SELECT * FROM file f WHERE f.episode_id=e.id AND f.is_lost=0) has_version FROM episode e LEFT JOIN division d ON e.division_id=d.id WHERE e.series_id=".escape($_GET['id'])." ORDER BY d.number IS NULL ASC, d.number ASC, e.number IS NULL ASC, e.number ASC, e.description ASC");
		$episodes = array();
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $rowe);
		}
		mysqli_free_result($resulte);
	} else {
		$row = array();
		$genres = array();
		$divisions = array();
		$episodes = array();
		$row['has_licensed_parts']=0;
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita ".$content_apos : "Afegeix ".$content_one; ?></h4>
					<hr>
					<form method="post" action="series_edit.php?type=<?php echo $type; ?>" enctype="multipart/form-data" onsubmit="return checkNumberOfEpisodes()">
						<div class="row align-items-end">
							<div class="col-sm-3">
								<div class="mb-3">
									<label for="form-external_id">Identificador de <?php echo $external_provider; ?></label>
									<input class="form-control" name="external_id" id="form-external_id"<?php echo ($type!='liveaction' ? ' type="number"' : ''); ?> value="<?php echo $row['external_id']; ?>">
								</div>
							</div>
							<div class="col-sm mb-3">
<?php
	if ($type!='liveaction') {
?>
								<button type="button" id="import-from-mal" class="btn btn-primary">
									<span id="import-from-mal-loading" class="d-none spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
									<span id="import-from-mal-not-loading" class="fa fa-list pe-2"></span>Importa la fitxa de MyAnimeList
								</button>
<?php
	}
?>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-tadaima_id">Identificador de fil a Tadaima.cat</label>
									<input class="form-control" name="tadaima_id" id="form-tadaima_id" type="number" value="<?php echo $row['tadaima_id']; ?>">
								</div>
							</div>
						</div>
						<div id="import-from-mal-done" class="col-sm mb-3 alert alert-warning d-none">
							<span class="fa fa-exclamation-triangle pe-2"></span>S'ha importat la fitxa de MyAnimeList. Revisa que les dades siguin correctes i tradueix-ne la sinopsi i el nom, si s'escau.
						</div>
						<hr />
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-name-with-autocomplete" class="mandatory">Nom</label>
									<input class="form-control" name="name" id="form-name-with-autocomplete" required maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['name'])); ?>">
									<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
									<input type="hidden" id="type" value="<?php echo $type; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-slug">Identificador<span class="mandatory"></span> <small class="text-muted">(autogenerat, no cal editar-lo)</small></label>
									<input class="form-control" name="slug" id="form-slug" required maxlength="200" value="<?php echo htmlspecialchars($row['slug']); ?>">
									<input type="hidden" id="form-old_slug" value="<?php echo htmlspecialchars($row['slug']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-air_status">Estat</label>
									<div id="form-air_status">
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="is_open" id="form-is_open" value="1"<?php echo $row['number_of_episodes']==-1? " checked" : ""; ?>>
											<label class="form-check-label" for="form-is_open"><?php echo $open_series; ?></label>
										</div>
										<div class="form-check form-check-inline">
											<input class="form-check-input" type="checkbox" name="has_licensed_parts" id="form-has_licensed_parts" value="1"<?php echo $row['has_licensed_parts']==1 ? " checked" : ""; ?>>
											<label class="form-check-label" for="form-has_licensed_parts">Té parts llicenciades</label>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-8">
								<div class="mb-3">
									<label for="form-alternate_names">Altres noms</label>
									<input class="form-control" name="alternate_names" id="form-alternate_names" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['alternate_names'])); ?>">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-score">Puntuació a <?php echo $external_provider; ?></label>
									<input class="form-control" name="score" id="form-score" type="number" value="<?php echo $row['score']; ?>" step=".01">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-12">
								<div class="mb-3">
									<label for="form-keywords">Paraules clau <small class="text-muted">(separades per espais; que no siguin ja al nom o noms alternatius, s'utilitza per a la cerca)</small></label>
									<input class="form-control" name="keywords" id="form-keywords" maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['keywords'])); ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-type" class="mandatory">Tipus</label>
									<select class="form-select" name="subtype" id="form-subtype" required>
										<option value="">- Selecciona un tipus -</option>
<?php
	if ($type=='manga') {
?>
										<option value="oneshot"<?php echo $row['subtype']=='oneshot' ? " selected" : ""; ?>>One-shot</option>
										<option value="serialized"<?php echo $row['subtype']=='serialized' ? " selected" : ""; ?>>Serialitzat</option>
<?php
	} else {
?>
										<option value="movie"<?php echo $row['subtype']=='movie' ? " selected" : ""; ?>>Film</option>
										<option value="series"<?php echo $row['subtype']=='series' ? " selected" : ""; ?>>Sèrie</option>
<?php
	}
?>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-publish_date">Data d'estrena</label>
									<input class="form-control" name="publish_date" type="date" id="form-publish_date" maxlength="200" value="<?php echo !empty($row['publish_date']) ? date('Y-m-d', strtotime($row['publish_date'])) : ""; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-author">Autor</label>
									<input class="form-control" name="author" id="form-author" maxlength="200" value="<?php echo htmlspecialchars($row['author']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-rating" class="mandatory">Valoració per edats</label>
									<select class="form-select" name="rating" id="form-rating" required>
										<option value="">- Selecciona una valoració -</option>
										<option value="TP"<?php echo $row['rating']=='TP' ? " selected" : ""; ?>>Tots els públics</option>
										<option value="+7"<?php echo $row['rating']=='+7' ? " selected" : ""; ?>>Majors de 7 anys</option>
										<option value="+13"<?php echo $row['rating']=='+13' ? " selected" : ""; ?>>Majors de 13 anys</option>
										<option value="+16"<?php echo $row['rating']=='+16' ? " selected" : ""; ?>>Majors de 16 anys</option>
										<option value="+18"<?php echo $row['rating']=='+18' ? " selected" : ""; ?>>Majors de 18 anys</option>
										<option value="XXX"<?php echo $row['rating']=='XXX' ? " selected" : ""; ?>>Contingut pornogràfic</option>
									</select>
								</div>
							</div>
						</div>
<?php
	if ($type=='manga') {
?>
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-comic_type" class="mandatory">Tipus de còmic</label>
									<select class="form-select" name="comic_type" id="form-comic_type" required>
										<option value="manga"<?php echo $row['comic_type']=='manga' ? " selected" : ""; ?>>Manga (còmic japonès)</option>
										<option value="manhwa"<?php echo $row['comic_type']=='manhwa' ? " selected" : ""; ?>>Manhwa (còmic coreà)</option>
										<option value="manhua"<?php echo $row['comic_type']=='manhua' ? " selected" : ""; ?>>Manhua (còmic xinès)</option>
										<option value="other"<?php echo $row['comic_type']=='other' ? " selected" : ""; ?>>Altres</option>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-reader_type" class="mandatory">Tipus de lector</label>
									<select class="form-select" name="reader_type" id="form-reader_type" required>
										<option value="paged"<?php echo $row['reader_type']!='strip' ? " selected" : ""; ?>>Paginat (lectura normal pàgina a pàgina)</option>
										<option value="strip"<?php echo $row['reader_type']=='strip' ? " selected" : ""; ?>>Webtoon (lectura per desplaçament, tira llarga)</option>
									</select>
								</div>
							</div>
						</div>
<?php
	} else {
?>
						<div class="row">
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-director">Director</label>
									<input class="form-control" name="director" id="form-director" maxlength="200" value="<?php echo htmlspecialchars($row['director']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="mb-3">
									<label for="form-studio">Estudi</label>
									<input class="form-control" name="studio" id="form-studio" maxlength="200" value="<?php echo htmlspecialchars($row['studio']); ?>">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<label for="form-duration">Durada</label>
									<input class="form-control" name="duration" id="form-duration" maxlength="200" value="<?php echo htmlspecialchars($row['duration']); ?>">
								</div>
							</div>
						</div>
<?php
	}
?>
						<div class="mb-3">
							<label for="form-synopsis">Sinopsi<span class="mandatory"></span> <small class="text-muted">(admet <a href="https://www.markdownguide.org/cheat-sheet/" target="_blank">Markdown</a>)</small></label>
							<textarea class="form-control" name="synopsis" id="form-synopsis" required style="height: 150px;"><?php echo htmlspecialchars(str_replace('&#039;',"'",html_entity_decode($row['synopsis']))); ?></textarea>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="mb-3">
									<label>Imatge de portada<?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?><br><small class="text-muted">(JPEG, ~300x424, ≤450x600, ≤150 KiB)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/covers/'.$row['id'].'.jpg');
?>
									<label for="form-image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
									<input class="form-control d-none" name="image" type="file" id="form-image" accept="image/jpeg" value="" onchange="checkImageUpload(this, 153600, 'form-image-preview', 'form-image-preview-link','form-image_url');">
									<input class="form-control" name="image_url" type="hidden" id="form-image_url" value="">
								</div>
							</div>
							<div class="col-sm-1">
								<div class="mb-3">
									<a id="form-image-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/covers/'.$row['id'].'.jpg" data-original="'.STATIC_URL.'/images/covers/'.$row['id'].'.jpg"' : ''; ?> target="_blank">
										<img id="form-image-preview" style="width: 64px; height: 90px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/covers/'.$row['id'].'.jpg" data-original="'.STATIC_URL.'/images/covers/'.$row['id'].'.jpg"' : ''; ?> alt="">
									</a>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="mb-3">
									<label>Imatge de capçalera<?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?><br><small class="text-muted">(JPEG, ~1104x256, ≤1200x400, ≤300 KiB)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/featured/'.$row['id'].'.jpg');
?>
									<label for="form-featured_image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
									<input class="d-none" name="featured_image" type="file" accept="image/jpeg" id="form-featured_image" onchange="checkImageUpload(this, 307200, 'form-featured-image-preview', 'form-featured-image-preview-link');">
								</div>
							</div>
							<div class="col-sm-4">
								<div class="mb-3">
									<a id="form-featured-image-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/featured/'.$row['id'].'.jpg" data-original="'.STATIC_URL.'/images/featured/'.$row['id'].'.jpg"' : ''; ?> target="_blank">
										<img id="form-featured-image-preview" style="width: 400px; height: 85px; object-fit: cover; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/featured/'.$row['id'].'.jpg" data-original="'.STATIC_URL.'/images/featured/'.$row['id'].'.jpg"' : ''; ?> alt="">
									</a>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-demographics">Demografia</label>
							<div id="form-demographics" class="row ps-3 pe-3">
<?php
	$resultg = query("SELECT g.* FROM genre g WHERE type='demographics' ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-2">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-external-id="<?php echo $type=='manga' ? $rowg['external_id_manga'] : $rowg['external_id_anime']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-genres">Gèneres</label>
							<div id="form-genres" class="row ps-3 pe-3">
<?php
	$resultg = query("SELECT g.* FROM genre g WHERE type='genre' ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-2">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-external-id="<?php echo $type=='manga' ? $rowg['external_id_manga'] : $rowg['external_id_anime']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-explicit">Nivell d'erotisme <small>(si n'hi ha; marca només el més alt; ecchi &lt; erotisme &lt; hentai)</small></label>
							<div id="form-explicit" class="row ps-3 pe-3">
<?php
	$resultg = query("SELECT g.* FROM genre g WHERE type='explicit' ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-2">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-external-id="<?php echo $type=='manga' ? $rowg['external_id_manga'] : $rowg['external_id_anime']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-themes">Temàtiques</label>
							<div id="form-themes" class="row ps-3 pe-3">
<?php
	$resultg = query("SELECT g.* FROM genre g WHERE type='theme' ORDER BY g.name");
	while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
								<div class="form-check col-sm-3">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-external-id="<?php echo $type=='manga' ? $rowg['external_id_manga'] : $rowg['external_id_anime']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-division-list"><?php echo $division_name; ?></label>
							<div class="container" id="form-division-list">
								<div class="row">
									<div class="w-100 column">
										<table class="table table-bordered table-hover table-sm" id="division-list-table" data-count="<?php echo max(count($divisions),1); ?>">
											<thead>
												<tr>
													<th style="width: 10%;" class="mandatory">Núm.</th>
													<th>Nom <small class="text-muted">(es mostra si n'hi ha més <?php echo $more_than_one; ?>; si està informat, no s'hi afegeix «<?php echo $division_name_singular; ?> X»)</small></th>
													<th class="mandatory" style="width: 15%;">Capítols</th>
													<th style="width: 15%;">Id. <?php echo $external_provider; ?></th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
<?php
	for ($i=0;$i<count($divisions);$i++) {
?>
												<tr id="form-division-list-row-<?php echo $i+1; ?>">
													<td>
														<input id="form-division-list-number-<?php echo $i+1; ?>" name="form-division-list-number-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $divisions[$i]['number']!=NULL ? floatval($divisions[$i]['number']) : ''; ?>" step="any" required/>
														<input id="form-division-list-id-<?php echo $i+1; ?>" name="form-division-list-id-<?php echo $i+1; ?>" type="hidden" value="<?php echo $divisions[$i]['id']; ?>"/>
													</td>
													<td>
														<input id="form-division-list-name-<?php echo $i+1; ?>" name="form-division-list-name-<?php echo $i+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($divisions[$i]['name']); ?>" placeholder="(Sense nom)"/>
													</td>
													<td>
														<input id="form-division-list-number_of_episodes-<?php echo $i+1; ?>" name="form-division-list-number_of_episodes-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $divisions[$i]['number_of_episodes']; ?>" required/>
													</td>
													<td>
														<input id="form-division-list-external_id-<?php echo $i+1; ?>" name="form-division-list-external_id-<?php echo $i+1; ?>"<?php echo ($type!='liveaction' ? ' type="number"' : ''); ?> class="form-control" value="<?php echo $divisions[$i]['external_id']; ?>"/>
													</td>
													<td class="text-center align-middle">
														<button id="form-division-list-delete-<?php echo $i+1; ?>" onclick="deleteDivisionRow(<?php echo $i+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
	if (count($divisions)==0) {
?>
												<tr id="form-division-list-row-1">
													<td>
														<input id="form-division-list-number-1" name="form-division-list-number-1" type="number" class="form-control" value="1" step="any" required/>
														<input id="form-division-list-id-1" name="form-division-list-id-1" type="hidden" value="-1"/>
													</td>
													<td>
														<input id="form-division-list-name-1" name="form-division-list-name-1" type="text" class="form-control" value="" placeholder="(Sense nom)"/>
													</td>
													<td>
														<input id="form-division-list-number_of_episodes-1" name="form-division-list-number_of_episodes-1" type="number" class="form-control" value="" required/>
													</td>
													<td>
														<input id="form-division-list-external_id-1" name="form-division-list-external_id-1"<?php echo ($type!='liveaction' ? ' type="number"' : ''); ?> class="form-control" value=""/>
													</td>
													<td class="text-center align-middle">
														<button id="form-division-list-delete-1" onclick="deleteDivisionRow(1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="w-100 text-center"><button onclick="addDivisionRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix <?php echo $division_one; ?></button></div>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-episode-list">Capítols</label>
							<div id="import-from-mal-episodes-done" class="col-sm mb-3 alert alert-warning d-none">
								<span class="fa fa-exclamation-triangle pe-2"></span>S'han importat els capítols de MyAnimeList. Revisa'n tots els camps.
							</div>
							<div class="container" id="form-episode-list">
								<div class="row">
									<div class="w-100 column">
<?php
		if ($type!='manga') {
?>
										<select id="form-episode-list-linked_episode_id-XXX" name="form-episode-list-linked_episode_id-XXX" class="form-select d-none">
											<option value="">- Selecciona un film extern -</option>
<?php
			$resultle = query("SELECT e.id, CONCAT(s.name, ' - ', IF(e.division_id IS NULL,'Altres',IFNULL(d.name,CONCAT('Temporada ',TRIM(d.number)+0))), ' - ', IF(e.number IS NULL,'Extra',CONCAT('Capítol ',TRIM(e.number)+0)),IF(e.description IS NULL,'',CONCAT(': ', e.description))) description FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN series s ON e.series_id=s.id WHERE s.type='$type' AND s.subtype='movie' ORDER BY s.name, d.number, e.number IS NULL ASC, e.number ASC, e.description");
			while ($lerow = mysqli_fetch_assoc($resultle)) {
?>
											<option value="<?php echo $lerow['id']; ?>"><?php echo htmlspecialchars($lerow['description']); ?></option>
<?php
			}
			mysqli_free_result($resultle);
?>
										</select>
<?php
		}
?>
										<table class="table table-bordered table-hover table-sm" id="episode-list-table" data-count="<?php echo max(count($episodes),1); ?>">
											<thead>
												<tr>
													<th style="width: 10%;"><?php echo $division_name_short; ?></th>
													<th style="width: 10%;">Núm.</th>
													<th>Descripció <small class="text-muted">(només informativa, només es mostra públicament en especials si no tenen títol específic a la versió)</small></th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
<?php
	$some_has_version = FALSE;
	for ($i=0;$i<count($episodes);$i++) {
		if ($episodes[$i]['has_version']) {
			$some_has_version = TRUE;
		}
?>
												<tr id="form-episode-list-row-<?php echo $i+1; ?>">
													<td>
														<input id="form-episode-list-division-<?php echo $i+1; ?>" name="form-episode-list-division-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $episodes[$i]['division']!=NULL ? floatval($episodes[$i]['division']) : ''; ?>" placeholder="(Altres)" step="any"/>
													</td>
													<td>
														<input id="form-episode-list-num-<?php echo $i+1; ?>" name="form-episode-list-num-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $episodes[$i]['number']!=NULL ? floatval($episodes[$i]['number']) : ''; ?>" placeholder="(Esp.)" step="any"/>
														<input id="form-episode-list-id-<?php echo $i+1; ?>" name="form-episode-list-id-<?php echo $i+1; ?>" type="hidden" value="<?php echo $episodes[$i]['id']; ?>"/>
														<input id="form-episode-list-has_version-<?php echo $i+1; ?>" type="hidden" value="<?php echo $episodes[$i]['has_version']; ?>"/>
													</td>
													<td>
<?php
		if (!empty($episodes[$i]['linked_episode_id'])) {
?>
														<select id="form-episode-list-linked_episode_id-<?php echo $i+1; ?>" name="form-episode-list-linked_episode_id-<?php echo $i+1; ?>" class="form-select" required>
															<option value="">- Selecciona un film extern -</option>
<?php
			$resultle = query("SELECT e.id, CONCAT(s.name, ' - ', IF(e.division_id IS NULL,'Altres',IFNULL(d.name,CONCAT('Temporada ',TRIM(d.number)+0))), ' - ', IF(e.number IS NULL,'Extra',CONCAT('Capítol ',TRIM(e.number)+0)),IF(e.description IS NULL,'',CONCAT(': ', e.description))) description FROM episode e LEFT JOIN division d ON e.division_id=d.id LEFT JOIN series s ON e.series_id=s.id WHERE s.type='$type' AND s.subtype='movie' ORDER BY s.name, d.number, e.number IS NULL ASC, e.number ASC, e.description");
			while ($lerow = mysqli_fetch_assoc($resultle)) {
?>
															<option value="<?php echo $lerow['id']; ?>"<?php echo $episodes[$i]['linked_episode_id']==$lerow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($lerow['description']); ?></option>
<?php
			}
			mysqli_free_result($resultle);
?>
														</select>
<?php
		} else {
?>
														<input id="form-episode-list-description-<?php echo $i+1; ?>" name="form-episode-list-description-<?php echo $i+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($episodes[$i]['description']); ?>" placeholder="(Sense descripció)"/>
<?php
		}
?>
													</td>
													<td class="text-center align-middle">
														<button id="form-episode-list-delete-<?php echo $i+1; ?>" onclick="deleteEpìsodeRow(<?php echo $i+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger<?php echo $episodes[$i]['has_version'] ? ' disabled' : ''; ?>"></button>
													</td>
												</tr>
<?php
	}
	if (count($episodes)==0) {
?>
												<tr id="form-episode-list-row-1">
													<td>
														<input id="form-episode-list-division-1" name="form-episode-list-division-1" type="number" class="form-control" value="1" placeholder="(Altres)" step="any"/>
													</td>
													<td>
														<input id="form-episode-list-num-1" name="form-episode-list-num-1" type="number" class="form-control" value="1" placeholder="(Esp.)" step="any"/>
														<input id="form-episode-list-id-1" name="form-episode-list-id-1" type="hidden" value="-1"/>
														<input id="form-episode-list-has_version-1" type="hidden" value="0"/>
													</td>
													<td>
														<input id="form-episode-list-description-1" name="form-episode-list-description-1" type="text" class="form-control" value="" placeholder="(Sense descripció)"/>
													</td>
													<td class="text-center align-middle">
														<button id="form-episode-list-delete-1" onclick="deleteEpìsodeRow(1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="d-flex">
										<button onclick="addEpisodeRow(false, false);" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix capítol</button>
										<button onclick="addEpisodeRow(true, false);" type="button" class="btn btn-success btn-sm ms-2"><span class="fa fa-plus pe-2"></span>Afegeix especial</button>
<?php
	if ($type!='manga') {
?>
										<button onclick="addEpisodeRow(true, true);" type="button" class="btn btn-success btn-sm ms-2"><span class="fa fa-plus pe-2"></span>Afegeix film enllaçat</button>
<?php
	}
?>
										<span style="flex-grow: 1;"></span>
<?php
	if ($type=='anime') {
?>
										<button type="button" id="import-from-mal-episodes" class="btn btn-primary btn-sm<?php echo $some_has_version ? ' disabled' : ''; ?>">
											<span id="import-from-mal-episodes-loading" class="d-none spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
											<span id="import-from-mal-episodes-not-loading" class="fa fa-list pe-2"></span>
											Importa els capítols de MyAnimeList
										</button>
<?php
	}
?>
										<button type="button" id="generate-episodes" class="btn btn-primary btn-sm ms-2<?php echo $some_has_version ? ' disabled' : ''; ?>">
											<span class="fa fa-sort-numeric-down pe-2"></span>
											Genera els capítols automàticament
										</button>
									</div>
								</div>
							</div>
						</div>
						<div class="mb-3">
							<label for="form-related-list">Contingut relacionat <small class="text-muted">(es mostrarà a la fitxa pública)</small></label>
							<div class="container" id="form-related-list">
<?php

	if (!empty($row['id'])) {
		$resultrs = query("SELECT DISTINCT t.series_id, CONCAT(s.type,' - ',s.name) name FROM (SELECT rs.related_series_id series_id FROM related_series rs WHERE rs.series_id=".escape($_GET['id'])." UNION SELECT rs.series_id series_id FROM related_series rs WHERE rs.related_series_id=".escape($_GET['id']).") t LEFT JOIN series s ON s.id=t.series_id ORDER BY s.type ASC, s.name ASC");
		$related_series = array();
		while ($rowrs = mysqli_fetch_assoc($resultrs)) {
			array_push($related_series, $rowrs);
		}
		mysqli_free_result($resultrs);
	} else {
		$related_series=array();
	}
?>
								<div class="row mb-3">
									<div class="w-100 column">
										<select id="form-related-list-related_series_id-XXX" name="form-related-list-related_series_id-XXX" class="form-select d-none">
											<option value="">- Selecciona un element -</option>
<?php
		$results = query("SELECT s.id, CONCAT(IF(s.type='anime','Anime',IF(s.type='manga','Manga','Acció real')),' - ',s.name) name FROM series s WHERE id<>".(!empty($row['id']) ? $row['id'] : -1)." ORDER BY s.type ASC, s.name ASC");
		while ($srow = mysqli_fetch_assoc($results)) {
?>
											<option value="<?php echo $srow['id']; ?>"><?php echo htmlspecialchars($srow['name']); ?></option>
<?php
		}
		mysqli_free_result($results);
?>
										</select>
										<table class="table table-bordered table-hover table-sm" id="related-list-table" data-count="<?php echo count($related_series); ?>">
											<thead>
												<tr>
													<th class="mandatory">Element</th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
												<tr id="related-list-table-empty" class="<?php echo count($related_series)>0 ? 'd-none' : ''; ?>">
													<td colspan="2" class="text-center">- No hi ha cap element relacionat -</td>
												</tr>
<?php
	for ($j=0;$j<count($related_series);$j++) {
?>
												<tr id="form-related-list-row-<?php echo $j+1; ?>">
													<td>
														<select id="form-related-list-related_series_id-<?php echo $j+1; ?>" name="form-related-list-related_series_id-<?php echo $j+1; ?>" class="form-select" required>
															<option value="">- Selecciona un element -</option>
<?php
		$results = query("SELECT s.id, CONCAT(IF(s.type='anime','Anime',IF(s.type='manga','Manga','Acció real')),' - ',s.name) name FROM series s WHERE id<>".(!empty($row['id']) ? $row['id'] : -1)." ORDER BY s.type ASC, s.name ASC");
		while ($srow = mysqli_fetch_assoc($results)) {
?>
															<option value="<?php echo $srow['id']; ?>"<?php echo $related_series[$j]['series_id']==$srow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($srow['name']); ?></option>
<?php
		}
		mysqli_free_result($results);
?>
														</select>
													</td>
													<td class="text-center align-middle">
														<button id="form-related-list-delete-<?php echo $j+1; ?>" onclick="deleteRelatedSeriesRow(<?php echo $j+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
?>
											</tbody>
										</table>
									</div>
									<div class="mb-3 row w-100 ms-0">
										<div class="col-sm text-center" style="padding-left: 0; padding-right: 0">
											<button onclick="addRelatedSeriesRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pe-2"></span>Afegeix un element relacionat</button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="mb-3 text-center pt-2">
							<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix ".$content_apos; ?></button>
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
