<?php
require_once(__DIR__.'/../common/initialization.inc.php');
$header_title=lang('admin.news_edit.header');
$page="news";
include(__DIR__.'/header.inc.php');

if (!empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
	if (!empty($_POST['action'])) {
		$data=array();
		if (!empty($_POST['id'])) {
			$data['id']=escape($_POST['id']);
		} else if ($_POST['action']=='edit') {
			crash(lang('admin.error.id_missing'));
		}
		if (!empty($_POST['fansub_id']) && is_numeric($_POST['fansub_id'])) {
			$data['fansub_id']=escape($_POST['fansub_id']);
		} else {
			$data['fansub_id']='NULL';
		}
		if (!empty($_POST['title'])) {
			$data['title']=escape($_POST['title']);
		} else {
			crash(lang('admin.error.title_missing'));
		}
		if (!empty($_POST['url'])) {
			$data['url']="'".escape($_POST['url'])."'";
		} else {
			$data['url']="NULL";
		}
		if (!empty($_POST['contents'])) {
			$data['contents']=escape($_POST['contents']);
		} else {
			crash(lang('admin.error.contents_missing'));
		}
		if (!empty($_POST['date'])) {
			$data['date']=date('Y-m-d H:i:s', strtotime($_POST['date']));
		} else {
			crash(lang('admin.error.date_missing'));
		}
		
		if ($_POST['action']=='edit') {
			$toupdate_result = query("SELECT n.*, f.name fansub_name, f.slug fansub_slug FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE MD5(CONCAT(n.title, n.date))='".$data['id']."'");
			if (mysqli_num_rows($toupdate_result)>1) {
				crash(lang('admin.error.news_edit_same_hash_error'));
			} else if (mysqli_num_rows($toupdate_result)==1) {
				$toupdate_row = mysqli_fetch_assoc($toupdate_result);
				if (empty($toupdate_row['fansub_name'])) {
					$toupdate_row['fansub_slug']=slugify(CURRENT_SITE_NAME);
					$toupdate_row['fansub_name']=CURRENT_SITE_NAME;
				}
				log_action("update-news", "News «".$toupdate_row['title']."» for fansub «".$toupdate_row['fansub_name']."» updated");
				if (!empty($_FILES['image'])) {
					move_uploaded_file($_FILES['image']["tmp_name"], STATIC_DIRECTORY.'/images/news/'.$toupdate_row['fansub_slug'].'/'.md5($data['title'].$data['date']));
					$data['image'] = "'".md5($data['title'].$data['date'])."'";
				} else if (!empty($toupdate_row['image'])) {
					$data['image'] = "'".$toupdate_row['image']."'";
				} else {
					$data['image'] = 'NULL';
				}
				query("UPDATE news SET fansub_id=".$data['fansub_id'].",url=".$data['url'].",contents='".$data['contents']."',title='".$data['title']."',date='".$data['date']."',image=".$data['image']." WHERE MD5(CONCAT(title, date))='".$data['id']."'");
			}
		}
		else {
			if ($data['fansub_id']!='NULL') {
				$fansub_result = query("SELECT f.name fansub_name, f.slug fansub_slug FROM fansub f WHERE f.id=".$data['fansub_id']);
				$fansub_row = mysqli_fetch_assoc($fansub_result);
			} else {
				$fansub_row = array();
				$fansub_row['fansub_slug']=slugify(CURRENT_SITE_NAME);
				$fansub_row['fansub_name']=CURRENT_SITE_NAME;
			}
			if (!empty($_FILES['image'])) {
				move_uploaded_file($_FILES['image']["tmp_name"], STATIC_DIRECTORY.'/images/news/'.$fansub_row['fansub_slug'].'/'.md5($data['title'].$data['date']));
				$data['image'] = "'".md5($data['title'].$data['date'])."'";
			} else {
				$data['image'] = 'NULL';
			}
			log_action("create-news", "News «".$_POST['title']."» for «".$fansub_row['fansub_name']."» created");
			query("INSERT INTO news (fansub_id,news_fetcher_id,title,contents,original_contents,date,url,image) VALUES (".$data['fansub_id'].",NULL,'".$data['title']."','".$data['contents']."','".$data['contents']."','".$data['date']."',".$data['url'].",".$data['image'].")");
		}

		$_SESSION['message']=lang('admin.generic.data_saved');

		header("Location: news_list.php");
		die();
	}

	if (isset($_GET['id'])) {
		$result = query("SELECT MD5(CONCAT(n.title, n.date)) id, n.*, f.slug fansub_slug FROM news n LEFT JOIN fansub f ON n.fansub_id=f.id WHERE MD5(CONCAT(n.title, n.date))='".escape($_GET['id'])."'");
		$row = mysqli_fetch_assoc($result) or crash(lang('admin.error.news_not_found'));
		mysqli_free_result($result);
		
		if (empty($row['fansub_slug'])) {
			$row['fansub_slug']=slugify(CURRENT_SITE_NAME);
		}
	} else {
		$row = array();
		$row['id'] = '';
		$row['title'] = '';
		$row['contents'] = '';
		$row['url'] = '';
		$row['fansub_id'] = '';
	}
?>
		<div class="container d-flex justify-content-center p-4">
			<div class="card w-100">
			<article class="card-body">
				<h4 class="card-title text-center mb-4 mt-1"><?php echo !empty($row['id']) ? lang('admin.news_edit.edit_title') : lang('admin.news_edit.create_title'); ?></h4>
				<hr>
				<form method="post" action="news_edit.php" enctype="multipart/form-data" onsubmit="return checkNewsPost()">
<?php
	if (empty($row['id'])) {
?>
					<p class="alert alert-warning"><span class="fa fa-exclamation-triangle me-2"></span><?php echo lang('admin.news_edit.warning_about_adding'); ?></p>
<?php
	}
?>
					<div class="mb-3">
						<label for="form-fansub_id"><?php echo lang('admin.news_edit.fansub'); ?></label> <?php print_helper_box(lang('admin.news_edit.fansub'), lang('admin.news_edit.fansub.help')); ?>
						<select name="fansub_id" class="form-select" id="form-fansub_id"<?php echo isset($_GET['id']) ? ' disabled' : ''; ?>>
<?php
	if ($_SESSION['admin_level']>=3) {
?>
							<option value=""><?php echo sprintf(lang('admin.news_edit.fansub.no_fansub'), MAIN_SITE_NAME); ?></option>
<?php
	}
	if (!empty($_SESSION['fansub_id']) && is_numeric($_SESSION['fansub_id'])) {
		$where = ' WHERE f.id='.$_SESSION['fansub_id'];
	} else {
		$where = '';
	}
	$result = query("SELECT f.* FROM fansub f$where ORDER BY f.name ASC");
	while ($frow = mysqli_fetch_assoc($result)) {
?>
							<option value="<?php echo $frow['id']; ?>"<?php echo $row['fansub_id']==$frow['id'] ? " selected" : ""; ?>><?php echo htmlspecialchars($frow['name']); ?></option>
<?php
	}
	mysqli_free_result($result);
?>
						</select>
						<?php echo isset($_GET['id']) ? '<input type="hidden" name="fansub_id" value="'.$row['fansub_id'].'">' : ''; ?>
						<input type="hidden" name="id" value="<?php echo $row['id']; ?>">
					</div>
					<div class="mb-3">
						<label for="form-title" class="mandatory"><?php echo lang('admin.news_edit.title'); ?></label> <?php print_helper_box(lang('admin.news_edit.title'), lang('admin.news_edit.title.help')); ?>
						<input class="form-control" name="title" id="form-title" required value="<?php echo htmlspecialchars($row['title']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-contents"><?php echo lang('admin.news_edit.contents'); ?><span class="mandatory"></span> <?php print_helper_box(lang('admin.news_edit.contents'), lang('admin.news_edit.contents.help')); ?> <small class="text-muted">(compte, en format HTML!)</small></label>
						<textarea class="form-control" name="contents" id="form-contents" required style="height: 150px;"><?php echo htmlspecialchars($row['contents']); ?></textarea>
					</div>
					<div class="mb-3">
						<label for="form-url"><?php echo lang('admin.news_edit.url'); ?></label> <?php print_helper_box(lang('admin.news_edit.url'), sprintf(lang('admin.news_edit.url.help'), MAIN_SITE_NAME)); ?>
						<input class="form-control" name="url" id="form-url" value="<?php echo htmlspecialchars($row['url']); ?>">
					</div>
					<div class="mb-3">
						<label for="form-date" class="mandatory"><?php echo lang('admin.news_edit.date'); ?></label> <?php print_helper_box(lang('admin.news_edit.date'), lang('admin.news_edit.date.help')); ?>
						<input class="form-control" name="date" type="datetime-local" id="form-date" required step="1" value="<?php echo !empty($row['date']) ? date('Y-m-d\TH:i:s', strtotime($row['date'])) : date('Y-m-d\TH:i:s'); ?>">
					</div>
					<div class="row">
						<div class="col-sm-3">
							<div class="mb-3">
								<label><?php echo lang('admin.news_edit.image'); ?></label> <?php print_helper_box(lang('admin.news_edit.image'), lang('admin.news_edit.image.help')); ?><br><small class="text-muted"><?php echo lang('admin.news_edit.image.requirements'); ?></small><br>
<?php
	$file_exists = !empty($row['id']) && file_exists(STATIC_DIRECTORY.'/images/news/'.$row['fansub_slug'].'/'.$row['image']);
?>
								<label for="form-image" class="btn btn-sm btn-<?php echo $file_exists ? 'warning' : 'primary' ; ?>"><span class="fa fa-upload pe-2"></span><?php echo $file_exists ? lang('admin.common.change_image') : lang('admin.common.upload_image') ; ?></label>
								<input class="form-control d-none" name="image" type="file" accept="image/jpeg,image/png" id="form-image" onchange="checkImageUpload(this, -1, 'image/*', 1, 1, 4096, 4096, 'form-image-preview', 'form-image-preview-link');">
							</div>
						</div>
						<div class="col-sm-4">
							<div class="mb-3">
								<a id="form-image-preview-link"<?php echo $file_exists ? ' href="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'" data-original="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'"' : ''; ?> target="_blank">
									<img id="form-image-preview" style="width: 140px; height: 90px; object-fit: contain; background-color: black; display:inline-block; text-indent: -10000px;"<?php echo $file_exists ? ' src="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'" data-original="'.STATIC_URL.'/images/news/'.$row['fansub_slug'].'/'.$row['image'].'"' : ''; ?> alt="">
								</a>
							</div>
						</div>
					</div>
					<div class="mb-3 text-center pt-2">
						<button type="submit" name="action" value="<?php echo !empty($row['id']) ? "edit" : "add"; ?>" class="btn btn-primary fw-bold"><span class="fa fa-check pe-2"></span><?php echo !empty($row['id']) ? lang('admin.generic.save_changes') : lang('admin.news_edit.create_button'); ?></button>
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
