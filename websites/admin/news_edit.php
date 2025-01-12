<?php
$header_title="Edició de notícies - Notícies";
$page="news";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca id");
		}
		if (!empty($_POST['fansub_id']) && is_numeric($_POST['fansub_id'])) {
			$data['fansub_id']=escape($_POST['fansub_id']);
		} else {
			$data['fansub_id']='NULL';
		}
		if (!empty($_POST['title'])) {
			$data['title']=escape($_POST['title']);
		} else {
			crash("Dades invàlides: manca title");
		}
		if (!empty($_POST['url'])) {
			$data['url']="'".escape($_POST['url'])."'";
		} else {
			$data['url']="NULL";
		}
		if (!empty($_POST['contents'])) {
			$data['contents']=escape($_POST['contents']);
		} else {
			crash("Dades invàlides: manca contents");
		}
		if (!empty($_POST['date'])) {
			$data['date']=date('Y-m-d H:i:s', strtotime($_POST['date']));
		} else {
			crash("Dades invàlides: manca date");
		}
		
		if ($_POST['action']=='edit') {
			$toupdate_result = query("SELECT n.*, f.name fansub_name, f.slug fansub_slug FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE MD5(CONCAT(n.title, n.date))='".$data['id']."'");
			if (mysqli_num_rows($toupdate_result)>1) {
				crash("No es pot editar la notícia: més d’una notícia amb el mateix MD5!");
			} else if (mysqli_num_rows($toupdate_result)==1) {
				$toupdate_row = mysqli_fetch_assoc($toupdate_result);
				log_action("update-news", "S’ha actualitzat la notícia «".$toupdate_row['title']."» del fansub «".$toupdate_row['fansub_name']."»");
				if (!empty($_FILES['image'])) {
					move_uploaded_file($_FILES['image']["tmp_name"], STATIC_DIRECTORY.'/images/news/'.$toupdate_row['fansub_slug'].'/'.md5($data['title'].$data['date']));
					$data['image'] = "'".md5($data['title'].$data['date'])."'";
				} else if (!empty($toupdate_row['image'])) {
					$data['image'] = "'".$toupdate_row['image']."'";
				} else {
					$data['image'] = 'NULL';
				}
				query("UPDATE news SET fansub_id=".$data['fansub_id'].",url=".$data['url'].",contents='".$data['contents']."',title='".$data['title']."',date='".$data['date']."',image=".$data['image']." WHERE MD5(CONCAT(title, date))='".$data['id']."'");
			}
		}
		else {
			$fansub_result = query("SELECT f.name fansub_name, f.slug fansub_slug FROM fansub f WHERE f.id=".$data['fansub_id']);
			$fansub_row = mysqli_fetch_assoc($fansub_result);
			if (!empty($_FILES['image'])) {
				move_uploaded_file($_FILES['image']["tmp_name"], STATIC_DIRECTORY.'/images/news/'.$fansub_row['fansub_slug'].'/'.md5($data['title'].$data['date']));
				$data['image'] = "'".md5($data['title'].$data['date'])."'";
			} else {
				$data['image'] = 'NULL';
			}
			log_action("create-news", "S’ha creat la notícia «".$_POST['title']."» de «".$fansub_row['fansub_name']."»");
			query("INSERT INTO news (fansub_id,news_fetcher_id,title,contents,original_contents,date,url,image) VALUES (".$data['fansub_id'].",NULL,'".$data['title']."','".$data['contents']."','".$data['contents']."','".$data['date']."',".$data['url'].",".$data['image'].")");
		}

		$_SESSION['message']="S’han desat les dades correctament.";

		header("Location: news_list.php");
		die();
	}

	if (isset($_GET['id'])) {
		$result = query("SELECT MD5(CONCAT(n.title, n.date)) id, n.*, f.slug fansub_slug FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE MD5(CONCAT(n.title, n.date))='".escape($_GET['id'])."'");
		$row = mysqli_fetch_assoc($result) or crash('News not found');
		mysqli_free_result($result);
	} else {
		$row = array();
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita la notícia" : "Afegeix una notícia a mà"; ?></h4>
				<hr>
				<form method="post" action="news_edit.php" enctype="multipart/form-data" onsubmit="return checkNewsPost()">
<?php
	if (empty($row['id'])) {
?>
					<p class="alert alert-warning"><span class="fa fa-exclamation-triangle me-2"></span>Normalment, no és necessari afegir manualment notícies de fansubs, ja que s’obtenen automàticament mitjançant els recollidors. Assegura’t que realment és això el que vols fer, i en cas de dubte, contacta amb un administrador.</p>
<?php
	}
?>
					<div class="mb-3">
						<label for="form-fansub_id">Fansub</label> <?php print_helper_box('Fansub', 'Fansub al qual està associada la notícia.\n\nNo és possible editar-lo una vegada afegida la notícia.'); ?>
						<select name="fansub_id" class="form-select" id="form-fansub_id"<?php echo isset($_GET['id']) ? ' disabled' : ''; ?>>
<?php
	if ($_SESSION['admin_level']>=3) {
?>
							<option value="">- Notícia en nom de Fansubs.cat (si no és el que vols, tria un altre fansub) -</option>
<?php
	}
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE f.id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT f.* FROM fansub f$where ORDER BY f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
							<option value="<?php echo $frow['id']; ?>"<?php echo $row['fansub_id']==$frow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
						</select>
						<?php echo isset($_GET['id']) ? '<input type="hidden" name="fansub_id" value="'.$row['fansub_id'].'">' : ''; ?>
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-title" class="mandatory">Títol</label> <?php print_helper_box('Títol', 'Títol de la notícia que es mostrarà al web.'); ?>
						<input class="form-control" name="title" id="form-title" required value="<?php echo htmlspecialchars($row['title']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-contents">Contingut<span class="mandatory"></span> <?php print_helper_box('Contingut', 'Text del cos de la notícia que es mostrarà al web.\n\nImportant: aquí s’hi introdueix codi HTML i el web l’imprimeix sense comprovar-ne la validesa: si és codi invàlid, pot trencar el web.\n\nNo recomanem fer servir codi HTML (ni tan sols etiquetes de negreta i cursiva) per aquest risc.\n\nSi tot i això decideixes fer-ne servir, assegura’t de tancar totes les etiquetes correctament.'); ?> <small class="text-muted">(compte, en format HTML!)</small></label>
						<textarea class="form-control" name="contents" id="form-contents" required style="height: 150px;"><?php echo htmlspecialchars($row['contents']); ?></textarea>
					</div>
					<div class="mb-3">
						<label for="form-url">URL</label> <?php print_helper_box('URL', 'URL a la qual enllaçarà la notícia.\n\nEs poden crear notícies sense URL, però Fansubs.cat no és un lloc on generar contingut propi, sinó un recull de tots els webs de notícies.'); ?>
						<input class="form-control" name="url" id="form-url" value="<?php echo htmlspecialchars($row['url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-date" class="mandatory">Data</label> <?php print_helper_box('Data', 'Data de la notícia que es mostrarà al web.'); ?>
						<input class="form-control" name="date" type="datetime-local" id="form-date" required step="1" value="<?php echo !empty($row['date']) ? date('Y-m-d\TH:i:s', strtotime($row['date'])) : date('Y-m-d\TH:i:s'); ?>">
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="mb-3">
								<label>Imatge <?php print_helper_box('Imatge', 'Imatge opcional que es mostrarà al costat de la notícia al web.'); ?><br><small class="text-muted">(format JPEG o PNG)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/news/'.$row['fansub_slug'].'/'.$row['image']);
?>
								<label for="form-image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="image" type="file" accept="image/jpeg,image/png" id="form-image" onchange="checkImageUpload(this, -1, 'image/*', 1, 1, 4096, 4096, 'form-image-preview', 'form-image-preview-link');">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="mb-3">
								<a id="form-image-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'" data-original="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'"' : ''; ?> target="_blank">
									<img id="form-image-preview" style="width: 140px; height: 90px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'" data-original="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix la notícia"; ?></button>
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
