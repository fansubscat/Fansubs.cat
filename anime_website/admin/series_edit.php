<?php
$header_title="Sèries";
$page="series";
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
		if (!empty($_POST['score']) && is_numeric($_POST['score'])) {
			$data['score']=escape($_POST['score']);
		} else {
			$data['score']="NULL";
		}
		if (!empty($_POST['alternate_names'])) {
			$data['alternate_names']="'".escape($_POST['alternate_names'])."'";
		} else {
			$data['alternate_names']="NULL";
		}
		if (!empty($_POST['type'])) {
			$data['type']=escape($_POST['type']);
		} else {
			crash("Dades invàlides: manca type");
		}
		if (!empty($_POST['air_date'])) {
			$data['air_date']="'".date('Y-m-d H:i:s', strtotime($_POST['air_date']))."'";
		} else {
			$data['air_date']="NULL";
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
		if (!empty($_POST['duration'])) {
			$data['duration']="'".escape($_POST['duration'])."'";
		} else {
			$data['duration']="NULL";
		}
		if (!empty($_POST['episodes']) && is_numeric($_POST['episodes'])) {
			$data['episodes']=escape($_POST['episodes']);
		} else if (!empty($_POST['is_open'])){
			$data['episodes']=-1;
		} else {
			crash("Dades invàlides: manca episodes");
		}
		if (!empty($_POST['image'])) {
			$data['image']=escape($_POST['image']);
		} else {
			crash("Dades invàlides: manca image");
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

		$episodes=array();
		$i=1;
		while (!empty($_POST['form-episode-list-id-'.$i])) {
			$episode = array();
			if (is_numeric($_POST['form-episode-list-id-'.$i])) {
				$episode['id']=escape($_POST['form-episode-list-id-'.$i]);
			} else {
				crash("Dades invàlides: id de capítol no numèric");
			}
			if (!empty($_POST['form-episode-list-num-'.$i]) && is_numeric($_POST['form-episode-list-num-'.$i])) {
				$episode['number']=escape($_POST['form-episode-list-num-'.$i]);
			} else {
				$episode['number']="NULL";
			}
			if (!empty($_POST['form-episode-list-name-'.$i])) {
				$episode['name']="'".escape($_POST['form-episode-list-name-'.$i])."'";
			} else {
				$episode['name']="NULL";
			}
			if (!empty($_POST['form-episode-list-date-'.$i])) {
				$episode['date']="'".date('Y-m-d H:i:s', strtotime($_POST['form-episode-list-date-'.$i]))."'";
			} else {
				$episode['date']="NULL";
			}
			array_push($episodes, $episode);
			$i++;
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-series", "S'ha actualitzat la sèrie amb nom '".$data['name']."' (id. de sèrie: ".$data['id'].")");
			query("UPDATE series SET slug='".$data['slug']."',name='".$data['name']."',alternate_names=".$data['alternate_names'].",score=".$data['score'].",type='".$data['type']."',air_date=".$data['air_date'].",author=".$data['author'].",director=".$data['director'].",studio=".$data['studio'].",rating=".$data['rating'].",episodes=".$data['episodes'].",synopsis='".$data['synopsis']."',duration=".$data['duration'].",image='".$data['image']."',myanimelist_id=".$data['myanimelist_id'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
			query("DELETE FROM rel_series_genre WHERE series_id=".$data['id']);
			foreach ($genres as $genre) {
				query("INSERT INTO rel_series_genre (series_id,genre_id) VALUES (".$data['id'].",".$genre.")");
			}
			$ids=array();
			foreach ($episodes as $episode) {
				if ($episode['id']!=-1) {
					array_push($ids,$episode['id']);
				}
			}
			//Links and episode_titles will be removed too because their FK is set to cascade
			query("DELETE FROM episode WHERE series_id=".$data['id']." AND id NOT IN (".(count($ids)>0 ? implode(',',$ids) : "-1").")");
			foreach ($episodes as $episode) {
				if ($episode['id']==-1) {
					query("INSERT INTO episode (series_id,number,name,date) VALUES (".$data['id'].",".$episode['number'].",".$episode['name'].",".$episode['date'].")");
				} else {
					query("UPDATE episode SET number=".$episode['number'].",name=".$episode['name'].",date=".$episode['date']." WHERE id=".$episode['id']);
				}
			}

			$_SESSION['message']="S'han desat les dades correctament.";
		}
		else {
			log_action("create-series", "S'ha creat una sèrie amb nom '".$data['name']."'");
			query("INSERT INTO series (slug,name,alternate_names,type,air_date,author,director,studio,rating,episodes,synopsis,duration,image,myanimelist_id,score,created,created_by,updated,updated_by) VALUES ('".$data['slug']."','".$data['name']."',".$data['alternate_names'].",'".$data['type']."',".$data['air_date'].",".$data['author'].",".$data['director'].",".$data['studio'].",".$data['rating'].",".$data['episodes'].",'".$data['synopsis']."',".$data['duration'].",'".$data['image']."',".$data['myanimelist_id'].",".$data['score'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			$inserted_id=mysqli_insert_id($db_connection);
			foreach ($genres as $genre) {
				query("INSERT INTO rel_series_genre (series_id,genre_id) VALUES (".$inserted_id.",".$genre.")");
			}
			foreach ($episodes as $episode) {
				query("INSERT INTO episode (series_id,number,name,date) VALUES (".$inserted_id.",".$episode['number'].",".$episode['name'].",".$episode['date'].")");
			}

			$_SESSION['message']="S'han desat les dades correctament.<br /><a class=\"btn btn-primary mt-2\" href=\"version_edit.php?series_id=$inserted_id\"><span class=\"fa fa-plus pr-2\"></span>Crea'n una versió</a>";
		}

		header("Location: series_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT s.* FROM series s WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Series not found');
		mysqli_free_result($result);

		$resultg = query("SELECT sg.* FROM rel_series_genre sg WHERE sg.series_id=".escape($_GET['id']));
		$genres = array();
		while ($rowg = mysqli_fetch_assoc($resultg)) {
			array_push($genres, $rowg['genre_id']);
		}
		mysqli_free_result($resultg);

		$resulte = query("SELECT e.* FROM episode e WHERE e.series_id=".escape($_GET['id'])." ORDER BY e.number IS NULL ASC, e.number ASC, e.name ASC");
		$episodes = array();
		while ($rowe = mysqli_fetch_assoc($resulte)) {
			array_push($episodes, $rowe);
		}
		mysqli_free_result($resulte);
	} else {
		$row = array();
		$genres = array();
		$episodes = array();
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita la sèrie" : "Afegeix una sèrie"; ?></h4>
					<hr>
					<form method="post" action="series_edit.php" onsubmit="return checkNumberOfEpisodes()">
						<div class="row align-items-end">
							<div class="col-sm-3">
								<div class="form-group">
									<label for="form-myanimelist_id">Identificador de MyAnimeList</label>
									<input class="form-control" name="myanimelist_id" id="form-myanimelist_id" type="number" maxlength="200" value="<?php echo $row['myanimelist_id']; ?>">
								</div>
							</div>
							<div class="col-sm form-group">
								<button type="button" id="import-from-mal" class="btn btn-primary">
									<span id="import-from-mal-loading" class="d-none spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
									<span id="import-from-mal-not-loading" class="fa fa-th-list pr-2"></span>Importa la fitxa de MyAnimeList
								</button>
							</div>
						</div>
						<div id="import-from-mal-done" class="col-sm form-group alert alert-warning d-none">
							S'ha importat la fitxa de MyAnimeList. Revisa que les dades siguin correctes i tradueix-ne la sinopsi i el nom, si s'escau.
						</div>
						<hr />
						<div class="row">
							<div class="col-sm">
								<div class="form-group">
									<label for="form-name-with-autocomplete">Nom</label>
									<input class="form-control" name="name" id="form-name-with-autocomplete" required maxlength="200" value="<?php echo htmlspecialchars(html_entity_decode($row['name'])); ?>">
									<input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-slug">Identificador <small class="text-muted">(autogenerat, normalment no cal editar-lo)</small></label>
									<input class="form-control" name="slug" id="form-slug" required maxlength="200" value="<?php echo htmlspecialchars($row['slug']); ?>">
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
							<div class="col-sm">
								<div class="form-group">
									<label for="form-type">Tipus</label>
									<select class="form-control" name="type" id="form-type" required>
										<option value="">- Selecciona un tipus -</option>
										<option value="movie"<?php echo $row['type']=='movie' ? " selected" : ""; ?>>Film</option>
										<option value="series"<?php echo $row['type']=='series' ? " selected" : ""; ?>>Sèrie</option>
									</select>
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-air_date">Data d'estrena</label>
									<input class="form-control" name="air_date" type="date" id="form-air_date" maxlength="200" value="<?php echo !empty($row['air_date']) ? date('Y-m-d', strtotime($row['air_date'])) : ""; ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-author">Autor</label>
									<input class="form-control" name="author" id="form-author" maxlength="200" value="<?php echo htmlspecialchars($row['author']); ?>">
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm">
								<div class="form-group">
									<label for="form-director">Director</label>
									<input class="form-control" name="director" id="form-director" maxlength="200" value="<?php echo htmlspecialchars($row['director']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-studio">Estudi</label>
									<input class="form-control" name="studio" id="form-studio" maxlength="200" value="<?php echo htmlspecialchars($row['studio']); ?>">
								</div>
							</div>
							<div class="col-sm">
								<div class="form-group">
									<label for="form-rating">Valoració per edats</label>
									<select class="form-control" name="rating" id="form-rating">
										<option value="">- Sense valoració -</option>
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
							<label for="form-synopsis">Sinopsi</label>
							<textarea class="form-control" name="synopsis" id="form-synopsis" required style="height: 150px;"><?php echo htmlspecialchars(str_replace('&#039;',"'",html_entity_decode($row['synopsis']))); ?></textarea>
						</div>
						<div class="row">
							<div class="col-sm-4">
								<div class="form-group">
									<label for="form-duration">Durada</label>
									<input class="form-control" name="duration" id="form-duration" maxlength="200" value="<?php echo htmlspecialchars($row['duration']); ?>">
								</div>
							</div>
							<div class="col-sm-7">
								<div class="form-group">
									<label for="form-image">URL de la imatge de portada</label>
									<input class="form-control" name="image" type="url" id="form-image" required maxlength="200" value="<?php echo htmlspecialchars($row['image']); ?>" oninput="$('#form-image-preview').prop('src',$(this).val());$('#form-image-preview-link').prop('href',$(this).val());">
								</div>
							</div>
							<div class="col-sm-1">
								<div class="form-group">
									<a id="form-image-preview-link" href="<?php echo htmlspecialchars($row['image']); ?>" target="blank">
										<img id="form-image-preview" style="width: 64px; height: 90px; object-fit: cover; background-color: black; display:inline-block;" src="<?php echo htmlspecialchars($row['image']); ?>" alt="">
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
								<div class="form-check form-check-inline col-sm-2">
									<input class="form-check-input" type="checkbox" name="genres[]" id="form-genre-<?php echo $rowg['id']; ?>" data-myanimelist-id="<?php echo $rowg['myanimelist_id']; ?>" value="<?php echo $rowg['id']; ?>"<?php echo in_array($rowg['id'],$genres)? "checked" : ""; ?>>
									<label class="form-check-label" for="form-genre-<?php echo $rowg['id']; ?>"><?php echo htmlspecialchars($rowg['name']); ?></label>
								</div>
<?php
	}
	mysqli_free_result($resultg);
?>
							</div>
						</div>
						<div class="row align-items-end">
							<div class="col-sm-2">
								<div class="form-group">
									<label for="form-episodes">Nombre de capítols</label>
									<input class="form-control" name="episodes" type="number" id="form-episodes" value="<?php echo $row['episodes']!=-1 ? $row['episodes'] : ''; ?>"<?php echo $row['episodes']==-1 ? ' disabled' : ''; ?>>
								</div>
							</div>
							<div class="col-sm form-group row">
								<div class="form-check form-check-inline">
									<input class="form-check-input" type="checkbox" name="is_open" id="form-is_open" value="1"<?php echo $row['episodes']==-1? " checked" : ""; ?>>
									<label class="form-check-label" for="form-is_open">Sèrie oberta</label>
								</div>
								<button type="button" id="import-from-mal-episodes" class="btn btn-primary">
									<span id="import-from-mal-episodes-loading" class="d-none spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
									<span id="import-from-mal-episodes-not-loading" class="fa fa-th-list pr-2"></span>
									Importa els capítols de MyAnimeList
								</button>
								<button type="button" id="generate-episodes" class="btn btn-primary ml-3">
									<span class="fa fa-sort-numeric-down pr-2"></span>
									Genera els capítols
								</button>
							</div>
						</div>
						<div id="import-from-mal-episodes-done" class="col-sm form-group alert alert-warning d-none">
							S'han importat els capítols de MyAnimeList. Revisa'n tots els camps i no oblidis traduir-ne els títols.
						</div>
						<div class="form-group">
							<label for="form-episode-list">Capítols</label>
							<div class="container" id="form-episode-list">
								<div class="row">
									<div class="w-100 column">
										<table class="table table-bordered table-hover table-sm" id="episode-list-table" data-count="<?php echo max(count($episodes),1); ?>">
											<thead>
												<tr>
													<th style="width: 10%;">Núm.</th>
													<th>Títol <small class="text-muted">(informatiu, no es mostra)</small></th>
													<th class="text-center" style="width: 15%;">Data d'emissió</th>
													<th class="text-center" style="width: 5%;">Acció</th>
												</tr>
											</thead>
											<tbody>
<?php
	for ($i=0;$i<count($episodes);$i++) {
?>
												<tr id="form-episode-list-row-<?php echo $i+1; ?>">
													<td>
														<input id="form-episode-list-num-<?php echo $i+1; ?>" name="form-episode-list-num-<?php echo $i+1; ?>" type="number" class="form-control" value="<?php echo $episodes[$i]['number']; ?>" placeholder="(Esp.)"/>
														<input id="form-episode-list-id-<?php echo $i+1; ?>" name="form-episode-list-id-<?php echo $i+1; ?>" type="hidden" value="<?php echo $episodes[$i]['id']; ?>"/>
													</td>
													<td>
														<input id="form-episode-list-name-<?php echo $i+1; ?>" name="form-episode-list-name-<?php echo $i+1; ?>" type="text" class="form-control" value="<?php echo htmlspecialchars($episodes[$i]['name']); ?>" placeholder="(Sense títol)"/>
													</td>
													<td>
														<input id="form-episode-list-date-<?php echo $i+1; ?>" name="form-episode-list-date-<?php echo $i+1; ?>" type="date" class="form-control" value="<?php echo !empty($episodes[$i]['date']) ? date('Y-m-d',strtotime($episodes[$i]['date'])) : ''; ?>"/>
													</td>
													<td class="text-center align-middle">
														<button id="form-episode-list-delete-<?php echo $i+1; ?>" onclick="deleteRow(<?php echo $i+1; ?>);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
<?php
	}
	if (count($episodes)==0) {
?>
												<tr id="form-episode-list-row-1">
													<td>
														<input id="form-episode-list-num-1" name="form-episode-list-num-1" type="number" class="form-control" value="" placeholder="(Esp.)"/>
														<input id="form-episode-list-id-1" name="form-episode-list-id-1" type="hidden" value="-1"/>
													</td>
													<td>
														<input id="form-episode-list-name-1" name="form-episode-list-name-1" type="text" class="form-control" value="" placeholder="(Sense títol)"/>
													</td>
													<td>
														<input id="form-episode-list-date-1" name="form-episode-list-date-1" type="date" class="form-control" value=""/>
													</td>
													<td class="text-center align-middle">
														<button id="form-episode-list-delete-1" onclick="deleteRow(1);" type="button" class="btn fa fa-trash p-1 text-danger"></button>
													</td>
												</tr>
	<?php
		}
	?>
											</tbody>
										</table>
									</div>
									<button onclick="addRow();" type="button" class="btn btn-success btn-sm"><span class="fa fa-plus pr-2"></span>Afegeix un altre capítol</button>
								</div>
							</div>
						</div>
						<div class="form-group text-center pt-2">
							<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix la sèrie"; ?></button>
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
