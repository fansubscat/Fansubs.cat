<?php
$header_title="Edició de fansubs - Fansubs";
$page="fansub";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
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
		if (!empty($_POST['type'])) {
			$data['type']=escape($_POST['type']);
		} else {
			crash("Dades invàlides: manca type");
		}
		if (!empty($_POST['url'])) {
			$data['url']="'".escape($_POST['url'])."'";
		} else {
			$data['url']="NULL";
		}
		if (!empty($_POST['twitter_url'])) {
			$data['twitter_url']="'".escape($_POST['twitter_url'])."'";
		} else {
			$data['twitter_url']="NULL";
		}
		if (!empty($_POST['twitter_handle'])) {
			$data['twitter_handle']=escape($_POST['twitter_handle']);
		} else {
			crash("Dades invàlides: manca twitter_handle");
		}
		if (!empty($_POST['ping_token'])) {
			$data['ping_token']="'".escape($_POST['ping_token'])."'";
		} else {
			$data['ping_token']="NULL";
		}
		if (!empty($_POST['status']) && $_POST['status']==1) {
			$data['status']=1;
		} else {
			$data['status']=0;
		}
		if (!empty($_POST['historical']) && $_POST['historical']==1) {
			$data['historical']=1;
		} else {
			$data['historical']=0;
		}
		if (!empty($_POST['archive_url'])) {
			$data['archive_url']="'".escape($_POST['archive_url'])."'";
		} else {
			$data['archive_url']="NULL";
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-fansub", "S'ha actualitzat el fansub amb nom '".$data['name']."' (id. de fansub: ".$data['id'].")");
			query("UPDATE fansub SET name='".$data['name']."',slug='".$data['slug']."',type='".$data['type']."',url=".$data['url'].",twitter_url=".$data['twitter_url'].",twitter_handle='".$data['twitter_handle']."',status=".$data['status'].",ping_token=".$data['ping_token'].",historical=".$data['historical'].",archive_url=".$data['archive_url'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);

			if (!empty($_FILES['icon'])) {
				move_uploaded_file($_FILES['icon']["tmp_name"], '../www.fansubs.cat/images/fansub_icons/'.$data['id'].'.png');
			}
			if (!empty($_FILES['logo'])) {
				move_uploaded_file($_FILES['logo']["tmp_name"], '../www.fansubs.cat/images/fansub_logos/'.$data['id'].'.png');
			}
		}
		else {
			log_action("create-fansub", "S'ha creat un fansub amb nom '".$data['name']."'");
			query("INSERT INTO fansub (name,slug,type,url,twitter_url,twitter_handle,status,ping_token,historical,archive_url,created,created_by,updated,updated_by) VALUES ('".$data['name']."','".$data['slug']."','".$data['type']."',".$data['url'].",".$data['twitter_url'].",'".$data['twitter_handle']."',".$data['status'].",".$data['ping_token'].",".$data['historical'].",".$data['archive_url'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");

			if (!empty($_FILES['icon'])) {
				move_uploaded_file($_FILES['icon']["tmp_name"], '../www.fansubs.cat/images/fansub_icons/'.mysqli_insert_id($db_connection).'.png');
			}
			if (!empty($_FILES['logo'])) {
				move_uploaded_file($_FILES['logo']["tmp_name"], '../www.fansubs.cat/images/fansub_logos/'.mysqli_insert_id($db_connection).'.png');
			}
		}

		$_SESSION['message']="S'han desat les dades correctament.";

		header("Location: fansub_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT f.* FROM fansub f WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Fansub not found');
		mysqli_free_result($result);
	} else {
		$row = array();
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita el fansub" : "Afegeix un fansub"; ?></h4>
				<hr>
				<form method="post" action="fansub_edit.php" enctype="multipart/form-data" onsubmit="return checkFansub()">
					<div class="form-group">
						<label for="form-name-with-autocomplete" class="mandatory">Nom</label>
						<input class="form-control" name="name" id="form-name-with-autocomplete" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>">
						<input type="hidden" id="form-id" name="id" value="<?php echo $row['id']; ?>">
					</div>
					<div class="form-group">
						<label for="form-slug">Identificador<span class="mandatory"></span> <small class="text-muted">(autogenerat, no cal editar-lo)</small></label>
						<input class="form-control" name="slug" id="form-slug" required maxlength="200" value="<?php echo htmlspecialchars($row['slug']); ?>">
					</div>
					<div class="form-group">
						<label for="form-type">Tipus</label>
						<select class="form-control" name="type" id="form-type" required>
							<option value="">- Selecciona un tipus -</option>
							<option value="fansub"<?php echo $row['type']=='fansub' ? " selected" : ""; ?>>Fansub</option>
							<option value="fandub"<?php echo $row['type']=='fandub' ? " selected" : ""; ?>>Fandub</option>
						</select>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label>Icona<?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?> <small class="text-muted">(PNG, mida 24x24px)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists('../www.fansubs.cat/images/fansub_icons/'.$row['id'].'.png');
?>
								<label for="form-icon" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'info' ; ?>"><span class="fa fa-upload pr-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="icon" type="file" accept="image/png" id="form-icon" onchange="checkImageUpload(this, -1, 'form-icon-preview', 'form-icon-preview-link');">
							</div>
						</div>
						<div class="col-sm-2" style="align-self: center;">
							<div class="form-group">
								<a id="form-icon-preview-link"<?php echo $file_exists ? ' href="https://www.fansubs.cat/images/fansub_icons/'.$row['id'].'.png" data-original="https://www.fansubs.cat/images/fansub_icons/'.$row['id'].'.png"' : ''; ?> target="_blank">
									<img id="form-icon-preview" style="width: 24px; height: 24px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="https://www.fansubs.cat/images/fansub_icons/'.$row['id'].'.png" data-original="https://www.fansubs.cat/images/fansub_icons/'.$row['id'].'.png"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="form-group">
								<label>Logo <small class="text-muted">(PNG, aprox. 140x40px)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists('../www.fansubs.cat/images/fansub_logos/'.$row['id'].'.png');
?>
								<label for="form-logo" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'info' ; ?>"><span class="fa fa-upload pr-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="logo" type="file" accept="image/png" id="form-logo" onchange="checkImageUpload(this, -1, 'form-logo-preview', 'form-logo-preview-link');">
							</div>
						</div>
						<div class="col-sm-2" style="align-self: center;">
							<div class="form-group">
								<a id="form-logo-preview-link"<?php echo $file_exists ? ' href="https://www.fansubs.cat/images/fansub_logos/'.$row['id'].'.png" data-original="https://www.fansubs.cat/images/fansub_logos/'.$row['id'].'.png"' : ''; ?> target="_blank">
									<img id="form-logo-preview" style="width: 140px; height: 60px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="https://www.fansubs.cat/images/fansub_logos/'.$row['id'].'.png" data-original="https://www.fansubs.cat/images/fansub_logos/'.$row['id'].'.png"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="form-url">URL</label>
						<input class="form-control" type="url" name="url" id="form-url" maxlength="200" value="<?php echo htmlspecialchars($row['url']); ?>">
					</div>
					<div class="form-group">
						<label for="form-twitter_url">URL del perfil de Twitter</label>
						<input class="form-control" type="url" name="twitter_url" id="form-twitter_url" maxlength="200" value="<?php echo htmlspecialchars($row['twitter_url']); ?>">
					</div>
					<div class="form-group">
						<label for="form-twitter_handle">Nom a Twitter<span class="mandatory"></span> <small class="text-muted">(incloent arrova, si no en té, el nom sencer del fansub)</small></label>
						<input class="form-control" name="twitter_handle" id="form-twitter_handle" required maxlength="200" value="<?php echo htmlspecialchars($row['twitter_handle']); ?>">
					</div>
					<div class="form-group">
						<label for="form-status">Estat</label>
						<div id="form-status" class="row pl-3 pr-3">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="status" id="form-active" value="1"<?php echo $row['status']==1? " checked" : ""; ?>>
								<label class="form-check-label" for="form-active">Actiu</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="historical" id="form-historical" value="1"<?php echo $row['historical']==1? " checked" : ""; ?>>
								<label class="form-check-label" for="form-historical">Històric</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label for="form-archive_url">URL d'Archive.org <small class="text-muted">(obligatori si és històric)</small></label>
						<input class="form-control" type="url" name="archive_url" id="form-archive_url" maxlength="200"<?php echo $row['historical']==0? " disabled" : ""; ?> value="<?php echo htmlspecialchars($row['archive_url']); ?>">
					</div>
					<div class="form-group">
						<label for="form-ping_token">Testimoni per a fer ping <small class="text-muted">(si es fa una petició a https://api.fansubs.cat/refresh/&lt;testimoni&gt;, s'actualitzaran les notícies al moment)</small></label>
						<input class="form-control" name="ping_token" id="form-ping_token" maxlength="200" value="<?php echo htmlspecialchars($row['ping_token']); ?>">
					</div>
					<div class="form-group text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix el fansub"; ?></button>
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
