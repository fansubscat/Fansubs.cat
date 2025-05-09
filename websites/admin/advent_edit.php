<?php
$header_title="Edició de calendaris d’advent - Altres";
$page="other";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_POST['action']) && $_POST['action']=='edit') {
		$data=array();
		if (!empty($_POST['year']) && is_numeric($_POST['year'])) {
			$data['year']=escape($_POST['year']);
		} else {
			crash("Dades invàlides: manca year");
		}
		if (!empty($_POST['position'])) {
			$data['position']=escape($_POST['position']);
		} else {
			crash("Dades invàlides: manca position");
		}

		$days = array();
		for ($i=1; $i<25; $i++) {
			if (!empty($_POST['link_url_'.$i])) {
				$data['link_url_'.$i]="'".escape($_POST['link_url_'.$i])."'";
			} else {
				$data['link_url_'.$i]="NULL";
			}
			if (!empty($_POST['description_'.$i])) {
				$data['description_'.$i]="'".escape($_POST['description_'.$i])."'";
			} else {
				$data['description_'.$i]="NULL";
			}
		}

		log_action("update-advent-calendar", "S’ha actualitzat el calendari d’advent del ".$_POST['year']);
		
		query("REPLACE INTO advent_calendar (year, position) VALUES (".$data['year'].",'".$data['position']."')");
		if (!empty($_FILES['background'])) {
			move_uploaded_file($_FILES['background']["tmp_name"], STATIC_DIRECTORY.'/images/advent/background_'.$data['year'].'.jpg');
		}
		if (!empty($_FILES['preview'])) {
			move_uploaded_file($_FILES['preview']["tmp_name"], STATIC_DIRECTORY.'/images/advent/preview_'.$data['year'].'.jpg');
		}
		if (!empty($_FILES['header'])) {
			move_uploaded_file($_FILES['header']["tmp_name"], STATIC_DIRECTORY.'/images/advent/header_'.$data['year'].'.jpg');
		}

		for ($i=1; $i<25; $i++) {
			query("REPLACE INTO advent_day (year, day, description, link_url) VALUES (".$data['year'].",".$i.",".$data['description_'.$i].",".$data['link_url_'.$i].")");
			if (!empty($_FILES['image_'.$i])) {
				move_uploaded_file($_FILES['image_'.$i]["tmp_name"], STATIC_DIRECTORY.'/images/advent/image_'.$data['year'].'_'.$i.'.jpg');
			}
		}

		$_SESSION['message']="S’han desat les dades correctament.";

		header("Location: advent_list.php");
		die();
	}
	if (isset($_GET['year']) && is_numeric($_GET['year'])) {
		$result = query("SELECT * FROM advent_calendar WHERE year=".escape($_GET['year'])."");
		$row = mysqli_fetch_assoc($result) or $row=array('year' => $_GET['year'], 'position' => '');
		mysqli_free_result($result);

		$days = array();
		$resultd = query("SELECT * FROM advent_day WHERE year=".escape($row['year'])." ORDER BY day ASC");
		while ($rowd = mysqli_fetch_assoc($resultd)) {
			$days[$rowd['day']]=$rowd;
		}
		mysqli_free_result($resultd);

		if (count($days)<24){
			for ($i=1; $i<25; $i++) {
				if (empty($days[$i])) {
					$days[$i]=array('link_url' => NULL, 'description' => NULL);
				}
			}
		}
	} else {
		crash('Year not found');
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1">Edita el calendari d’advent del <?php echo $row['year']; ?></h4>
				<hr>
				<form method="post" action="advent_edit.php" enctype="multipart/form-data">
					<div class="row">
						<div class="col-sm-3">
							<div class="mb-3">
								<label>Imatge de fons<span class="mandatory"></span> <?php print_helper_box('Imatge de fons', 'Imatge que s’utilitza per al fons del calendari.\n\nHa de ser un fitxer JPEG i tenir una mida mínima de 1920x1080 píxels (o la mateixa resolució però en vertical).'); ?><br><small class="text-muted">(JPEG, mida mínima 1920x1080px o 1080x1920px)</small></label><br>
<?php
	$file_exists = file_exists(STATIC_DIRECTORY.'/images/advent/background_'.$row['year'].'.jpg');
?>
								<label for="form-background" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="background" type="file" accept="image/jpeg" id="form-background" onchange="checkImageUpload(this, -1, 'image/jpeg', 1080, 1080, 4096, 4096, 'form-background-preview', 'form-background-preview-link');">
							<input type="hidden" name="year" value="<?php echo $row['year']; ?>">
							</div>
						</div>
						<div class="col-sm-3" style="align-self: center;">
							<div class="mb-3">
								<a id="form-background-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/advent/background_'.$row['year'].'.jpg" data-original="'.STATIC_URL.'/images/advent/background_'.$row['year'].'.jpg"' : ''; ?> target="_blank">
									<img id="form-background-preview" style="width: 192px; height: 108px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/advent/background_'.$row['year'].'.jpg" data-original="'.STATIC_URL.'/images/advent/background_'.$row['year'].'.jpg"' : ''; ?> alt="">
								</a>
							</div>
						</div>
						<div class="col-sm-3">
							<div class="mb-3">
								<label>Imatge de previsualització<span class="mandatory"></span> <?php print_helper_box('Imatge de previsualització', 'Imatge que s’utilitza per a la visualització en mode targeta de les xarxes socials (atribut og:image).\n\nHa de ser un fitxer JPEG i tenir una mida mínima de 1200x600 píxels.'); ?><br><small class="text-muted">(JPEG, mida mínima 1200x600px)</small></label><br>
<?php
	$file_exists = file_exists(STATIC_DIRECTORY.'/images/advent/preview_'.$row['year'].'.jpg');
?>
								<label for="form-preview" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="preview" type="file" accept="image/jpeg" id="form-preview" onchange="checkImageUpload(this, -1, 'image/jpeg', 1200, 600, 4096, 4096, 'form-preview-preview', 'form-preview-preview-link');">
							</div>
						</div>
						<div class="col-sm-3" style="align-self: center;">
							<div class="mb-3">
								<a id="form-preview-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/advent/preview_'.$row['year'].'.jpg" data-original="'.STATIC_URL.'/images/advent/preview_'.$row['year'].'.jpg"' : ''; ?> target="_blank">
									<img id="form-preview-preview" style="width: 192px; height: 108px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/advent/preview_'.$row['year'].'.jpg" data-original="'.STATIC_URL.'/images/advent/preview_'.$row['year'].'.jpg"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="mb-3">
								<label>Imatge de capçalera<span class="mandatory"></span> <?php print_helper_box('Imatge de capçalera', 'Imatge que s’utilitza per a la capçalera dels portals d’anime, manga i imatge real (a la secció de recomanacions) durant el període del calendari d’advent.\n\nHa de ser un fitxer JPEG i tenir una mida mínima de 1920x400 píxels.'); ?><br><small class="text-muted">(JPEG, mida mínima 1920x400px)</small></label><br>
<?php
	$file_exists = file_exists(STATIC_DIRECTORY.'/images/advent/header_'.$row['year'].'.jpg');
?>
								<label for="form-header" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="header" type="file" accept="image/jpeg" id="form-header" onchange="checkImageUpload(this, -1, 'image/jpeg', 1920, 400, 4096, 4096, 'form-header-preview', 'form-header-preview-link');">
							</div>
						</div>
						<div class="col-sm-3" style="align-self: center;">
							<div class="mb-3">
								<a id="form-header-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/advent/header_'.$row['year'].'.jpg" data-original="'.STATIC_URL.'/images/advent/header_'.$row['year'].'.jpg"' : ''; ?> target="_blank">
									<img id="form-header-preview" style="width: 240px; height: 51px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/advent/header_'.$row['year'].'.jpg" data-original="'.STATIC_URL.'/images/advent/header_'.$row['year'].'.jpg"' : ''; ?> alt="">
								</a>
							</div>
						</div>
						<div class="col-sm-6">
							<label for="form-position" class="mandatory">Posició del logo</label> <?php print_helper_box('Posició del logo', 'Indica si, a la pàgina del calendari d’advent, el logo de Fansubs.cat ha d’estar col·locat a l’esquerra o a la dreta.\n\nCal ajustar-ho segons el fons del calendari per a evitar tapar la imatge.'); ?>
							<select class="form-control" name="position" id="form-position" required>
								<option value="">- Selecciona una posició -</option>
								<option value="left"<?php echo $row['position']=='left' ? " selected" : ""; ?>>A l’esquerra</option>
								<option value="right"<?php echo $row['position']=='right' ? " selected" : ""; ?>>A la dreta</option>
							</select>
						</div>
					</div>
					<div class="mb-3">
						<div class="container" id="form-days-list">
							<div class="row">
								<div class="w-100 column">
									<table class="table table-bordered table-hover table-sm" id="days-list-table" data-count="24">
										<thead>
											<tr>
												<th class="text-center" style="width: 5%;">Dia</th>
												<th class="text-center" style="width: 20%;">Imatge<span class="mandatory"></span> <?php print_helper_box('Imatge de la casella', 'Imatge que s’utilitza per a la casella del dia indicat una vegada s’ha obert.\n\nHa de ser un fitxer JPEG i tenir una mida exacta de 512x512 píxels.'); ?> <small class="text-muted">(512x512px)</small></th>
												<th>URL de l’enllaç i descripció<span class="mandatory"></span> <?php print_helper_box('URL de l’enllaç i descripció', 'Enllaç al qual apunta la casella del dia indicat una vegada s’ha obert.\n\nPot ser un URL extern o bé del portal de Fansubs.cat.\n\nLa descripció es mostra sota la imatge als vídeos promocionals que es fan cada dia obrint la casella.\n\nEl format és «Nom del fansub | Títol del contingut».'); ?></th>
											</tr>
										</thead>
										<tbody>
<?php

		for ($i=1; $i<25; $i++) {
?>
											<tr id="form-days-list-row-<?php echo $i; ?>">
												<td class="text-center align-middle">
													<strong><?php echo $i; ?></strong>
												</td>
												<td class="text-center align-middle">
<?php
			$file_exists = file_exists(STATIC_DIRECTORY.'/images/advent/image_'.$row['year'].'_'.$i.'.jpg');
?>
													<a id="form-image_<?php echo $i; ?>-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/advent/image_'.$row['year'].'_'.$i.'.jpg" data-original="'.STATIC_URL.'/images/advent/image_'.$row['year'].'_'.$i.'.jpg"' : ''; ?> target="_blank">
														<img id="form-image_<?php echo $i; ?>-preview" style="width: 64px; height: 64px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/advent/image_'.$row['year'].'_'.$i.'.jpg" data-original="'.STATIC_URL.'/images/advent/image_'.$row['year'].'_'.$i.'.jpg"' : ''; ?> alt="">
													</a>
													<br>
													<label for="form-image_<?php echo $i; ?>" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
													<input class="form-control d-none" name="image_<?php echo $i; ?>" type="file" accept="image/jpeg" id="form-image_<?php echo $i; ?>" onchange="checkImageUpload(this, -1, 'image/jpeg', 512, 512, 512, 512, 'form-image_<?php echo $i; ?>-preview', 'form-image_<?php echo $i; ?>-preview-link');">
												</td>
												<td class="text-center align-middle">
													<input id="form-days-list-link_url-<?php echo $i+1; ?>" name="link_url_<?php echo $i; ?>" type="url" class="form-control" value="<?php echo htmlspecialchars($days[$i]['link_url']); ?>" maxlength="200" placeholder="- Introdueix un enllaç -"/>
													<input id="form-days-list-description-<?php echo $i+1; ?>" name="description_<?php echo $i; ?>" type="text" class="form-control mt-2" value="<?php echo htmlspecialchars($days[$i]['description']); ?>" maxlength="200" placeholder="- Introdueix una descripció -"/>
												</td>
											</tr>
<?php
		}
?>
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="edit" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span>Desa els canvis</button>
					</div>
				</form>
			</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include(__DIR__.'/footer.inc.php');
?>
