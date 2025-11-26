<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.news_fetcher_edit.header');
$page="news";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.id_missing'));
		}
		if (!empty($_POST['fansub_id']) && is_numeric($_POST['fansub_id'])) {
			$data['fansub_id']=escape($_POST['fansub_id']);
		} else {
			crash(lang('admin.error.fansub_id_missing'));
		}
		if (!empty($_POST['url'])) {
			$data['url']=escape($_POST['url']);
		} else {
			crash(lang('admin.error.url_missing'));
		}
		if (!empty($_POST['method'])) {
			$data['method']=escape($_POST['method']);
		} else {
			crash(lang('admin.error.method_missing'));
		}
		if (!empty($_POST['fetch_type'])) {
			$data['fetch_type']=escape($_POST['fetch_type']);
		} else {
			crash(lang('admin.error.fetch_type_missing'));
		}
		
		if ($_POST['action']=='edit') {
			$old_result = query("SELECT * FROM news_fetcher WHERE id=".$data['id']);
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash(lang('admin.error.news_fetcher_edit_concurrency_error'));
			}
			
			log_action("update-news-fetcher", "News fetcher with URL «".$_POST['url']."» (news fetcher id: ".$data['id'].") updated");
			query("UPDATE news_fetcher SET fansub_id=".$data['fansub_id'].",url='".$data['url']."',method='".$data['method']."',fetch_type='".$data['fetch_type']."',updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);
		}
		else {
			log_action("create-news-fetcher", "News fetcher with URL «".$_POST['url']."» created");
			query("INSERT INTO news_fetcher (fansub_id,url,method,fetch_type,status,last_fetch_result,last_fetch_date,last_fetch_increment,created,created_by,updated,updated_by) VALUES (".$data['fansub_id'].",'".$data['url']."','".$data['method']."','".$data['fetch_type']."','idle',NULL,NULL,NULL,CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");
		}

		$_SESSION['message']=lang('admin.generic.data_saved');

		header("Location: news_fetcher_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT f.* FROM news_fetcher f WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.news_fetcher_not_found'));
		mysqli_free_result($result);
	} else {
		$row = array();
		$row['id']='';
		$row['fansub_id']='';
		$row['url']='';
		$row['updated']='';
		$row['method']='';
		$row['fetch_type']='';
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? lang('admin.news_fetcher_edit.edit_title') : lang('admin.news_fetcher_edit.create_title'); ?></h4>
				<hr>
				<form method="post" action="news_fetcher_edit.php">
					<div class="mb-3">
						<label for="form-fansub_id" class="mandatory"><?php echo lang('admin.news_fetcher_edit.fansub'); ?></label> <?php print_helper_box(lang('admin.news_fetcher_edit.fansub'), lang('admin.news_fetcher_edit.fansub.help')); ?>
						<select name="fansub_id" class="form-select" id="form-fansub_id" required>
							<option value=""><?php echo lang('admin.news_fetcher_edit.fansub.select'); ?></option>
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
					<div class="mb-3">
						<label for="form-url" class="mandatory"><?php echo lang('admin.news_fetcher_edit.url'); ?></label> <?php print_helper_box(lang('admin.news_fetcher_edit.url'), lang('admin.news_fetcher_edit.url.help')); ?>
						<input class="form-control" name="url" id="form-url" required value="<?php echo htmlspecialchars($row['url']); ?>">
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
						<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-method" class="mandatory"><?php echo lang('admin.news_fetcher_edit.method'); ?></label> <?php print_helper_box(lang('admin.news_fetcher_edit.method'), lang('admin.news_fetcher_edit.method.help')); ?>
						<select name="method" class="form-select" id="form-method" required>
							<option value=""><?php echo lang('admin.news_fetcher_edit.method.select'); ?></option>
							<option value="animugen"<?php echo $row['method']=='animugen' ? " selected" : ""; ?>>AniMugen</option>
							<option value="blogspot"<?php echo $row['method']=='blogspot' ? " selected" : ""; ?>><?php echo lang('admin.generic.fetch_method.blogspot_generic'); ?></option>
							<option value="blogspot_api"<?php echo $row['method']=='blogspot_api' ? " selected" : ""; ?>><?php echo lang('admin.generic.fetch_method.blogspot_api'); ?></option>
							<option value="blogspot_api_llpnf"<?php echo $row['method']=='blogspot_api_llpnf' ? " selected" : ""; ?>>Blogspot (API - Lluna Plena no Fansub)</option>
							<option value="blogspot_2nf"<?php echo $row['method']=='blogspot_2nf' ? " selected" : ""; ?>>Blogspot (2nB no Fansub)</option>
							<option value="blogspot_as"<?php echo $row['method']=='blogspot_as' ? " selected" : ""; ?>>Blogspot (AnliumSubs)</option>
							<option value="blogspot_bsc"<?php echo $row['method']=='blogspot_bsc' ? " selected" : ""; ?>>Blogspot (Bleach - Sub Català)</option>
							<option value="blogspot_dnf"<?php echo $row['method']=='blogspot_dnf' ? " selected" : ""; ?>>Blogspot (Dragon no Fansub)</option>
							<option value="blogspot_llpnf"<?php echo $row['method']=='blogspot_llpnf' ? " selected" : ""; ?>>Blogspot (Lluna Plena no Fansub)</option>
							<option value="blogspot_mnf"<?php echo $row['method']=='blogspot_mnf' ? " selected" : ""; ?>>Blogspot (Manga no Fansub)</option>
							<option value="blogspot_pnm"<?php echo $row['method']=='blogspot_pnm' ? " selected" : ""; ?>>Blogspot (Projecte Nou Món)</option>
							<option value="blogspot_snf"<?php echo $row['method']=='blogspot_snf' ? " selected" : ""; ?>>Blogspot (Seireitei no Fansub)</option>
							<option value="blogspot_shinsengumi"<?php echo $row['method']=='blogspot_shinsengumi' ? " selected" : ""; ?>>Blogspot (Shinsengumi no Fansub)</option>
							<option value="blogspot_teqma"<?php echo $row['method']=='blogspot_teqma' ? " selected" : ""; ?>>Blogspot (Tot el que m’agrada)</option>
							<option value="blogspot_tnf"<?php echo $row['method']=='blogspot_tnf' ? " selected" : ""; ?>>Blogspot (Tohoshinki no Fansub)</option>
							<option value="blogspot_uto"<?php echo $row['method']=='blogspot_uto' ? " selected" : ""; ?>>Blogspot (Un Tortosí Otaku)</option>
							<option value="catsub"<?php echo $row['method']=='catsub' ? " selected" : ""; ?>>CatSub</option>
							<option value="espurnaescarlata"<?php echo $row['method']=='espurnaescarlata' ? " selected" : ""; ?>>Espurna Escarlata</option>
							<option value="mangadex_edcec"<?php echo $row['method']=='mangadex_edcec' ? " selected" : ""; ?>>Mangadex (El Detectiu Conan en català)</option>
							<option value="ouferrat"<?php echo $row['method']=='ouferrat' ? " selected" : ""; ?>>Ou ferrat</option>
							<option value="phpbb_dnf"<?php echo $row['method']=='phpbb_dnf' ? " selected" : ""; ?>>phpBB (Dragon no Fansub)</option>
							<option value="phpbb_llpnf"<?php echo $row['method']=='phpbb_llpnf' ? " selected" : ""; ?>>phpBB (Lluna Plena no Fansub)</option>
							<option value="roninfansub"<?php echo $row['method']=='roninfansub' ? " selected" : ""; ?>>Rōnin Fansub</option>
							<option value="weebly_rnnf"<?php echo $row['method']=='weebly_rnnf' ? " selected" : ""; ?>>Weebly (RuffyNatsu no Fansub)</option>
							<option value="wordpress_arf"<?php echo $row['method']=='wordpress_arf' ? " selected" : ""; ?>>Wordpress (ARFansub)</option>
							<option value="wordpress_ddc"<?php echo $row['method']=='wordpress_ddc' ? " selected" : ""; ?>>Wordpress (Dengeki Daisy Cat)</option>
							<option value="wordpress_mdcf"<?php echo $row['method']=='wordpress_mdcf' ? " selected" : ""; ?>>Wordpress (Món Detectiu Conan Fansub)</option>
							<option value="wordpress_xf"<?php echo $row['method']=='wordpress_xf' ? " selected" : ""; ?>>Wordpress (XOP Fansub)</option>
							<option value="wordpress_ys"<?php echo $row['method']=='wordpress_ys' ? " selected" : ""; ?>>Wordpress (YacchySubs)</option>
							<option value="wordpress_ynf"<?php echo $row['method']=='wordpress_ynf' ? " selected" : ""; ?>>Wordpress (Yoshiwara no Fansub)</option>
						</select>
					</div>
					<div class="mb-3">
						<label for="form-fetch_type" class="mandatory"><?php echo lang('admin.news_fetcher_edit.fetch_type'); ?></label> <?php print_helper_box(lang('admin.news_fetcher_edit.fetch_type'), lang('admin.news_fetcher_edit.fetch_type.help')); ?>
						<select name="fetch_type" class="form-select" id="form-fetch_type" required>
							<option value=""><?php echo lang('admin.news_fetcher_edit.fetch_type.select'); ?></option>
							<option value="periodic"<?php echo $row['fetch_type']=='periodic' ? " selected" : ""; ?>><?php echo lang('admin.generic.fetch_type.periodic'); ?></option>
							<option value="onrequest"<?php echo $row['fetch_type']=='onrequest' ? " selected" : ""; ?>><?php echo lang('admin.generic.fetch_type.by_request'); ?></option>
							<option value="onetime_retired"<?php echo $row['fetch_type']=='onetime_retired' ? " selected" : ""; ?>><?php echo lang('admin.generic.fetch_type.single_retired'); ?></option>
							<option value="onetime_inactive"<?php echo $row['fetch_type']=='onetime_inactive' ? " selected" : ""; ?>><?php echo lang('admin.generic.fetch_type.single_inactive'); ?></option>
						</select>
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? lang('admin.generic.save_changes') : lang('admin.news_fetcher_edit.create_button'); ?></button>
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
