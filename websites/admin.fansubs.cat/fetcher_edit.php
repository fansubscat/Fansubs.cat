<?php
$header_title="Edició de recollidors - Notícies";
$page="news";
include("header.inc.php");

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
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
			crash("Dades invàlides: manca fansub_id");
		}
		if (!empty($_POST['url'])) {
			$data['url']=escape($_POST['url']);
		} else {
			crash("Dades invàlides: manca url");
		}
		if (!empty($_POST['method'])) {
			$data['method']=escape($_POST['method']);
		} else {
			crash("Dades invàlides: manca method");
		}
		if (!empty($_POST['fetch_type'])) {
			$data['fetch_type']=escape($_POST['fetch_type']);
		} else {
			crash("Dades invàlides: manca fetch_type");
		}
		
		if ($_POST['action']=='edit') {
			log_action("update-fetcher", "S'ha actualitzat el recollidor amb URL '".$data['url']."' (id. de recollidor: ".$data['id'].")");
			query("UPDATE fetcher SET fansub_id=".$data['fansub_id'].",url='".$data['url']."',method='".$data['method']."',fetch_type='".$data['fetch_type']."',updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
		}
		else {
			log_action("create-fetcher", "S'ha creat un recollidor amb URL '".$data['url']."'");
			query("INSERT INTO fetcher (fansub_id,url,method,fetch_type,status,last_fetch_result,last_fetch_date,last_fetch_increment,created,created_by,updated,updated_by) VALUES (".$data['fansub_id'].",'".$data['url']."','".$data['method']."','".$data['fetch_type']."','idle',NULL,NULL,NULL,CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
		}

		$_SESSION['message']="S'han desat les dades correctament.";

		header("Location: fetcher_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT f.* FROM fetcher f WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash('Fetcher not found');
		mysqli_free_result($result);
	} else {
		$row = array();
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? "Edita el recollidor" : "Afegeix un recollidor"; ?></h4>
				<hr>
				<form method="post" action="fetcher_edit.php">
					<div class="form-group">
						<label for="form-fansub_id" class="mandatory">Fansub</label>
						<select name="fansub_id" class="form-control" id="form-fansub_id" required>
							<option value="">- Selecciona un fansub -</option>
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
					<div class="form-group">
						<label for="form-url" class="mandatory">URL</label>
						<input class="form-control" name="url" id="form-url" required value="<?php echo htmlspecialchars($row['url']); ?>">
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
					</div>
					<div class="form-group">
						<label for="form-method" class="mandatory">Mètode de recollida</label>
						<select name="method" class="form-control" id="form-method" required>
							<option value="">- Selecciona un mètode -</option>
							<option value="animugen"<?php echo $row['method']=='animugen' ? " selected" : ""; ?>>AniMugen</option>
							<option value="blogspot"<?php echo $row['method']=='blogspot' ? " selected" : ""; ?>>Blogspot (genèric)</option>
							<option value="blogspot_2nf"<?php echo $row['method']=='blogspot_2nf' ? " selected" : ""; ?>>Blogspot (2nB no Fansub)</option>
							<option value="blogspot_as"<?php echo $row['method']=='blogspot_as' ? " selected" : ""; ?>>Blogspot (AnliumSubs)</option>
							<option value="blogspot_bsc"<?php echo $row['method']=='blogspot_bsc' ? " selected" : ""; ?>>Blogspot (Bleach - Sub Català)</option>
							<option value="blogspot_dnf"<?php echo $row['method']=='blogspot_dnf' ? " selected" : ""; ?>>Blogspot (Dragon no Fansub)</option>
							<option value="blogspot_llpnf"<?php echo $row['method']=='blogspot_llpnf' ? " selected" : ""; ?>>Blogspot (Lluna Plena no Fansub)</option>
							<option value="blogspot_mnf"<?php echo $row['method']=='blogspot_mnf' ? " selected" : ""; ?>>Blogspot (Manga no Fansub)</option>
							<option value="blogspot_snf"<?php echo $row['method']=='blogspot_snf' ? " selected" : ""; ?>>Blogspot (Seireitei no Fansub)</option>
							<option value="blogspot_shinsengumi"<?php echo $row['method']=='blogspot_shinsengumi' ? " selected" : ""; ?>>Blogspot (Shinsengumi no Fansub)</option>
							<option value="blogspot_teqma"<?php echo $row['method']=='blogspot_teqma' ? " selected" : ""; ?>>Blogspot (Tot el que m'agrada)</option>
							<option value="blogspot_tnf"<?php echo $row['method']=='blogspot_tnf' ? " selected" : ""; ?>>Blogspot (Tohoshinki no Fansub)</option>
							<option value="catsub"<?php echo $row['method']=='catsub' ? " selected" : ""; ?>>CatSub</option>
							<option value="mangadex_edcec"<?php echo $row['method']=='mangadex_edcec' ? " selected" : ""; ?>>Mangadex (El Detectiu Conan en català)</option>
							<option value="phpbb_dnf"<?php echo $row['method']=='phpbb_dnf' ? " selected" : ""; ?>>phpBB (Dragon no Fansub)</option>
							<option value="phpbb_llpnf"<?php echo $row['method']=='phpbb_llpnf' ? " selected" : ""; ?>>phpBB (Lluna Plena no Fansub)</option>
							<option value="roninfansub"<?php echo $row['method']=='roninfansub' ? " selected" : ""; ?>>Rōnin Fansub</option>
							<option value="weebly_rnnf"<?php echo $row['method']=='weebly_rnnf' ? " selected" : ""; ?>>Weebly (RuffyNatsu no Fansub)</option>
							<option value="wordpress_arf"<?php echo $row['method']=='wordpress_arf' ? " selected" : ""; ?>>Wordpress (ARFansub)</option>
							<option value="wordpress_ddc"<?php echo $row['method']=='wordpress_ddc' ? " selected" : ""; ?>>Wordpress (Dengeki Daisy Cat)</option>
							<option value="wordpress_mdcf"<?php echo $row['method']=='wordpress_mdcf' ? " selected" : ""; ?>>Wordpress (Món Detectiu Conan Fansub)</option>
							<option value="wordpress_xf"<?php echo $row['method']=='wordpress_xf' ? " selected" : ""; ?>>Wordpress (XOP Fansub)</option>
							<option value="wordpress_ynf"<?php echo $row['method']=='wordpress_ynf' ? " selected" : ""; ?>>Wordpress (Yoshiwara no Fansub)</option>
						</select>
					</div>
					<div class="form-group">
						<label for="form-fetch_type" class="mandatory">Freqüència de recollida</label>
						<select name="fetch_type" class="form-control" id="form-fetch_type" required>
							<option value="">- Selecciona una freqüència -</option>
							<option value="periodic"<?php echo $row['fetch_type']=='periodic' ? " selected" : ""; ?>>Periòdica (cada 15 minuts)</option>
							<option value="onrequest"<?php echo $row['fetch_type']=='onrequest' ? " selected" : ""; ?>>Només a petició (amb testimoni de ping)</option>
							<option value="onetime_retired"<?php echo $row['fetch_type']=='onetime_retired' ? " selected" : ""; ?>>Només obtenció inicial (URL retirada)</option>
							<option value="onetime_inactive"<?php echo $row['fetch_type']=='onetime_inactive' ? " selected" : ""; ?>>Només obtenció inicial (URL inactiva)</option>
						</select>
					</div>
					<div class="form-group text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary font-weight-bold"><span class="fa fa-check pr-2"></span><?php echo !empty($row['id']) ? "Desa els canvis" : "Afegeix el recollidor"; ?></button>
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
