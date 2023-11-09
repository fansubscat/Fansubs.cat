<?php
$header_title="Edició de comunitats - Altres";
$page="other";
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
		if (!empty($_POST['description'])) {
			$data['description']=escape($_POST['description']);
		} else {
			crash("Dades invàlides: manca description");
		}
		if (!empty($_POST['url'])) {
			$data['url']=escape($_POST['url']);
		} else {
			crash("Dades invàlides: manca url");
		}
		if (!empty($_POST['category'])) {
			$data['category']=escape($_POST['category']);
		} else {
			crash("Dades invàlides: manca category");
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-community", "S’ha actualitzat la comunitat «".$_POST['name']."» (id. de comunitat: ".$data['id'].")");
			query("UPDATE community SET name='".$data['name']."',url='".$data['url']."',category='".$data['category']."',description='".$data['description']."',updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);

			if (!empty($_FILES['logo'])) {
				move_uploaded_file($_FILES['logo']["tmp_name"], STATIC_DIRECTORY.'/images/communities/'.$data['id'].'.png');
			}
		}
		else {
			log_action("create-community", "S’ha creat la comunitat «".$_POST['name']."»");
			query("INSERT INTO community (name,url,category,description,created,created_by,updated,updated_by) VALUES ('".$data['name']."','".$data['url']."','".$data['category']."','".$data['description']."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");

			if (!empty($_FILES['logo'])) {
				move_uploaded_file($_FILES['logo']["tmp_name"], STATIC_DIRECTORY.'/images/communities/'.mysqli_insert_id($db_connection).'.png');
			}
		}

		$_SESSION['message']="S’han desat les dades correctament.";

		header("Location: community_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT c.* FROM community c WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Community not found');
		mysqli_free_result($result);
	} else {
		$row = array();
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita la comunitat" : "Afegeix una comunitat"; ?></h4>
				<hr>
				<form method="post" action="community_edit.php" enctype="multipart/form-data" onsubmit="return checkCommunity()">
					<div class="mb-3">
						<label for="form-name" class="mandatory">Nom</label>
						<input class="form-control" name="name" id="form-name" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>">
						<input type="hidden" id="form-id" name="id" value="<?php echo $row['id']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-url" class="mandatory">URL</label>
						<input class="form-control" type="url" name="url" id="form-url" maxlength="200" value="<?php echo htmlspecialchars($row['url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-description" class="mandatory">Descripció</label>
						<input class="form-control" name="description" id="form-description" maxlength="200" value="<?php echo htmlspecialchars($row['description']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-category" class="mandatory">Categoria</label>
						<select class="form-select" name="category" id="form-category" required>
							<option value="">- Selecciona una categoria -</option>
							<option value="featured"<?php echo $row['category']=='featured' ? " selected" : ""; ?>>Destacats</option>
							<option value="blogs"<?php echo $row['category']=='blogs' ? " selected" : ""; ?>>Blogs i notícies</option>
							<option value="catalogs"<?php echo $row['category']=='catalogs' ? " selected" : ""; ?>>Catàlegs</option>
							<option value="art"<?php echo $row['category']=='art' ? " selected" : ""; ?>>Còmic i arts visuals</option>
							<option value="forums"<?php echo $row['category']=='forums' ? " selected" : ""; ?>>Comunitats i fòrums</option>
							<option value="culture"<?php echo $row['category']=='culture' ? " selected" : ""; ?>>Cultura asiàtica</option>
							<option value="creators"<?php echo $row['category']=='creators' ? " selected" : ""; ?>>Divulgadors</option>
							<option value="dubbing"<?php echo $row['category']=='dubbing' ? " selected" : ""; ?>>Doblatge</option>
							<option value="music"<?php echo $row['category']=='music' ? " selected" : ""; ?>>Música i versions</option>
							<option value="nostalgia"<?php echo $row['category']=='nostalgia' ? " selected" : ""; ?>>Nostàlgia</option>
							<option value="podcasts"<?php echo $row['category']=='podcasts' ? " selected" : ""; ?>>Pòdcasts</option>
							<option value="preservation"<?php echo $row['category']=='preservation' ? " selected" : ""; ?>>Preservació</option>
							<option value="subtitles"<?php echo $row['category']=='subtitles' ? " selected" : ""; ?>>Subtítols</option>
							<option value="others"<?php echo $row['category']=='others' ? " selected" : ""; ?>>Altres</option>
						</select>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="mb-3">
								<label>Logo<?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?> <small class="text-muted">(PNG, aprox. 160x160px)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/communities/'.$row['id'].'.png');
?>
								<label for="form-logo" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="logo" type="file" accept="image/png" id="form-logo" onchange="checkImageUpload(this, -1, 'form-logo-preview', 'form-logo-preview-link');">
							</div>
						</div>
						<div class="col-sm-2" style="align-self: center;">
							<div class="mb-3">
								<a id="form-logo-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/communities/'.$row['id'].'.png" data-original="'.STATIC_URL.'/images/communities/'.$row['id'].'.png"' : ''; ?> target="_blank">
									<img id="form-logo-preview" style="width: 60px; height: 60px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/communities/'.$row['id'].'.png" data-original="'.STATIC_URL.'/images/communities/'.$row['id'].'.png"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix la comunitat"; ?></button>
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
