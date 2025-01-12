<?php
$header_title="Darrers comentaris";
$page="analytics";

include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['text'])) {
			$data['text']=escape($_POST['text']);
		} else {
			crash("Dades invàlides: manca text");
		}
		if (!empty($_POST['reply_to_comment_id']) && is_numeric($_POST['reply_to_comment_id'])) {
			$data['reply_to_comment_id']=escape($_POST['reply_to_comment_id']);
		} else {
			$data['reply_to_comment_id']='NULL';
		}
		if (!empty($_POST['version_id']) && is_numeric($_POST['version_id'])) {
			$data['version_id']=escape($_POST['version_id']);
		} else {
			crash("Dades invàlides: manca version_id");
		}
		if (!empty($_POST['fansub_id']) && is_numeric($_POST['fansub_id'])) {
			if ($_POST['fansub_id']==-1) {
				$data['fansub_id']='NULL';
				$data['type']='admin';
			} else {
				$data['fansub_id']=escape($_POST['fansub_id']);
				$data['type']='fansub';
			}
		} else {
			crash("Dades invàlides: manca fansub_id");
		}
		
		if ($_POST['action']=='reply') {
			log_action("reply-comment", "S’ha respost al comentari amb identificador ".$_POST['reply_to_comment_id']);
			query("INSERT INTO comment (user_id, version_id, type, fansub_id, reply_to_comment_id, last_replied, text, created, updated)
			VALUES (NULL, ".$data['version_id'].", '".$data['type']."', ".$data['fansub_id'].", ".$data['reply_to_comment_id'].", CURRENT_TIMESTAMP, '".$data['text']."', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)");
			query("UPDATE comment SET last_replied=CURRENT_TIMESTAMP WHERE id=".$data['reply_to_comment_id']);
		}

		$_SESSION['message']="S’han desat les dades correctament.";

		if (!empty($_POST['source_version_id'])) {
			header('Location: version_stats.php?type='.$_POST['source_type'].'&id='.$_POST['source_version_id']);
		} else {
			header("Location: comment_list.php");
		}
		die();
	}

	if (!empty($_GET['id'])) {
		$result = query("SELECT * FROM comment WHERE id='".escape($_GET['id'])."'");
		$row = mysqli_fetch_assoc($result) or crash('Comment not found');
		mysqli_free_result($result);
	} else {
		$row = array();
		$row['id'] = '';
		$row['version_id'] = escape($_GET['source_version_id']);
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? 'Respon a' : 'Escriu'; ?> un comentari</h4>
					<hr>
					<form method="post" action="comment_reply.php">
<?php
	if (!empty($row['id'])) {
?>
						<div class="mb-3">
							<label for="form-original_comment">Comentari original</label> <?php print_helper_box('Comentari original', 'Mostra el comentari original de l’usuari al qual respons.'); ?>
							<div class="form-control" id="form-original_comment"><?php echo htmlspecialchars($row['text']); ?></div>
						</div>
<?php
	}
?>
						<div class="mb-3">
							<label for="form-fansub"><?php echo !empty($row['id']) ? 'Respon' : 'Escriu'; ?> en nom de<span class="mandatory"></span></label> <?php print_helper_box((!empty($row['id']) ? 'Respon' : 'Escriu').' en nom de', 'Defineix en nom de quin fansub parles.'); ?>
							<select name="fansub_id" class="form-select" id="form-fansub" required>
<?php
	if ($_SESSION['admin_level']>=3) {
		if (!empty($row['id'])) {
			$result = query("SELECT f.* FROM rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE vf.version_id=(SELECT c.version_id FROM comment c WHERE c.id=".escape($row['id']).")");
		} else {
			$result = query("SELECT f.* FROM rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id WHERE vf.version_id=".escape($row['version_id']));
		}
?>
								<option value="-1" selected>Fansubs.cat</option>
<?php
	} else if (!empty($_SESSION['fansub_id'])) {
		$result = query("SELECT f.* FROM fansub f WHERE f.id=".escape($_SESSION['fansub_id']));
	}
	while ($frow = mysqli_fetch_assoc($result)) {
?>
								<option value="<?php echo $frow['id']; ?>"<?php echo $_SESSION['fansub_id']==$frow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
							</select>
						</div>
						<div class="mb-3">
							<label for="form-text" class="mandatory">Text <?php echo !empty($row['id']) ? 'de la resposta' : 'del comentari'; ?></label> <?php print_helper_box('Text '.(!empty($row['id']) ? 'de la resposta' : 'del comentari'), 'S’hi introdueix el text que es vol afegir com a comentari.'); ?>
							<textarea class="form-control" name="text" id="form-text" required style="height: 150px;"></textarea>
							<input type="hidden" name="reply_to_comment_id" value="<?php echo htmlspecialchars($row['id']); ?>">
							<input type="hidden" name="version_id" value="<?php echo htmlspecialchars($row['version_id']); ?>">
							<input type="hidden" name="source_version_id" value="<?php echo !empty($_GET['source_version_id']) ? htmlspecialchars($_GET['source_version_id']) : ''; ?>">
							<input type="hidden" name="source_type" value="<?php echo !empty($_GET['source_type']) ? htmlspecialchars($_GET['source_type']) : ''; ?>">
						</div>
						<div class="mb-3 text-center pt-2">
							<button type="submit" name="action" value="reply" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span>Publica <?php echo !empty($row['id']) ? 'la resposta' : 'el comentari'; ?></button>
						</div>
					</form>
					
				</article>
			</div>
		</div>
<?php
}

else{
	header("Location: login.php");
}



include(__DIR__.'/footer.inc.php');
?>
