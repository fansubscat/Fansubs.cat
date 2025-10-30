<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.link_edit.header');
$page="other";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=3) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id']) && is_numeric($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.id_missing'));
		}
		if (!empty($_POST['name'])) {
			$data['name']=escape($_POST['name']);
		} else {
			crash(lang('admin.error.name_missing'));
		}
		if (!empty($_POST['description'])) {
			$data['description']=escape($_POST['description']);
		} else {
			crash(lang('admin.error.description_missing'));
		}
		if (!empty($_POST['url'])) {
			$data['url']=escape($_POST['url']);
		} else {
			crash(lang('admin.error.url_missing'));
		}
		if (!empty($_POST['category'])) {
			$data['category']=escape($_POST['category']);
		} else {
			crash(lang('admin.error.category_missing'));
		}
		
		if ($_POST['action']=='edit') {
			$old_result = query("SELECT * FROM community WHERE id=".$data['id']);
			$old_row = mysqli_fetch_assoc($old_result);
			if ($old_row['updated']!=$_POST['last_update']) {
				crash(lang('admin.error.link_edit_concurrency_error'));
			}
			
			log_action("update-link", "Link «".$_POST['name']."» (link id: ".$data['id'].") updated");
			query("UPDATE community SET name='".$data['name']."',url='".$data['url']."',category='".$data['category']."',description='".$data['description']."',updated=CURRENT_TIMESTAMP,updated_by='".escape($_SESSION['username'])."' WHERE id=".$data['id']);

			if (!empty($_FILES['logo'])) {
				move_uploaded_file($_FILES['logo']["tmp_name"], STATIC_DIRECTORY.'/images/communities/'.$data['id'].'.png');
			}
		}
		else {
			log_action("create-link", "Link «".$_POST['name']."» created");
			query("INSERT INTO community (name,url,category,description,created,created_by,updated,updated_by) VALUES ('".$data['name']."','".$data['url']."','".$data['category']."','".$data['description']."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."',CURRENT_TIMESTAMP,'".escape($_SESSION['username'])."')");

			if (!empty($_FILES['logo'])) {
				move_uploaded_file($_FILES['logo']["tmp_name"], STATIC_DIRECTORY.'/images/communities/'.mysqli_insert_id($db_connection).'.png');
			}
		}

		$_SESSION['message']=lang('admin.generic.data_saved');

		header("Location: link_list.php");
		die();
	}

	if (isset($_GET['id']) && is_numeric($_GET['id'])) {
		$result = query("SELECT c.* FROM community c WHERE id=".escape($_GET['id']));
		$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.link_not_found'));
		mysqli_free_result($result);
	} else {
		$row = array();
		$row['id']='';
		$row['name']='';
		$row['updated']='';
		$row['url']='';
		$row['description']='';
		$row['category']='';
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? lang('admin.link_edit.edit_title') : lang('admin.link_edit.create_title'); ?></h4>
				<hr>
				<form method="post" action="link_edit.php" enctype="multipart/form-data" onsubmit="return checkCommunity()">
					<div class="mb-3">
						<label for="form-name" class="mandatory"><?php echo lang('admin.link_edit.name'); ?></label> <?php print_helper_box(lang('admin.link_edit.name'), lang('admin.link_edit.name.help')); ?>
						<input class="form-control" name="name" id="form-name" required maxlength="200" value="<?php echo htmlspecialchars($row['name']); ?>">
						<input type="hidden" id="form-id" name="id" value="<?php echo $row['id']; ?>">
						<input type="hidden" name="last_update" value="<?php echo $row['updated']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-url" class="mandatory"><?php echo lang('admin.link_edit.url'); ?></label> <?php print_helper_box(lang('admin.link_edit.url'), lang('admin.link_edit.url.help')); ?>
						<input class="form-control" type="url" name="url" id="form-url" maxlength="200" value="<?php echo htmlspecialchars($row['url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-description" class="mandatory"><?php echo lang('admin.link_edit.description'); ?></label> <?php print_helper_box(lang('admin.link_edit.description'), lang('admin.link_edit.description.help')); ?>
						<input class="form-control" name="description" id="form-description" maxlength="200" value="<?php echo htmlspecialchars($row['description']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-category" class="mandatory"><?php echo lang('admin.link_edit.category'); ?></label> <?php print_helper_box(lang('admin.link_edit.category'), lang('admin.link_edit.category.help')); ?>
						<select class="form-select" name="category" id="form-category" required>
							<option value=""><?php echo lang('admin.link_edit.category.select'); ?></option>
							<option value="featured"<?php echo $row['category']=='featured' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.featured'); ?></option>
							<option value="blogs"<?php echo $row['category']=='blogs' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.blogs'); ?></option>
							<option value="catalogs"<?php echo $row['category']=='catalogs' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.catalogs'); ?></option>
							<option value="art"<?php echo $row['category']=='art' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.art'); ?></option>
							<option value="forums"<?php echo $row['category']=='forums' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.forums'); ?></option>
							<option value="culture"<?php echo $row['category']=='culture' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.culture'); ?></option>
							<option value="creators"<?php echo $row['category']=='creators' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.creators'); ?></option>
							<option value="dubbing"<?php echo $row['category']=='dubbing' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.dubbing'); ?></option>
							<option value="music"<?php echo $row['category']=='music' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.music'); ?></option>
							<option value="nostalgia"<?php echo $row['category']=='nostalgia' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.nostalgia'); ?></option>
							<option value="podcasts"<?php echo $row['category']=='podcasts' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.podcasts'); ?></option>
							<option value="preservation"<?php echo $row['category']=='preservation' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.preservation'); ?></option>
							<option value="subtitles"<?php echo $row['category']=='subtitles' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.subtitles'); ?></option>
							<option value="others"<?php echo $row['category']=='others' ? " selected" : ""; ?>><?php echo lang('admin.generic.link_category.others'); ?></option>
						</select>
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="mb-3">
								<label><?php echo lang('admin.link_edit.logo'); ?><?php echo empty($row['id']) ? '<span class="mandatory"></span>' : ''; ?> <?php print_helper_box(lang('admin.link_edit.logo'), lang('admin.link_edit.logo.help')); ?> <small class="text-muted"><?php echo lang('admin.link_edit.logo.requirements'); ?></small></label><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/communities/'.$row['id'].'.png');
?>
								<label for="form-logo" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? lang('admin.common.change_image') : lang('admin.common.upload_image') ; ?></label>
								<input class="form-control d-none" name="logo" type="file" accept="image/png" id="form-logo" onchange="checkImageUpload(this, -1, 'image/png', 160, 160, 160, 160, 'form-logo-preview', 'form-logo-preview-link');">
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
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? lang('admin.generic.save_changes') : lang('admin.link_edit.create_button'); ?></button>
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
