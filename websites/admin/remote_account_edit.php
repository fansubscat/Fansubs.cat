<?php
$header_title="Edició de comptes remots - Fansubs";
$page="fansub";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=2) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash("Dades invàlides: manca id");
		}
		if (!empty($_POST['fansub_id']) && is_numeric($_POST['fansub_id'])) {
			$data['fansub_id']=escape($_POST['fansub_id']);
		} else {
			$data['fansub_id']='NULL';
		}
		if (!empty($_POST['name'])) {
			$data['name']=escape($_POST['name']);
		} else {
			crash("Dades invàlides: manca name");
		}
		if (!empty($_POST['token'])) {
			$data['token']=escape($_POST['token']);
		} else {
			crash("Dades invàlides: manca token");
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-remote-account", "S’ha actualitzat el compte remot «".$_POST['name']."» (id. de compte remot: ".$data['id'].")");
			query("UPDATE remote_account SET name='".$data['name']."',token='".$data['token']."',fansub_id=".$data['fansub_id'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
		}
		else {
			log_action("create-remote-account", "S’ha creat el compte remot «".$_POST['name']."»");
			query("INSERT INTO remote_account (name,token,fansub_id,total_storage, used_storage, created,created_by,updated,updated_by) VALUES ('".$data['name']."','".$data['token']."',".$data['fansub_id'].",0,0,CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
		}

		$_SESSION['message']="S’han desat les dades correctament.";

		header("Location: remote_account_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT a.* FROM remote_account a WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Remote account not found');
		mysqli_free_result($result);
	} else {
		$row = array();
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita el compte remot" : "Afegeix un compte remot"; ?></h4>
				<hr>
				<form method="post" action="remote_account_edit.php">
					<div class="mb-3">
						<label for="form-name">Compte<span class="mandatory"></span> <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="Compte" data-bs-contents="Adreça electrònica del compte. Correspon a l’adreça de correu utilitzada per a iniciar la sessió al compte de MEGA."></small></label>
						<input class="form-control" name="name" type="email" id="form-name" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>">
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-storage">Ús de l’emmagatzematge <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="Ús de l’emmagatzematge" data-bs-contents="Indica l’emmagatzematge utilitzat al compte. S’actualitza una vegada al dia. Si has afegit el compte fa poc, no està disponible."></small></label>
						<input class="form-control" name="storage" id="form-storage" value="<?php echo $row['total_storage']!=0 ? (number_format($row['used_storage']/$row['total_storage']*100, 2, ',')).'% ('.number_format($row['used_storage']/1024/1024/1024, 2, ',').'/'.number_format($row['total_storage']/1024/1024/1024, 2, ',').' GB)' : 'No disponible'; ?>" readonly>
					</div>
					<div class="mb-3">
						<label for="form-token">Identificador de sessió<span class="mandatory"></span> <small data-bs-toggle="modal" data-bs-target="#generic-modal" class="text-muted fa fa-question-circle modal-help-button" data-bs-title="Identificador de sessió" data-bs-contents="Identificador de sessió obtingut amb MegaCMD. Això ens permet iniciar la sessió amb aquest compte sense fer servir una contrasenya i obtenir-ne enllaços automàticament i actualitzar-ne l’estat de l’emmagatzematge. Per a més informació de com obtenir-lo, consulteu el manual del tauler d’administració a la icona d’ajuda de la part superior dreta del web (apartat «Com obtenir l’identificador de sessió»)."></small></label>
						<input class="form-control" name="token" id="form-token" required maxlength="200" value="<?php echo htmlspecialchars($row['token']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-fansub_id">Fansub</label>
						<select name="fansub_id" class="form-select" id="form-fansub_id">
							<option value="">- Intern de Fansubs.cat -</option>
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
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix el compte remot"; ?></button>
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
