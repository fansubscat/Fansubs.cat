<?php
$header_title="Edició de fansubs - Fansubs";
$page="fansub";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && ($_SESSION['admin_level']>=3 || ($_SESSION['admin_level']==2 && !empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id']) && ($_SESSION['fansub_id']==$_GET['id'] || $_SESSION['fansub_id']==$_POST['id'])))) {
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
		if (!empty($_POST['discord_url'])) {
			$data['discord_url']="'".escape($_POST['discord_url'])."'";
		} else {
			$data['discord_url']="NULL";
		}
		if (!empty($_POST['mastodon_url'])) {
			$data['mastodon_url']="'".escape($_POST['mastodon_url'])."'";
		} else {
			$data['mastodon_url']="NULL";
		}
		if (!empty($_POST['mastodon_handle'])) {
			$data['mastodon_handle']=escape($_POST['mastodon_handle']);
		} else {
			crash("Dades invàlides: manca mastodon_handle");
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
		if (!empty($_POST['bluesky_url'])) {
			$data['bluesky_url']="'".escape($_POST['bluesky_url'])."'";
		} else {
			$data['bluesky_url']="NULL";
		}
		if (!empty($_POST['bluesky_handle'])) {
			$data['bluesky_handle']=escape($_POST['bluesky_handle']);
		} else {
			crash("Dades invàlides: manca bluesky_handle");
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
		if (!empty($_POST['is_historical']) && $_POST['is_historical']==1) {
			$data['is_historical']=1;
		} else {
			$data['is_historical']=0;
		}
		if (!empty($_POST['archive_url'])) {
			$data['archive_url']="'".escape($_POST['archive_url'])."'";
		} else {
			$data['archive_url']="NULL";
		}
		if (!empty($_POST['hentai_category']) && ($_POST['hentai_category']==1 || $_POST['hentai_category']==2)) {
			$data['hentai_category']=$_POST['hentai_category'];
		} else {
			$data['hentai_category']=0;
		}
		if (!empty($_POST['old_username'])) {
			$data['old_username']=escape($_POST['old_username']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca old_username");
		}
		if (!empty($_POST['user_password'])) {
			$data['user_password']=escape($_POST['user_password']);
		} else if ($_POST['action']=='edit') {
			$data['user_password']='';
		} else {
			crash("Dades invàlides: manca user_password");
		}
		if (!empty($_POST['email'])) {
			$data['email']=escape($_POST['email']);
		} else {
			crash("Dades invàlides: manca email");
		}
		
		if ($_POST['action']=='edit') {
			$old_result = query("SELECT * FROM fansub WHERE id=".$data['id']);
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash("Algú altre ha actualitzat el fansub mentre tu l’editaves. Hauràs de tornar a fer els canvis.");
			}
			
			log_action("update-fansub", "S’ha actualitzat el fansub «".$_POST['name']."» (id. de fansub: ".$data['id'].")");
			query("UPDATE fansub SET name='".$data['name']."',slug='".$data['slug']."',type='".$data['type']."',url=".$data['url'].",email='".$data['email']."',twitter_url=".$data['twitter_url'].",twitter_handle='".$data['twitter_handle']."',mastodon_url=".$data['mastodon_url'].",mastodon_handle='".$data['mastodon_handle']."',discord_url=".$data['discord_url'].",bluesky_url=".$data['bluesky_url'].",bluesky_handle='".$data['bluesky_handle']."',status=".$data['status'].",ping_token=".$data['ping_token'].",is_historical=".$data['is_historical'].",archive_url=".$data['archive_url'].",hentai_category=".$data['hentai_category'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);

			if (!empty($_FILES['icon'])) {
				move_uploaded_file($_FILES['icon']["tmp_name"], STATIC_DIRECTORY.'/images/icons/'.$data['id'].'.png');
			}
			
			edit_fansub_user($data['id'], $data['old_username'], $data['user_password']);
		}
		else {
			log_action("create-fansub", "S’ha creat el fansub «".$_POST['name']."»");
			query("INSERT INTO fansub (name,slug,type,url,email,twitter_url,twitter_handle,mastodon_url,mastodon_handle,discord_url,bluesky_handle,bluesky_url,status,ping_token,is_historical,archive_url,hentai_category,created,created_by,updated,updated_by) VALUES ('".$data['name']."','".$data['slug']."','".$data['type']."',".$data['url'].",'".$data['email']."',".$data['twitter_url'].",'".$data['twitter_handle']."',".$data['mastodon_url'].",'".$data['mastodon_handle']."',".$data['discord_url'].",'".$data['bluesky_handle']."',".$data['bluesky_url'].",".$data['status'].",".$data['ping_token'].",".$data['is_historical'].",".$data['archive_url'].",".$data['hentai_category'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
			
			$fansub_id=mysqli_insert_id($db_connection);

			if (!empty($_FILES['icon'])) {
				move_uploaded_file($_FILES['icon']["tmp_name"], STATIC_DIRECTORY.'/images/icons/'.$fansub_id.'.png');
			}
			
			add_fansub_user($fansub_id, $data['user_password']);
		}

		$_SESSION['message']="S’han desat les dades correctament.";

		header("Location: fansub_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT f.*, (SELECT u.username FROM user u WHERE u.fansub_id=f.id) old_username FROM fansub f WHERE id=".escape($_GET['id']));
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
					<div class="mb-3">
						<label for="form-name-with-autocomplete" class="mandatory">Nom</label> <?php print_helper_box('Nom', 'Nom complet del fansub.'); ?>
						<input class="form-control" name="name" id="form-name-with-autocomplete" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>">
						<input type="hidden" id="form-id" name="id" value="<?php echo $row['id']; ?>">
						<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
						<input type="hidden" name="old_username" value="<?php echo $row['old_username']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-slug">Identificador<span class="mandatory"></span> <?php print_helper_box('Identificador', 'Text curt que es farà servir a l’URL per a enllaçar a continguts d’aquest fansub.\n\nNormalment s’autogenera i no cal editar-lo.'); ?></label>
						<input class="form-control" name="slug" id="form-slug" required maxlength="200" value="<?php echo htmlspecialchars($row['slug']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-type">Tipus</label> <?php print_helper_box('Tipus', 'Indica si és un fansub o un fandub.\n\nSi el mateix grup fa fansubs i fandubs, caldrà crear-ne dos de diferents i diferenciar-los pel nom («Fansub» i «Fansub (doblatges)», per exemple).'); ?>
						<select class="form-select" name="type" id="form-type" required>
							<option value="">- Selecciona un tipus -</option>
							<option value="fansub"<?php echo $row['type']=='fansub' ? " selected" : ""; ?>>Fansub</option>
							<option value="fandub"<?php echo $row['type']=='fandub' ? " selected" : ""; ?>>Fandub</option>
						</select>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="mb-3">
								<label>Icona<?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?> <?php print_helper_box('Icona', 'Logo del fansub que es mostra al web en tot el contingut associat a un fansub.\n\nHa de ser un fitxer PNG de mida exacta 192x192 píxels.'); ?> <small class="text-muted">(PNG, mida 192x192px)</small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/icons/'.$row['id'].'.png');
?>
								<label for="form-icon" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? 'Canvia la imatge...' : 'Puja una imatge...' ; ?></label>
								<input class="form-control d-none" name="icon" type="file" accept="image/png" id="form-icon" onchange="checkImageUpload(this, -1, 'image/png', 192, 192, 192, 192, 'form-icon-preview', 'form-icon-preview-link');">
							</div>
						</div>
						<div class="col-sm-2" style="align-self: center;">
							<div class="mb-3">
								<a id="form-icon-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/icons/'.$row['id'].'.png" data-original="'.STATIC_URL.'/images/icons/'.$row['id'].'.png"' : ''; ?> target="_blank">
									<img id="form-icon-preview" style="width: 60px; height: 60px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/icons/'.$row['id'].'.png" data-original="'.STATIC_URL.'/images/icons/'.$row['id'].'.png"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="form-url">URL</label> <?php print_helper_box('URL', 'Adreça URL completa del lloc web del fansub.\n\nSi no en té, es deixa en blanc.'); ?>
						<input class="form-control" type="url" name="url" id="form-url" maxlength="200" value="<?php echo htmlspecialchars($row['url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-email">Adreça electrònica<span class="mandatory"></label> <?php print_helper_box('Adreça electrònica', 'Adreça electrònica del fansub.\n\nÉs necessària per a crear el seu perfil d’usuari i de comunitat.'); ?>
						<input class="form-control" type="email" name="email" id="form-email" maxlength="200" value="<?php echo htmlspecialchars($row['email']); ?>" required>
					</div>
					<div class="mb-3">
						<label for="form-user_password">Contrasenya de l’usuari públic<?php echo !empty($row['id']) ? ' (introdueix-la només si la vols canviar)' : '<span class="mandatory">'; ?></label> <?php print_helper_box('Contrasenya de l’usuari públic', 'Contrasenya de l’usuari al web públic.\n\nÉs necessària per a crear el seu perfil d’usuari i de comunitat.\n\nCada fansub disposa d’un usuari públic al web (amb nom d’usuari igual al seu nom de fansub) i hi pot iniciar la sessió amb aquesta contrasenya. Es pot fer servir per a respondre a la comunitat en nom del fansub.'); ?>
						<input class="form-control" type="password" name="user_password" id="form-user_password" maxlength="200" minlength="6"autocomplete="new-password" value=""<?php echo !empty($row['id']) ? '' : ' required'; ?>>
					</div>
					<div class="mb-3">
						<label for="form-bluesky_url">URL del perfil a Bluesky</label> <?php print_helper_box('URL del perfil a Bluesky', 'Adreça URL completa del perfil del fansub a Bluesky.\n\nSi no en té, es deixa en blanc.'); ?>
						<input class="form-control" type="url" name="bluesky_url" id="form-bluesky_url" maxlength="200" value="<?php echo htmlspecialchars($row['bluesky_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-bluesky_handle">Nom a Bluesky<span class="mandatory"></span> <?php print_helper_box('Nom a Bluesky', 'Identificador del perfil del fansub a Bluesky incloent l’arrova.\n\nSi no en té, cal posar-hi el nom complet del fansub.\n\nS’utilitza per a esmentar el fansub a les publicacions.'); ?></label>
						<input class="form-control" name="bluesky_handle" id="form-bluesky_handle" required maxlength="200" value="<?php echo htmlspecialchars($row['bluesky_handle']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-discord_url">URL del servidor de Discord públic del fansub</label> <?php print_helper_box('URL del servidor de Discord públic del fansub', 'Adreça URL completa del Discord públic del fansub.\n\nSi no en té, es deixa en blanc.'); ?>
						<input class="form-control" type="url" name="discord_url" id="form-discord_url" maxlength="200" value="<?php echo htmlspecialchars($row['discord_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-mastodon_url">URL del perfil a Mastodon</label> <?php print_helper_box('URL del perfil a Mastodon', 'Adreça URL completa del perfil del fansub a Mastodon.\n\nSi no en té, es deixa en blanc.'); ?>
						<input class="form-control" type="url" name="mastodon_url" id="form-mastodon_url" maxlength="200" value="<?php echo htmlspecialchars($row['mastodon_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-mastodon_handle">Nom a Mastodon<span class="mandatory"></span> <?php print_helper_box('Nom a Mastodon', 'Identificador del perfil del fansub a Mastodon incloent l’arrova.\n\nSi no en té, cal posar-hi el nom complet del fansub.\n\nS’utilitza per a esmentar el fansub a les publicacions.'); ?></label>
						<input class="form-control" name="mastodon_handle" id="form-mastodon_handle" required maxlength="200" value="<?php echo htmlspecialchars($row['mastodon_handle']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-twitter_url">URL del perfil a X</label> <?php print_helper_box('URL del perfil a X', 'Adreça URL completa del perfil del fansub a X.\n\nSi no en té, es deixa en blanc.'); ?>
						<input class="form-control" type="url" name="twitter_url" id="form-twitter_url" maxlength="200" value="<?php echo htmlspecialchars($row['twitter_url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-twitter_handle">Nom a X<span class="mandatory"></span> <?php print_helper_box('Nom a X', 'Identificador del perfil del fansub a X incloent l’arrova.\n\nSi no en té, cal posar-hi el nom complet del fansub.\n\nS’utilitza per a esmentar el fansub a les publicacions.'); ?></label>
						<input class="form-control" name="twitter_handle" id="form-twitter_handle" required maxlength="200" value="<?php echo htmlspecialchars($row['twitter_handle']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-status">Estat</label> <?php print_helper_box('Estat', 'Especifica si el fansub està en actiu o és històric.\n\nEs consideren inactius els fansubs que fa anys que no publiquen cap contingut, independentment de l’estat del seu web.\n\nEs consideren històrics tots els fansubs que han desaparegut i ja no tenen web.'); ?>
						<div id="form-status">
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="status" id="form-active" value="1"<?php echo $row['status']==1? " checked" : ""; ?>>
								<label class="form-check-label" for="form-active">Actiu</label>
							</div>
							<div class="form-check form-check-inline">
								<input class="form-check-input" type="checkbox" name="is_historical" id="form-is_historical" value="1"<?php echo $row['is_historical']==1? " checked" : ""; ?> onchange="$('#form-archive_url').prop('disabled',!$(this).prop('checked'));">
								<label class="form-check-label" for="form-is_historical">Històric</label>
							</div>
						</div>
					</div>
					<div class="mb-3">
						<label for="form-hentai_category">Edició de hentai</label> <?php print_helper_box('Edició de hentai', 'Indica si el fansub edita hentai. S’utilitza per a mostrar-lo a Hentai.cat o no.\n\nSi no edita mai hentai, no apareixerà a Hentai.cat.\n\nSi només edita hentai, no apareixerà a Fansubs.cat.\n\nSi edita hentai de vegades, apareixerà a tots dos portals, però les notícies es filtraran depenent de si contenen les paraules «hentai», «yaoi» o «yuri» i les coincidents només es mostraran al portal corresponent.'); ?>
						<select class="form-select" name="hentai_category" id="form-hentai_category" required>
							<option value="0"<?php echo $row['hentai_category']==0 ? " selected" : ""; ?>>No edita mai hentai</option>
							<option value="1"<?php echo $row['hentai_category']==1 ? " selected" : ""; ?>>De vegades edita hentai</option>
							<option value="2"<?php echo $row['hentai_category']==2 ? " selected" : ""; ?>>Únicament edita hentai</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="form-archive_url">URL d’Archive.org <?php print_helper_box('URL d’Archive.org', 'Adreça URL completa del web del fansub a Archive.org.\n\nNomés s’omple quan el fansub és històric: en aquest cas, el camp és obligatori.'); ?></label>
						<input class="form-control" type="url" name="archive_url" id="form-archive_url" maxlength="200"<?php echo $row['is_historical']==0? " disabled" : ""; ?> value="<?php echo htmlspecialchars($row['archive_url']); ?>" required>
					</div>
					<div class="mb-3">
						<label for="form-ping_token">Testimoni per a fer ping <?php print_helper_box('Testimoni per a fer ping', 'Defineix un testimoni perquè el codi del web del fansub pugui fer una crida interna a Fansubs.cat.\n\nS’utilitza per a forçar una actualització de les notícies (i en els fansubs que en tenen configurada l’obtenció a petició): si es fa una petició a '.API_URL.'/refresh/{testimoni}, s’actualitzaran les notícies al moment.\n\nSi no saps què és això, és segur deixar el camp en blanc.'); ?></label>
						<input class="form-control" name="ping_token" id="form-ping_token" maxlength="200" value="<?php echo htmlspecialchars($row['ping_token']); ?>">
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix el fansub"; ?></button>
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
