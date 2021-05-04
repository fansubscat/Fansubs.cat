<?php
$header_title="Edició de comptes - Comptes";
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
		if (!empty($_POST['type'])) {
			$data['type']=escape($_POST['type']);
		} else {
			crash("Dades invàlides: manca type");
		}
		if (!empty($_POST['session_id'])) {
			$data['session_id']=escape($_POST['session_id']);
		} else {
			crash("Dades invàlides: manca session_id");
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-account", "S'ha actualitzat el compte '".$data['name']."' (id. de compte: ".$data['id'].")");
			query("UPDATE account SET name='".$data['name']."',type='".$data['type']."',session_id='".$data['session_id']."',fansub_id=".$data['fansub_id'].",updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
		}
		else {
			log_action("create-account", "S'ha creat el compte '".$data['name']."'");
			query("INSERT INTO account (name,type,session_id,fansub_id,created,created_by,updated,updated_by) VALUES ('".$data['name']."','".$data['type']."','".$data['session_id']."',".$data['fansub_id'].",CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
		}

		$_SESSION['message']="S'han desat les dades correctament.";

		header("Location: account_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT a.* FROM account a WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Account not found');
		mysqli_free_result($result);
	} else {
		$row = array();
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita el compte" : "Afegeix un compte"; ?></h4>
				<hr>
				<form method="post" action="account_edit.php">
					<div class="form-group">
						<label for="form-name" class="mandatory">Nom</label>
						<input class="form-control" name="name" id="form-name" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>">
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
					</div>
					<div class="form-group">
						<label for="form-type" class="mandatory">Tipus</label>
						<select name="type" class="form-control" id="form-type" required>
							<option value="">- Selecciona -</option>
							<option value="googledrive"<?php echo $row['type']=='googledrive' ? " selected" : ""; ?>>Google Drive</option>
							<option value="mega"<?php echo $row['type']=='mega' ? " selected" : ""; ?>>MEGA</option>
							<option value="storage"<?php echo $row['type']=='storage' ? " selected" : ""; ?>>Emmagatzematge</option>
						</select>
					</div>
					<div class="form-group">
						<label for="form-session_id" class="mandatory">Id. de sessió (MEGA) / Id. d'unitat compartida (Google Drive) / URL base (emmagatzematge)</label>
						<input class="form-control" name="session_id" id="form-session_id" required maxlength="200" value="<?php echo htmlspecialchars($row['session_id']); ?>">
					</div>
					<div class="form-group">
						<label for="form-fansub_id">Fansub</label>
						<select name="fansub_id" class="form-control" id="form-fansub_id">
							<option value="">- Qualsevol fansub hi té accés -</option>
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
					<div class="form-group text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix el compte"; ?></button>
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
