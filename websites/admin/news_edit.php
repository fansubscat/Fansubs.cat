<?php
$header_title="Edició de notícies - Notícies";
$page="news";
include("header.inc.php");

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
				crash("No es pot editar la notícia: més d'una notícia amb el mateix MD5!");
			} else if (mysqli_num_rows($toupdate_result)==1) {
				$toupdate_row = mysqli_fetch_assoc($toupdate_result);
				log_action("update-news", "S'ha actualitzat la notícia '".escape($toupdate_row['title'])."' del fansub '".escape($toupdate_row['fansub_name'])."'");
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
			if (isset($_POST['import_pending_id'])) {
				log_action("import-pending-news", "S'ha importat la notícia proposada amb id. ".escape($_POST['import_pending_id']));
				query("DELETE FROM pending_news WHERE id=".escape($_POST['import_pending_id']));
			}
			log_action("create-news", "S'ha creat la notícia '".$data['title']."' de '".escape($fansub_row['fansub_name'])."'");
			query("INSERT INTO news (fansub_id,news_fetcher_id,title,contents,original_contents,date,url,image) VALUES (".$data['fansub_id'].",NULL,'".$data['title']."','".$data['contents']."','".$data['contents']."','".$data['date']."',".$data['url'].",".$data['image'].")");
		}

		$_SESSION['message']="S'han desat les dades correctament.";

		header("Location: news_list.php");
		die();
	}

	if (isset($_GET['id'])) {
		$result = query("SELECT MD5(CONCAT(n.title, n.date)) id, n.*, f.slug fansub_slug FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE MD5(CONCAT(n.title, n.date))='".escape($_GET['id'])."'");
		$row = mysqli_fetch_assoc($result) or crash('News not found');
		mysqli_free_result($result);
	} else {
		$row = array();
		if (!empty($_GET['import_pending_id'])) {
			$pending_result = query("SELECT pn.* FROM pending_news pn WHERE pn.id=".escape($_GET['import_pending_id']));
			$prow = mysqli_fetch_assoc($pending_result) or crash('Pending news not found');
			$row['fansub_id']="";
			$row['title']=$prow['title'];
			$row['contents']=$prow['contents'];
			$row['url']=$prow['url'];
			$row['pending_id']=$prow['id'];
			$row['pending_author']=$prow['sender_name'];
			$row['pending_email']=$prow['sender_email'];
			$row['pending_image']=$prow['image_url'];
			$row['pending_comments']=$prow['comments'];
		}
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita la notícia" : (!empty($_GET['import_pending_id']) ? "Importa una notícia proposada" : "Afegeix una notícia a mà"); ?></h4>
				<hr>
				<form method="post" action="news_edit.php" enctype="multipart/form-data">
<?php
	if(isset($_GET['import_pending_id'])) {
?>
					<div class="form-group">
						<label for="form-pending_author">Autor que ha proposat la notícia</label>
						<input class="form-control" id="form-pending_author" disabled value="<?php echo htmlspecialchars($row['pending_author']); ?>">
					</div>
					<div class="form-group">
						<label for="form-pending_email">Correu de l'autor que ha proposat la notícia</label>
						<input class="form-control" id="form-pending_email" disabled value="<?php echo htmlspecialchars($row['pending_email']); ?>">
					</div>
					<div class="form-group">
						<label for="form-pending_comments">Comentaris de l'autor que ha proposat la notícia</label>
						<input class="form-control" id="form-pending_comments" disabled value="<?php echo htmlspecialchars($row['pending_comments']); ?>">
					</div>
					<div class="form-group">
						<label for="form-pending_image">Imatge proposada per a la notícia</label>
						<input class="form-control" id="form-pending_image" disabled value="<?php echo htmlspecialchars($row['pending_image']); ?>">
					</div>
<?php
	} else if (empty($row['id'])) {
?>
					<p class="alert alert-warning"><span class="fa fa-exclamation-triangle mr-2"></span>Normalment, no és necessari afegir manualment notícies de fansubs, ja que s'obtenen automàticament mitjançant els recollidors. Assegura't que realment és això el que vols fer, i en cas de dubte, contacta amb un administrador.</p>
<?php
	}
?>
					<div class="form-group">
						<label for="form-fansub_id">Fansub</label>
						<select name="fansub_id" class="form-control" id="form-fansub_id"<?php echo isset($_GET['id']) ? ' disabled' : ''; ?>>
							<option value="">- Notícia en nom de Fansubs.cat (tria un altre fansub, si no és així) -</option>
<?php
	$result = query("SELECT f.* FROM fansub f ORDER BY f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
							<option value="<?php echo $frow['id']; ?>"<?php echo $row['fansub_id']==$frow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
						</select>
						<?php echo isset($_GET['id']) ? '<input type="hidden" name="fansub_id" value="'.$row['fansub_id'].'">' : ''; ?>
						<?php echo isset($_GET['import_pending_id']) ? '<input type="hidden" name="import_pending_id" value="'.$row['pending_id'].'">' : ''; ?>
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
					</div>
					<div class="form-group">
						<label for="form-title" class="mandatory">Títol</label>
						<input class="form-control" name="title" id="form-title" required value="<?php echo htmlspecialchars($row['title']); ?>">
					</div>
					<div class="form-group">
						<label for="form-contents">Contingut<span class="mandatory"></span> <small class="text-muted">(compte, en format HTML!)</small></label>
						<textarea class="form-control" name="contents" id="form-contents" required style="height: 150px;"><?php echo htmlspecialchars($row['contents']); ?></textarea>
					</div>
					<div class="form-group">
						<label for="form-url">URL</label>
						<input class="form-control" name="url" id="form-url" value="<?php echo htmlspecialchars($row['url']); ?>">
					</div>
					<div class="form-group">
						<label for="form-date" class="mandatory">Data</label>
						<input class="form-control" name="date" type="datetime-local" id="form-date" required step="1" value="<?php echo !empty($row['date']) ? date('Y-m-d\TH:i:s', strtotime($row['date'])) : date('Y-m-d\TH:i:s'); ?>">
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label>Imatge<br><small class="text-muted">(format JPEG o PNG)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/news/'.$row['fansub_slug'].'/'.$row['image']);
?>
								<label for="form-image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'info' ; ?>"><span class="fa fa-upload pr-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="image" type="file" accept="image/jpeg,image/png" id="form-image" onchange="checkImageUpload(this, -1, 'form-image-preview', 'form-image-preview-link');">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="form-group">
								<a id="form-image-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'" data-original="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'"' : ''; ?> target="_blank">
									<img id="form-image-preview" style="width: 140px; height: 90px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'" data-original="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="form-group text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix la notícia"; ?></button>
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
