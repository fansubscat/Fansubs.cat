<?php
$header_title="Llista de recollidors - Notícies";
$page="news";
include("header.inc.php");

function get_method($method){
	switch($method){
		case 'animugen':
			return "AniMugen";
		case 'blogspot':
			return "Blogspot (genèric)";
		case 'blogspot_2nf':
			return "Blogspot (2nB no Fansub)";
		case 'blogspot_as':
			return "Blogspot (AnliumSubs)";
		case 'blogspot_bsc':
			return "Blogspot (Bleach - Sub Català)";
		case 'blogspot_dnf':
			return "Blogspot (Dragon no Fansub)";
		case 'blogspot_llpnf':
			return "Blogspot (Lluna Plena no Fansub)";
		case 'blogspot_mnf':
			return "Blogspot (Manga no Fansub)";
		case 'blogspot_snf':
			return "Blogspot (Seireitei no Fansub)";
		case 'blogspot_shinsengumi':
			return "Blogspot (Shinsengumi no Fansub)";
		case 'blogspot_teqma':
			return "Blogspot (Tot el que m'agrada)";
		case 'blogspot_tnf':
			return "Blogspot (Tohoshinki no Fansub)";
		case 'catsub':
			return "CatSub";
		case 'mangadex_edcec':
			return "Mangadex (El Detectiu Conan en català)";
		case 'ouferrat':
			return "Ou ferrat";
		case 'phpbb_dnf':
			return "phpBB (Dragon no Fansub)";
		case 'phpbb_llpnf':
			return "phpBB (Lluna Plena no Fansub)";
		case 'roninfansub':
			return "Rōnin Fansub";
		case 'weebly_rnnf':
			return "Weebly (RuffyNatsu no Fansub)";
		case 'wordpress_arf':
			return "Wordpress (ARFansub)";
		case 'wordpress_ddc':
			return "Wordpress (Dengeki Daisy Cat)";
		case 'wordpress_mdcf':
			return "Wordpress (Món Detectiu Conan Fansub)";
		case 'wordpress_xf':
			return "Wordpress (XOP Fansub)";
		case 'wordpress_ynf':
			return "Wordpress (Yoshiwara no Fansub)";
		default:
			return $method;
	}
}

function get_fetch_type($fetch_type){
	switch ($fetch_type){
		case 'periodic':
			return 'Periòdic';
		case 'onrequest':
			return 'A petició';
		case 'onetime_retired':
			return 'Una vegada (retirat)';
		case 'onetime_inactive':
			return 'Una vegada (inactiu)';
		default:
			return $fetch_type;
	}
}

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
		log_action("delete-fetcher", "S'ha suprimit el recollidor amb URL '".query_single("SELECT url FROM fetcher WHERE id=".escape($_GET['delete_id']))."' (id. de recollidor: ".$_GET['delete_id'].")");
		query("UPDATE news SET fetcher_id=NULL WHERE fetcher_id=".escape($_GET['delete_id']));
		query("DELETE FROM fetcher WHERE id=".escape($_GET['delete_id']));
		$_SESSION['message']="S'ha suprimit correctament.";
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
				<article class="card-body">
					<h4 class="card-title text-center mb-4 mt-1">Llista de recollidors</h4>
					<hr>

<?php
	if (!empty($_SESSION['message'])) {
?>
					<p class="alert alert-success text-center"><?php echo $_SESSION['message']; ?></p>
<?php
		$_SESSION['message']=NULL;
	}
?>
					<table class="table table-hover table-striped">
						<thead class="thead-dark">
							<tr>
								<th scope="col">Fansub</th>
								<th scope="col">URL</th>
								<th scope="col">Mètode</th>
								<th scope="col">Freqüència</th>
								<th class="text-center" scope="col">Accions</th>
							</tr>
						</thead>
						<tbody>
<?php
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE fe.fansub_id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT fe.*, f.name fansub_name FROM fetcher fe LEFT JOIN fansub f ON fe.fansub_id=f.id$where ORDER BY f.name ASC, fe.url ASC");
	if (mysqli_num_rows($result)==0) {
?>
							<tr>
								<td colspan="5" class="text-center">- No hi ha cap recollidor -</td>
							</tr>
<?php
	}
	while ($row = mysqli_fetch_assoc($result)) {
?>
							<tr>
								<th scope="row" class="align-middle"><?php echo !empty($row['fansub_name']) ? htmlspecialchars($row['fansub_name']) : '(Tots)'; ?></th>
								<td class="align-middle small"><?php echo htmlspecialchars($row['url']); ?></td>
								<td class="align-middle"><?php echo htmlspecialchars(get_method($row['method'])); ?></th>
								<td class="align-middle"><?php echo htmlspecialchars(get_fetch_type($row['fetch_type'])); ?></th>
								<td class="align-middle text-center"><a href="fetcher_edit.php?id=<?php echo $row['id']; ?>" title="Modifica" class="fa fa-edit p-1"></a> <a href="fetcher_list.php?delete_id=<?php echo $row['id']; ?>" title="Suprimeix" onclick="return confirm(<?php echo htmlspecialchars(json_encode("Segur que vols suprimir el recollidor de '".$row['url']."'? L'acció no es podrà desfer. No se'n suprimiran les notícies.")); ?>)" onauxclick="return false;" class="fa fa-trash p-1 text-danger"></a></td>
							</tr>
<?php
	}
	mysqli_free_result($result);
?>
						</tbody>
					</table>
					<div class="text-center">
						<a href="fetcher_edit.php" class="btn btn-primary"><span class="fa fa-plus pr-2"></span>Afegeix un recollidor</a>
					</div>
				</article>
			</div>
		</div>
<?php
} else {
	header("Location: login.php");
}

include("footer.inc.php");
?>
